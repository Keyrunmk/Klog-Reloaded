<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;

trait ImageTrait
{
    public function getImagePath(): string
    {
        $imagePath = request("image")->store("uploads", "public");
        $image = Image::make(public_path("storage/$imagePath"))->fit(2000, 2000);
        $image->save();

        return $imagePath;
    }
}