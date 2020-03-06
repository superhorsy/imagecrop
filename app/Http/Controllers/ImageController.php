<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{

    public function __construct()
    {
        $this->middleware('fileSize');
    }

    public function convertImage(Request $request)
    {
        $this->validateResizeRequest($request);

        $cacheKey = "{$request->url}:{$request->width}x{$request->height}";

        $imageService = new ImageService($request->url, $cacheKey);
        $imageService->resizeImage($request->width, $request->height);


        return response(
            $imageService->getResultImage(),
            200,
            [
                'Content-Type' => $imageService->getMime()
            ]
        );
    }

    private function validateResizeRequest($request)
    {
        $maxDimensions = config('app.imageDimensionsLimit');

        $this->validate(
            $request,
            [
                "url"    => "required|url",
                "height" => "required|numeric|max:$maxDimensions",
                "width"  => "required|numeric|max:$maxDimensions",
            ]
        );
    }
}
