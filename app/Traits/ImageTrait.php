<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait ImageTrait
{
    public function getImagePath(): string
    {
        $imagePath = request("image")->store("uploads", "public");
        $image = Image::make(storage_path("app/public/$imagePath"))->fit(2000, 2000);
        $image->save();

        return $imagePath;
    }

    public function deleteImage(Model $model): void
    {
        if ($model->image) {
            if (Storage::disk("local")->exists("public/" . $model->image->path)) {
                if (Storage::disk("local")->delete("public/" . $model->image->path)) {
                    $model->image()->delete();
                };
            }
        }
    }
}
