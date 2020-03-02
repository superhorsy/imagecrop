<?php

namespace App\Http\Controllers;

use App\Domains\ImageConverter\ImageConverter;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Intervention\Image\Image;

class ImageController extends Controller
{
    /**
     * @var ImageConverter
     */
    private $imageConverter;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ImageConverter $imageConverter)
    {
        $this->middleware('fileSize');

        $this->imageConverter = $imageConverter;
    }

    /**
     * @param $url string File url
     * @param $width integer
     * @param $height integer
     */
    public function convertImage(Request $request)
    {
        extract($request->only('width','height','url'));

        $linkFileFormat = pathinfo($url, PATHINFO_EXTENSION);
        $file = FileHelper::putFileToTempDir($url);

        $image = $this->imageConverter
            ->setFormat($linkFileFormat)
            ->setFilePath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file)
            ->crop($width,$height);

        return $image->toResponse();
    }
}
