<?php

namespace App\Http\Resources;

class PostResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "body" => $this->body,
            "image" => new ImageResource($this->image),
            "comments" => CommentResource::collection($this->comments),
            "tags" => TagResource::collection($this->tags),
        ];
    }
}
