<?php

namespace App\Services;

use App\Contracts\LocationContract;
use App\Contracts\PostContract;
use App\Exceptions\ForbiddenException;
use App\facades\UserLocation;
use App\Models\Location;
use App\Models\Post;
use App\Repositories\LocationRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;

class PostService
{
    protected PostRepository $postRepository;
    protected LocationRepository $locationRepository;

    public function __construct(PostContract $postRepository, LocationContract $locationRepository)
    {
        $this->postRepository = $postRepository;
        $this->locationRepository = $locationRepository;
    }

    public function index(): Collection
    {
        $posts =  $this->postRepository->allPosts();
        return collect($posts->all());
    }

    public function find(int $post_id): Post
    {
        return $this->postRepository->findOneOrFail($post_id);
    }

    public function store(array $attributes): Post
    {
        $location = UserLocation::getCountryName();
        $location_id = $this->locationRepository->getLocationId($location);
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

    public function update(int $post_id, array $attributes): Post
    {
        $post = $this->postRepository->findOneOrFail($post_id);
        $this->checkForPermission($post);
        $this->postRepository->update($attributes, $post_id);

        if ($attributes["image"] ?? false) {
            $post = $this->updateImage($post);
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

    public function report(Post $post, array $attributes): void
    {
        $attributes = array_merge($attributes, [
            "post_id" => $post->id,
            "user_id" => auth()->user()->id,
        ]);

        $this->postRepository->reportPost($post, $attributes);
    }

    public function createComment(int $post_id, array $attributes): Post
    {
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
        $comment = $post->comments->first();

        Gate::authorize("delete-post-comment", [$post, $comment]);

        return $comment->delete();
    }

    public function tagPost(int $post_id, array $attributes): Void
    {
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
