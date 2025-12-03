<?php

namespace App\Libs;

use File;
use  Image;


class Upload
{

    public function UploadImage($name, $path, $model,$custom_name,$x = null,$y = null)
    {

        $filename = '';

        if (request()->hasFile($name)) {

            if (isset($model)) {


                if ($model->{$name} != '' or $model->{$name} != null) {

                    $oldimage = $path . '/' . $model->{$name};

                    if (File::exists($oldimage)) {
                        unlink($oldimage);
                    }
                }


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    if($x == null && $y == null) {
                        request()->file($name)->move($path, $filename);
                    } else {
                        $img_path = request()->file($name)->getRealPath();
                        $pic = Image::make($img_path)->resize($x, $y);
                        $pic->save($path.'/'.$filename);
                    }

                    return $filename;

                } else {

                    $filename =  $custom_name . '.' . request()->file($name)->extension();

                    if($x == null && $y == null) {
                        request()->file($name)->move($path, $filename);
                    } else {
                        $img_path = request()->file($name)->getRealPath();
                        $pic = Image::make($img_path)->resize($x, $y);
                        $pic->save($path.'/'.$filename);
                    }

                    return $filename;
                }


            } else {


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    if($x == null && $y == null) {
                        request()->file($name)->move($path, $filename);
                    } else {
                        $img_path = request()->file($name)->getRealPath();
                        $pic = Image::make($img_path)->resize($x, $y);
                        $pic->save($path.'/'.$filename);
                    }

                    return $filename;

                } else {


                    $filename = $custom_name. '.' . request()->file($name)->extension();

                    if($x == null && $y == null) {
                        request()->file($name)->move($path, $filename);
                    } else {
                        $img_path = request()->file($name)->getRealPath();
                        $pic = Image::make($img_path)->resize($x, $y);
                        $pic->save($path.'/'.$filename);
                    }

                    return $filename;

                }

            }
        }

        if (request()->file($name) == '') {

            //if($model != null)
            $input = $model->{$name};

            return $input;
        }

    }



    public function UploadFile($name, $path, $model,$custom_name = null)
    {

        $filename = '';

        if (request()->hasFile($name)) {

            if (isset($model)) {


                if ($model->{$name} != '' or $model->{$name} != null) {

                    $oldimage = Upload_Path() . '/' . $model->{$name};


                    if (File::exists($oldimage)) {
                        unlink($oldimage);
                    }
                }


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                } else {

                    $filename = $custom_name. '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                }


            } else {


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                } else {

                    $filename = $custom_name. '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                }

            }
        }

        if (request()->file($name)) {

            $input = $model->{$name};

            return $input;
        }

    }



    public function UploadImage2($name, $path, $model,$custom_name)
    {

        $filename = '';

        if (request()->hasFile($name)) {

            if (isset($model)) {


                if ($model->{$name} != '' or $model->{$name} != null) {

                    $oldimage = Upload_Path() . '/' . $model->{$name};


                    if (File::exists($oldimage)) {
                        unlink($oldimage);
                    }
                }


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                } else {

                    $filename =  $custom_name . '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;
                }


            } else {


                if($custom_name == null) {

                    $filename = uniqid() . '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                } else {


                    $filename = $custom_name. '.' . request()->file($name)->extension();

                    request()->file($name)->move($path, $filename);

                    return $filename;

                }

            }
        }

        if (request()->file($name) == '') {

            //if($model != null)
            $input = $model->{$name};

            return $input;
        }

    }


}




