<?php

namespace App\Http\Controllers;

use App\Contracts\TagContract;
use App\Models\Post;
use App\Repositories\TagRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class TagController extends BaseController
{
    // todo
    protected TagRepository $tagRepository;

    public function __construct(TagContract $tagRepository)
    {
        
    }

    public function store(Post $post): JsonResponse
    {
        
    }
}
