<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsCollection;
use App\Services\HomeService;
use Exception;
use Illuminate\Http\JsonResponse;

class HomeController extends BaseController
{
    public HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index(): JsonResponse
    {
        try {
            $posts = $this->homeService->getPosts();
            return $this->successResponse(message: "Home view", data: new PostsCollection($posts));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    // public function store(User $user)
    // {
    //     $attributes = request()->validate([
    //         "name" => ["required", "string"],
    //     ]);

    //     $tag = $user->tags()->create($attributes);

    //     return response()->json([
    //         "status" => "success",
    //         "tag_id" => $tag->id,
    //         "tag" => $tag->name,
    //     ]);
    // }
}
