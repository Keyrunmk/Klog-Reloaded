<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware("adminRole:createCategory")->except("show");
    }

    public function show(int $category_id): JsonResponse
    {
        try {
            $category = $this->categoryService->show($category_id);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No category with id: $category_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category id: $category_id", data: new CategoryResource($category));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $category = $this->categoryService->create($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category stored", data: new CategoryResource($category));
    }

    public function update(int $category_id, Request $request): JsonResponse
    {
        try {
            $category = $this->categoryService->update($category_id, $request);
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse("No category with id: $category_id", Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(message: "Category id: $category_id updated", data: new CategoryResource($category));
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
}
