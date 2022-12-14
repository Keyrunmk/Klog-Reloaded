<?php

namespace App\Http\Resources;

use App\Models\User;
class LoginResource extends BaseResource
{
    public User $model;
    public string $token;

    public function __construct(User $model, string $token = "")
    {
        $this->model = $model;
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
            "status" => "success",
            "model" => get_class($this->model),
            "username" => $this->model->username,
            "email" => $this->model->email,
            "authorization" => [
                "token" => $this->token,
                "type" => "bearer"
            ],
        ];
    }
}
