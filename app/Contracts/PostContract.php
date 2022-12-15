<?php

namespace App\Contracts;

use App\Models\Post;

interface PostContract
{
    public function saveImage(Post $post, string $imagePath): void;

    public function savePostLocation(Post $post, string $location): void;

    public function updateImage(Post $post, string $imagePath): void;

    public function reportPost(Post $post, array $attributes): void;
}
