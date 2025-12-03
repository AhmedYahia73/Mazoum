<?php

use App\Libs\Upload;

if (! function_exists('upload')) {

    function upload() {
        return new Upload();
    }

}

if (! function_exists('Upload_Path')) {

    function Upload_Path($img = null) {

        if ($img == null) {
            return public_path('images');
        } else {
            return public_path('images') . '/' . $img;
        }

    }

}


if (! function_exists('Image_Path')) {

    function Image_Path($img = null) {

        $existImage = public_path('images') . '/' . $img;

        if (! File::exists($existImage) || $img == null) {
            return asset('img/no-image.png');
        } else {
            return asset('images') . '/' . $img;
        }

    }

}


if (! function_exists('Custom_Image_Path')) {

    function Custom_Image_Path($path,$img) {

        $existImage = public_path($path) . '/' . $img;

        /*
      	if (! File::exists($existImage) || $img == null) {
            return asset('img/no-image.png');
        } else {
            return asset($path) . '/' . $img;
        }
        */
      
		return asset($path) . '/' . $img;


    }

}

if (! function_exists('Custom_Video_Path')) {

    function Custom_Video_Path($path,$video) {

        $existVideo = public_path($path) . '/' . $video;

        if (! File::exists($existVideo) || $video == null) {
            return '';
        } else {
            return asset($path) . '/' . $video;
        }

    }

}
