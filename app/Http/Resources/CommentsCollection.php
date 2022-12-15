<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->map(function($comment){
            return [
                "id" => $comment->id,
                "user_id" => $comment->user_id,
                "body" => $comment->body,
                "created_at" => $comment->created_at,
            ];
        });
    }
}
