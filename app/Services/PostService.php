<?php

namespace App\Services;

use App\Contracts\PostContract;
use App\Exceptions\ForbiddenException;
use App\facades\UserLocation;
use App\Models\Location;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Validations\ValidatePostRequest;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class PostService
{
    protected PostRepository $postRepository;
    protected ValidatePostRequest $postValidation;

    public function __construct(PostContract $postRepository, ValidatePostRequest $postValidation)
    {
        $this->postRepository = $postRepository;
        $this->postValidation = $postValidation;
    }

    public function index(): Paginator
    {
        try {
            return $this->postRepository->paginate(number: 15);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function find(int $post_id): Post
    {
        return $this->postRepository->findOneOrFail($post_id);
    }

    public function store(Request $request): Post
    {
        $attributes = $this->postValidation->validate($request);
        $location = UserLocation::getCountryName() ?? "world";
        $location_id = Location::where("country_name", $location)->value("id") ?? 1;
        $attributes = array_merge($attributes, [
            "user_id" => Auth::user()->id,
            "location_id" => $location_id,
        ]);

        if ($attributes["image"] ?? false) {
            $imagePath = $this->getImagePath();
        }

        $post = $this->postRepository->create($attributes);

        if ($imagePath ?? false) {
            $this->postRepository->saveImage($post, $imagePath);
        }

        $this->postRepository->savePostLocation($post, $location);
        // Cache::forget("posts");

        return $post;
    }

    public function update(int $post_id, Request $request): Post
    {
        $attributes = $this->postValidation->validate($request);

        $post = $this->postRepository->findOneOrFail($post_id);
        $this->checkForPermission($post);

        try {
            DB::beginTransaction();
            $this->postRepository->update($attributes, $post_id);

            if ($attributes["image"] ?? false) {
                $post = $this->updateImage($post);
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        // Cache::forget("posts");

        return $post;
    }

    public function delete(int $post_id): bool
    {
        $post = $this->postRepository->findOneOrFail($post_id);
        $this->checkForPermission($post);

        return $this->postRepository->delete($post_id);
    }

    public function report(Post $post, Request $request): void
    {
        $attributes = $request->validate([
            "case" => ["required", "string", "max:500"],
        ]);

        $attributes = array_merge($attributes, [
            "post_id" => $post->id,
            "user_id" => auth()->user()->id,
        ]);

        $this->postRepository->reportPost($post, $attributes);
    }

    public function createComment(int $post_id, Request $request): Post
    {
        $attributes = $request->validate([
            "body" => ["required", "string", "max:255"],
        ]);

        $post = $this->postRepository->findWithComments($post_id);

        $post->comments()->create([
            "user_id" => Auth::user()->id,
            "body" => $attributes["body"],
        ]);

        return $post;
    }

    public function destroyComment(int $post_id, int $comment_id): bool
    {
        $post = $this->postRepository->findWithComment($post_id, $comment_id);
        $comment = $post->comments[0];

        if (auth()->user()->can("delete", $post) || auth()->user()->can("delete", $comment)) {
            return $comment->delete();
        }

        throw new ForbiddenException();
    }

    public function tagPost(int $post_id): Void
    {
        $attributes = request()->validate([
            "name" => ["required", "string"],
        ]);

        $post = $this->postRepository->findOneOrFail($post_id);
        $this->postRepository->saveTag($post, $attributes);
    }

    public function updateImage(Post $post): Post
    {
        $imagePath = $this->getImagePath();
        $this->postRepository->updateImage($post, $imagePath);
        // Cache::forget("posts");

        return $post;
    }

    public function getImagePath(): string
    {
        $imagePath = request("image")->store("uploads", "public");
        $image = Image::make(public_path("storage/$imagePath"))->fit(2000, 2000);
        $image->save();

        return $imagePath;
    }

    public function checkForPermission(Post $post): void
    {
        if (auth()->user()->cannot("update", $post)) {
            throw new ForbiddenException("You do not own this post");
        }
    }
}
