<?php

namespace App\Http\Resources;

use App\Models\User;

class UserResource extends BaseResource
{
    protected User $user;
    protected string $token;
    protected string $message;

    public function __construct(string $message = "", User $user, string $token = "")
    {
        $this->message = $message;
        $this->user = $user;
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
        $response = [
            "status" => $this->user->status,
            "id" => $this->user->id,
            "name" => $this->user->first_name . " " . $this->user->last_name,
            "username" => $this->user->username,
        ];

        if (!empty($this->message)) {
            $response = array_merge($response, [
                "message" => $this->message,
            ]);
        }

        if (!empty($this->token)) {
            $response = array_merge($response, [
                "token" => $this->token,
            ]);
        }

        return $response;
    }
}
