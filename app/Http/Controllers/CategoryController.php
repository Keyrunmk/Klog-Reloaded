<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware("adminRole:createCategory")->except(["show", "index"]);
    }

    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->index();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "All categories", data: $this->resource($categories));
    }

    public function show(int $category_id): JsonResponse
    {
        try {
            $category = $this->categoryService->show($category_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category id: $category_id", data: $this->resource($category));
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->create($request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category stored", data: $this->resource($category));
    }

    public function update(int $category_id, CategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->update($category_id, $request->all());
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category id: $category_id updated", data: $this->resource($category));
    }

    public function destroy(int $category_id): JsonResponse
    {
        try {
            $this->categoryService->delete($category_id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse("Category deleted");
    }

    public function resource($data): JsonResource
    {
        if ($data instanceof Collection) {
            return CategoryResource::collection($data);
        }
        return new CategoryResource($data);
    }
}
