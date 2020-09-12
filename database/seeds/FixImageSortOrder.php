<?php

use App\Image;
use Illuminate\Database\Seeder;

class FixImageSortOrder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = Image::max('order') + 1;

        $images = Image::whereNull('order')->get();

        foreach ($images as $image) {
            $image->order = $order;
            $image->save();

            $order++;
        }
    }
}
