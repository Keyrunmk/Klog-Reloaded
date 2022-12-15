<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->map(function ($post) {
            return [
                "id" => $post->id,
                "title" => $post->title,
                "body" => $post->body,
                "comments" => new CommentsCollection($post->comments),
                "tags" => new TagsCollection($post->tags),
            ];
        })->toArray();
    }
}
