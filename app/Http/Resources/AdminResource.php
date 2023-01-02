<?php

namespace App\Http\Resources;

use App\Models\Admin;

class AdminResource extends BaseResource
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
            "name" => $this->first_name . " " . $this->last_name,
            "email" => $this->email,
        ];
    }
}
