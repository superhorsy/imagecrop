<?php


namespace App\Services;


use App\Domains\FileProcessor\FileProcessor;
use App\Domains\ImageConverter\ImageConverter;

class ImageService
{
    private $mime;
    private $resultImage;
    /**
     * @var FileProcessor
     */
    private $processor;
    /**
     * @var ImageConverter
     */
    private $converter;

    public function __construct(string $url, string $cacheKey)
    {
        $this->processor = new FileProcessor($url, $cacheKey);

        $this->converter = app()->make(ImageConverter::class);
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getResultImage(): string
    {
        return $this->resultImage;
    }

    public function resizeImage(int $width, int $height): void
    {
        $conversion = function ($file) use ($width, $height) {
            return $this->converter
                ->setFile($file)
                ->resize($width, $height)
                ->getData();
        };

        $data = $this->processor->getConvertedImageData($conversion);
        list ($this->mime, $this->resultImage) = $data;
    }
}
