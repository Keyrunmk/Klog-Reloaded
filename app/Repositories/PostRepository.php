<?php

namespace App\Repositories;

use App\Contracts\PostContract;
use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;


class PostRepository extends BaseRepository implements PostContract
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function allPosts(): Paginator
    {
        return $this->model->with(["image", "tags", "comments"])->paginate(15);
    }

    public function saveImage(Post $post, string $imagePath): void
    {
        $post->image()->create(["path" => $imagePath]);
    }

    public function savePostLocation(Post $post, string $location): void
    {
        $post->location()->create(["country_name" => $location]);
    }

    public function updateImage(Post $post, string $imagePath): void
    {
        $post->image()->update(["path" => $imagePath]);
    }

    public function reportPost(Post $post, array $attributes): void
    {
        $post->postReports()->create($attributes);
    }

    public function findWithComments(int $post_id): Post
    {
        return $this->findOneOrFail($post_id)->with("comments")->first();
    }

    public function findWithComment(int $post_id, int $comment_id): Post
    {
        return $this->findOneOrFail($post_id)->with(["comments" => function ($query) use ($comment_id) {
            $query->where("id", $comment_id)->first();
        }])->first();
    }

    public function saveTag(Post $post, array $attributes): void
    {
        $post->tags()->create($attributes);
    }
}
