<?php

namespace App\Services;

use App\Contracts\PostContract;
use App\Exceptions\ForbiddenException;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Traits\ImageTrait;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostService extends BaseService
{
    use ImageTrait;

    protected PostRepository $postRepository;

    public function __construct(PostContract $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(): Paginator
    {
        return $this->postRepository->allPosts();
    }

    public function find(int $post_id): Post
    {
        return $this->postRepository->findOneOrFail($post_id);
    }

    public function store(array $attributes): Post
    {
        $location = $this->getLocation();
        $attributes = array_merge($attributes, [
            "user_id" => Auth::user()->id,
            "location_id" => $location->id,
        ]);

        if ($attributes["image"] ?? false) {
            unset($attributes["image"]);
            $imagePath = $this->getImagePath();
        }

        $post = $this->postRepository->create($attributes);
        if ($imagePath ?? false) {
            $this->postRepository->saveImage($post, $imagePath);
        }

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
            $this->deleteImage($post);
            $this->postRepository->saveImage($post, $imagePath);
        }
        // Cache::forget("posts");

        return $post->fresh();
    }

    public function delete(int $post_id): bool
    {
        $post = $this->postRepository->findOneOrFail($post_id);
        $this->checkForPermission($post);

        $this->deleteImage($post);

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
