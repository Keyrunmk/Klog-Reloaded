<?php

namespace App\Services;

use App\Contracts\CategoryContract;
use App\Repositories\CategoryRepository;
use App\Validations\CategoryValidation;
use Illuminate\Http\Request;

class CategoryService
{
    protected CategoryRepository $categoryRepository;
    protected CategoryValidation $categoryValidation;

    public function __construct(CategoryContract $categoryRepository, CategoryValidation $categoryValidation)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryValidation = $categoryValidation;
    }

    public function show(int $category_id): mixed
    {
        return $this->categoryRepository->findOneOrFail($category_id);
    }

    public function create(Request $request): mixed
    {
        $attributes = $this->categoryValidation->validate($request);

        return $this->categoryRepository->create($attributes);
    }

    public function update(int $category_id, Request $request): mixed
    {
        $attributes = $this->categoryValidation->validate($request);

        $this->categoryRepository->update($attributes, $category_id);
        return $this->categoryRepository->findOneOrFail($category_id);
    }

    public function delete(int $category_id): mixed
    {
        return $this->categoryRepository->delete($category_id);
    }
}
