<?php

namespace App\Http\Controllers;

use File;
use App\Lab;
use App\Http\Requests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MigrationController extends Controller
{
    public function images()
    {
        $labs = Lab::all();

        foreach ($labs as $lab) {
            for($i = 1; $i<=6; $i++) {
                $imagePath = public_path("img/laborbilder/bild{$i}{$lab->id}_neu.jpg");

                if(File::exists($imagePath))
                {
                    $image = new UploadedFile($imagePath, 'old-bilder.jpg');

                    $lab->uploadImage($image, 'bilder');
                }

                $imagePath = public_path("img/zertifikate/zert{$i}{$lab->id}_neu.jpg");

                if(File::exists($imagePath))
                {
                    $image = new UploadedFile($imagePath, 'old-zert.jpg');

                    $lab->uploadImage($image, 'zert');
                }
            }
        }

        return 'Images migrated successfully!';
    }
}
