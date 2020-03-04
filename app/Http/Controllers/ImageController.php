<?php

namespace App\Http\Controllers;

use App\Domains\ImageConverter\ImageConverter;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

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
     * @param $url string File Url
     * @param $width integer
     * @param $height integer
     */
    public function convertImage(Request $request)
    {
        extract($request->only('url', 'height', 'width'));

        $key = FileHelper::getCacheKey($url, $height, $width);

        if (Cache::has($key)) {
            $image = Cache::get($key);

            Cache::put($key, $image, Carbon::now()->addHours(5));
        } else {
            $linkFileFormat = pathinfo($url, PATHINFO_EXTENSION);

            $file = FileHelper::putFileToTempDir($url);

            $image = $this->imageConverter
                ->setFormat($linkFileFormat)
                ->setFilePath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file)
                ->crop($width, $height)
                ->toResponse();

            Cache::put($key, $image, Carbon::now()->addHours(5));

            FileHelper::deleteFile($file);
        }

        return $image;
    }
}
