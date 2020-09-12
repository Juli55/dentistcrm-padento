<?php

namespace App\Http\Controllers\API;

use App\Image;
use App\Http\Requests;
use App\Lab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    public function destroy($imageId)
    {
        $image = Image::findOrFail($imageId);

        $image->delete();

        return response()->json('success');
    }

    public function sort(Request $request)
    {
        $images = Image::whereIn('id', $request->ids)->get();

        $order = 1;

        foreach ($request->ids as $id) {
            $image = $images->where('id', $id)->first();

            $image->order = $order;
            $image->save();

            $order++;
        }

        return $images->sortBy('order')->values()->all();
    }

    public function sync()
    {
        $labs = Lab::all();

        foreach ($labs as $lab) {
            for($i=1; $i<=5; $i++) {
                $path = public_path(sprintf("img/laborbilder/bild%s%s_neu.jpg", $i, $lab->id));

                if (\File::exists($path)) {
                    $file = pathToUploadedFile($path);

                    $image = Image::named($file->getClientOriginalName())->type('bilder');

                    \File::copy($path, $image->path);

                    $lab->saveImage($image);

                    $image->makeThumbnail();
                    $image->makeIcon();
                }

                $path = public_path(sprintf("img/zertifikate/zert%s%s_neu.jpg", $i, $lab->id));

                if (\File::exists($path)) {
                    $file = pathToUploadedFile($path);

                    $image = Image::named($file->getClientOriginalName())->type('zert');

                    \File::copy($path, $image->path);

                    $lab->saveImage($image);

                    $image->makeThumbnail();
                    $image->makeIcon();
                }
            }
        }

        return "success";
    }
}
