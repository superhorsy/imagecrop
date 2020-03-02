<?php


namespace App\Domains\ImageConverter;


interface ImageConverter
{
    /**
     * Returns cropped image filepath
     * @param int $width
     * @param int $height
     * @return string
     */
    public function crop(int $width, int $height):ImageConverter;
}
