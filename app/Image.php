<?php

namespace App;

use Image as InterventionImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Image extends Model
{
    protected $fillable = [
        'name', 'path', 'thumbnail_path', 'icon_path', 'title', 'description', 'type', 'is_default', 'order',
    ];

    protected $hidden = [
        'imageable_type', 'imageable_id',
    ];

    protected $appends = [
        'url', 'thumbnail_url', 'icon_url',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $baseDir = 'img';

    protected $thumbSize = 300;

    protected $iconSize = 100;

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Is default image
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeDefault($query)
    {
        return $query->whereIsDefault(true);
    }

    /**
     * Make new image object
     *
     * @param $name
     *
     * @return mixed
     */
    public static function named($name)
    {
        return (new static)->saveAs($name);
    }

    /**
     * Save file attributes
     *
     * @param $name
     *
     * @return $this
     */
    protected function saveAs($name)
    {
        $this->name = sprintf("%s-%s", generateUUID(), $name);
        $this->path = sprintf("%s/%s", $this->baseDir, $this->name);
        $this->thumbnail_path = sprintf("%s/th-%s", $this->baseDir, $this->name);
        $this->icon_path = sprintf("%s/ic-%s", $this->baseDir, $this->name);
        $this->order = self::max('order') + 1;

        return $this;
    }

    /**
     * Set image type.
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Move file to directory
     *
     * @param $file
     *
     * @return $this
     */
    public function move($file)
    {
        Storage::disk('public')->put($this->path, file_get_contents($file));
        $this->makeThumbnail();

        $this->makeIcon();

        return $this;
    }

    /**
     * Make image thumbnail
     */
    public function makeThumbnail()
    {
        InterventionImage::make(storage_path('app/public/'.$this->path))
            ->fit($this->thumbSize)
            ->save(storage_path('app/public/'.$this->thumbnail_path));
    }

    /**
     * Make image icon
     */
    public function makeIcon()
    {
        InterventionImage::make(storage_path('app/public/'.$this->path))
            ->fit($this->iconSize)
            ->save(storage_path('app/public/'.$this->icon_path));
    }

    /**
     * Remove images
     */
    public function removeImages()
    {
        Storage::disk('public')->delete([$this->path, $this->thumbnail_path, $this->icon_path]);
    }

    public function getThumbnailPath()
    {
        return url(Storage::url($this->thumbnail_path));
    }

    public function getIconPath()
    {
        return url(Storage::url($this->icon_path));
    }

    public function getPath()
    {
        return url(Storage::url($this->path));
    }

    public function getUrlAttribute()
    {
        return $this->getPath();
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getThumbnailPath();
    }

    public function getIconUrlAttribute()
    {
        return $this->getIconPath();
    }

    public function delete()
    {
        $this->removeImages();

        return parent::delete();
    }

}
