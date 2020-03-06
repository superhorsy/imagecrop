<?php


namespace App\Domains\ImageConverter;

use Intervention\Image\ImageManagerStatic as Image;


class ImagicImageConverter implements ImageConverter
{
    /**
     * @var \Intervention\Image\Image
     */
    private $image;

    public function __construct()
    {
        Image::configure(array('driver' => 'imagick'));
    }

    public function resize(int $width, int $height): ImageConverter
    {
        $this->image = $this->image->resize($width, $height);
        return $this;
    }

    /**
     * Returns an array with mime type and image after conversion
     * @return array
     */
    public function getData():array
    {
        return [$this->image->mime(), $this->image->encode()];
    }

    public function setFile(string $file): ImageConverter
    {
        $this->image = Image::make($file);
        return $this;
    }
}
