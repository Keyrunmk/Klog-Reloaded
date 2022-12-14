<?php

namespace App\Http\Resources;

use App\Models\Admin;

class AdminResource extends BaseResource
{
    protected Admin $admin;
    protected string $token;

    public function __construct(Admin $admin, string $token)
    {
        $this->admin = $admin;
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->admin->id,
            "name" => $this->admin->first_name . " " . $this->admin->last_name,
            "token" => $this->token,
        ];
    }
}
