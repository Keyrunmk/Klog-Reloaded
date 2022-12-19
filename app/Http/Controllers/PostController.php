<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCommentRequest;
use App\Http\Requests\PostReportRequest;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostTagRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
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
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Posts fetched", data: $this->resource($posts));
    }

    public function show(int $post): JsonResponse
    {
        try {
            $post = $this->postService->find($post);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Post fetched", data: $this->resource($post));
    }

    public function store(PostRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $post = $this->postService->store($request->all());
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse(message: "Post stored", data: $this->resource($post));
    }

    public function update(int $post_id, PostRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $post = $this->postService->update($post_id, $request->all());
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse(message: "post id: $post_id updated", data: $this->resource($post));
    }

    public function destroy(int $post_id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->postService->delete($post_id);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse("Post id: $post_id deleted successfully");
    }

    public function report(int $post_id, PostReportRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->find($post_id);
            $this->postService->report($post, $request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Report id: $post_id submitted successfully");
    }

    public function comment(int $post_id, PostCommentRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createComment($post_id, $request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Comments for post id: $post->id", data: $post->comments);
    }

    public function deleteComment(int $post_id, int $comment_id): JsonResponse
    {
        try {
            $this->postService->destroyComment($post_id, $comment_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Comment id: $comment_id deleted.");
    }

    public function tagPost(int $post_id, PostTagRequest $request): JsonResponse
    {
        try {
            $this->postService->tagPost($post_id, $request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Post id: $post_id tagged");
    }

    public function resource($data): JsonResource
    {
        if ($data instanceof Collection || $data instanceof Paginator) {
            return PostResource::collection($data);
        }

        return new PostResource($data);
    }
}
