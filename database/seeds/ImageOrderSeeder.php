<?php

use App\Image;
use Illuminate\Database\Seeder;

class ImageOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images = Image::all();

        foreach ($images as $image) {
            if(!$image->order) {
                $image->order = $image->id;
                $image->save();
            }
        }
    }
}
