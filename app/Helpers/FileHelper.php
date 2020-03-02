<?php


namespace App\Helpers;


class FileHelper
{
    public static function putFileToTempDir(string $url): string
    {
        // Use basename() function to return the base name of file
        $filePath = basename($url);

        // Use file_get_contents() function to get the file
        // from url and use file_put_contents() function to
        // save the file by using base name
        if (!file_put_contents($filePath, file_get_contents($url))) {
            echo "File downloading failed.";
        }
        return $filePath;
    }
}
