<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends BaseController
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): JsonResponse
    {
        try {
            $posts = $this->postService->index();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Posts fetched", data: $posts);
    }

    public function show(int $post): JsonResponse
    {
        try {
            $post = $this->postService->find($post);
            return $this->successResponse(message: "Post fetched", data: new PostResource($post));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $post = $this->postService->store($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Post stored", data: new PostResource($post));
    }

    public function update(int $post_id, Request $request): JsonResponse
    {
        try {
            $post = $this->postService->update($post_id, $request);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No post with post id: $post_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "post id: $post_id updated", data: new PostResource($post));
    }

    public function destroy(int $post_id): JsonResponse
    {
        try {
            $this->postService->delete($post_id);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No post with id: $post_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Post id: $post_id deleted successfully");
    }

    public function report(int $post_id, Request $request): JsonResponse
    {
        try {
            $post = $this->postService->find($post_id);
            $this->postService->report($post, $request);
            return $this->successResponse("Report submitted successfully");
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->errorResponse("Failed to submit report");
    }

    public function comment(int $post_id, Request $request): JsonResponse
    {
        try {
            $post = $this->postService->createComment($post_id, $request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Comments for post id: $post->id", data: $post->comments);
    }

    public function deleteComment(int $post_id, int $comment_id): JsonResponse
    {
        try {
            $this->postService->destroyComment($post_id, $comment_id);
        } catch(ForbiddenException $exception) {
            return $this->errorResponse("You cannot delete this comment", (int) $exception->getCode());
        }catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Comment id: $comment_id deleted.");
    }

    public function tagPost(int $post_id): JsonResponse
    {
        try {
            $this->postService->tagPost($post_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Post id: $post_id tagged");
    }
}
