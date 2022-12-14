<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostsCollection extends ResourceCollection
{
    protected $posts;

    public function __construct(Collection $posts)
    {
        $this->posts = $posts;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->posts->map(function($post) {
            return [
                "id" => $post->id,
                "title" => $post->title,
                "body" => $post->body,
            ];
        })->toArray();
    }
}
