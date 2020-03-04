<?php


namespace App\Helpers;


class FileHelper
{
    public static function putFileToTempDir(string $url): string
    {
        $filePath = uniqid("tmp_",true);

        if (!file_put_contents($filePath, file_get_contents($url))) {
            echo "File downloading failed.";
        }
        return $filePath;
    }

    public static function deleteFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function getCacheKey(string $url, int $height, int $width):string {
        return "$url:$height:$width";
    }
}
