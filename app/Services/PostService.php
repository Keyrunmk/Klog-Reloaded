<?php

namespace App\Services;

use App\Contracts\LocationContract;
use App\Contracts\PostContract;
use App\Exceptions\ForbiddenException;
use App\facades\UserLocation;
use App\Models\Post;
use App\Repositories\LocationRepository;
use App\Repositories\PostRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostService
{
    use ImageTrait;

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
            unset($attributes["image"]);
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
        if ($attributes["image"] ?? false) {
            unset($attributes["image"]);
            $imagePath = $this->getImagePath();
        }

        $this->postRepository->update($attributes, $post_id);

        if ($imagePath ?? false) {
            $post = $this->postRepository->updateImage($post, $imagePath);
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

    public function checkForPermission(Post $post): void
    {
        if (Gate::denies("update-post", $post)) {
            throw new ForbiddenException("You do not own this post");
        }
    }
}
