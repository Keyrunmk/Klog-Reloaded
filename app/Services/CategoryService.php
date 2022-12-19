<?php

namespace App\Services;

use App\Contracts\CategoryContract;
use App\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryContract $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): mixed
    {
        return $this->categoryRepository->all();
    }

    public function show(int $category_id): mixed
    {
        return $this->categoryRepository->findOneOrFail($category_id);
    }

    public function create(array $attributes): mixed
    {
        return $this->categoryRepository->create($attributes);
    }

    public function update(int $category_id, array $attributes): mixed
    {
        $this->categoryRepository->update($attributes, $category_id);
        return $this->categoryRepository->findOneOrFail($category_id);
    }

    public function delete(int $category_id): mixed
    {
        return $this->categoryRepository->delete($category_id);
    }
}
