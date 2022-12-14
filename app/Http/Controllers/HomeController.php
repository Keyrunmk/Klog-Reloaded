<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostsCollection;
use App\Services\HomeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), (int) $exception->getCode());
        }

        return $this->successResponse(message: "Home view", data: new PostsCollection($posts));
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
