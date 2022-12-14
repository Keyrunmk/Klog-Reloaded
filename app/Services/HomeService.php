<?php

namespace App\Services;

use App\Contracts\PostContract;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;

class HomeService
{
    public PostRepository $postRepository;

    public function __construct(PostContract $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPosts(): Collection
    {
        if (auth()->user ?? false) {
            $location = auth()->user()->location->first();
            $authPosts = $location->posts()->get();

            $userTags = auth()->user()->tags()->get();

            foreach ($userTags as $tag) {
                $authPosts->add($tag->posts);
            }
        }

        $posts = $this->postRepository->all(sortBy:"desc");

        if ($authPosts ?? false) {
            $posts->add($authPosts);
        }

        return $posts;
    }
}