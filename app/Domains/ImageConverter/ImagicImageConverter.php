<?php


namespace App\Domains\ImageConverter;

use Intervention\Image\ImageManagerStatic as Image;


class ImagicImageConverter implements ImageConverter
{
    /**
     * @var \Intervention\Image\Image
     */
    private $image;

    private $format;

    public function __construct()
    {
        Image::configure(array('driver' => 'imagick'));
    }

    public function crop(int $width, int $height): ImageConverter
    {
        $this->image = $this->image->resize($width, $height);
        return $this;
    }

    public function toResponse()
    {
        return $this->image->response();
    }

    public function setFilePath(string $filePath): ImageConverter
    {
        $this->image = Image::make($filePath);
        return $this;
    }

    public function setFormat($format): ImageConverter
    {
        $this->format = $format;
        return $this;
    }
}
