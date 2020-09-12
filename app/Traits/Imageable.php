<?php

namespace App\Traits;

use App\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait Imageable
{
    /**
     * Related images
     *
     * @return mixed
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Avatar image
     *
     * @return mixed
     */
    public function default_image()
    {
        return $this->morphOne(Image::class, 'imageable')->default();
    }

    /**
     * Upload image to collection
     *
     * @param $file
     * @param null $type
     *
     * @return Model
     */
    public function uploadImage($file, $type = null)
    {
        $image = Image::named($file->getClientOriginalName())->type($type)->move($file);

        return $this->saveImage($image);
    }

    /**
     * Save image to collection
     *
     * @param Image $image
     *
     * @return Model
     */
    public function saveImage(Image $image)
    {
        return $this->images()->save($image);
    }

    /**
     * Set image as an avatar
     *
     * @param $imageId
     *
     * @return mixed
     */
    public function setDefaultImage($imageId)
    {
        $image = $this->images()->findOrFail($imageId);

        $this->images()->update(['is_default' => false]);

        $image->is_default = true;

        return $image->save();
    }

    public function defaultImage()
    {
        $image = $this->default_image;

        if (! $image) {
            $image = $this->images()->oldest()->first();
        }

        return $image;
    }

    public function removeImages()
    {
        foreach ($this->images as $image) {
            $image->removeImages();

            $image->delete();
        }
    }
}