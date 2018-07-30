<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class Img extends \App\Controller\Master\MasterController
{

    public function resize(string $img, int $size)
    {
        $img = str_replace("_dirsep_", "/", $img);
        $imgPath = GenFun::rootPath() . "uploads/{$img}";
        
        if (\file_exists($imgPath)) {

            // check file extension
            $FileInfo = pathinfo($imgPath);

            if (empty($FileInfo['extension'])) {
                die('Error 404');
            }

            list($width, $height) = getimagesize($imgPath);
            $newHeight = $size;
            $newWidth = \round(($newHeight / $height) * $width);
            $image_p = \imagecreatetruecolor($newWidth, $newHeight);

            if ($FileInfo['extension'] == 'png') {
                $image = \imagecreatefrompng($imgPath);
                header('Content-type: image/png');
                \imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                \imagepng($image_p);
            } else {

                header('Content-type: image/jpeg');

                $image = \imagecreatefromjpeg($imgPath);
                \imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                \imagejpeg($image_p, null, 100);
            }

        } else {
            die('Error 404');
        }
    }

    public function resizeWH(string $img, int $width, int $height)
    {
        $img = str_replace("_dirsep_", "/", $img);
        $imgPath = GenFun::rootPath() . "uploads/{$img}";
        if (!\file_exists($imgPath)) {
            die('Error 404');
        }

        // check file extension
        $FileInfo = pathinfo($imgPath);

        if (empty($FileInfo['extension'])) {
            die('Error 404');
        }
        if ($FileInfo['extension'] == 'png') {
            $image = imagecreatefrompng($imgPath);
        }else{
            $image = imagecreatefromjpeg($imgPath);
        }

        $thumb_width = $width;
        $thumb_height = $height;
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ( $original_aspect >= $thumb_aspect )
        {
           // If image is wider than thumbnail (in aspect ratio sense)
           $new_height = $thumb_height;
           $new_width = $width / ($height / $thumb_height);
        }
        else
        {
           // If the thumbnail is wider than the image
           $new_width = $thumb_width;
           $new_height = $height / ($width / $thumb_width);
        }
        
        $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
        
        // Resize and crop
        imagecopyresampled($thumb,
                           $image,
                           0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                           0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                           0, 0,
                           $new_width, $new_height,
                           $width, $height);
        if ($FileInfo['extension'] == 'png') {
            header('Content-type: image/jpeg');
            imagejpeg($thumb, null, 80);
        }else{
            header('Content-type: image/png');
            imagepng($thumb);
        }

    }

}
