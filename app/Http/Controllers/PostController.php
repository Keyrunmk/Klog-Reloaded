<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\PostCommentRequest;
use App\Http\Requests\PostReportRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostTagRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsCollection;
use App\Services\PostService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
            return $this->successResponse(message: "Posts fetched", data: new PostsCollection($posts));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function show(int $post): JsonResponse
    {
        try {
            $post = $this->postService->find($post);
            return $this->successResponse(message: "Post fetched", data: $this->resource($post));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function store(PostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->store($request->all());
            return $this->successResponse(message: "Post stored", data: $this->resource($post));
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function update(int $post_id, PostRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $post = $this->postService->update($post_id, $request->all());
            DB::commit();
            return $this->successResponse(message: "post id: $post_id updated", data: $this->resource($post));
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return $this->errorResponse("No post with post id: $post_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function destroy(int $post_id): JsonResponse
    {
        try {
            $this->postService->delete($post_id);
            return $this->successResponse("Post id: $post_id deleted successfully");
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No post with id: $post_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function report(int $post_id, PostReportRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->find($post_id);
            $this->postService->report($post, $request->all());
            return $this->successResponse("Report submitted successfully");
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function comment(int $post_id, PostCommentRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createComment($post_id, $request->all());
            return $this->successResponse(message: "Comments for post id: $post->id", data: $post->comments);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function deleteComment(int $post_id, int $comment_id): JsonResponse
    {
        try {
            $this->postService->destroyComment($post_id, $comment_id);
            return $this->successResponse("Comment id: $comment_id deleted.");
        } catch (ForbiddenException $exception) {
            return $this->errorResponse("You cannot delete this comment", (int) $exception->getCode());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function tagPost(int $post_id, PostTagRequest $request): JsonResponse
    {
        try {
            $this->postService->tagPost($post_id, $request->all());
            return $this->successResponse("Post id: $post_id tagged");
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
    }

    public function resource($data):JsonResource
    {
        return new PostResource($data);
    }
}
