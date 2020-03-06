<?php


namespace App\Domains\ImageConverter;


interface ImageConverter
{
    public function resize(int $width, int $height):ImageConverter;

    /**
     * Returns an array with mime type and image after conversion
     * @return array
     */
    public function getData():array;
}
