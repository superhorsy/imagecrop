<?php


namespace App\Domains\FileProcessor;

use App\Domains\FileValidator\FileValidationException;
use App\Domains\FileValidator\FileValidator;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class FileProcessor
{
    private $url;
    private $cacheKey;
    /**
     * @var FileValidator
     */
    private $fileValidator;

    public function __construct($url, $cacheKey)
    {
        $this->url = $url;
        $this->cacheKey = $cacheKey;
        $this->fileValidator = new FileValidator();
    }

    /**
     * Perform given operation on image
     *
     * @param  Closure  $makeImage
     *
     * @return string
     * @throws FileValidationException
     */
    public function getConvertedImageData(Closure $makeImage): array
    {
        if (Cache::has($this->cacheKey)) {
            $imageData = Cache::get($this->cacheKey);
        } else {
            $imageData = $this->convertImage($makeImage);
        }

        Cache::put(
            $this->cacheKey,
            $imageData,
            Carbon::now()->addHours(config('app.cacheStoreTime'))
        );

        return $imageData;
    }

    private function convertImage(Closure $makeImage)
    {
        $file = $this->putFileToTempDir($this->url);

        $this->fileValidator->run($file);

        $imageData = $makeImage($file);

        $this->deleteFile($file);

        return $imageData;
    }

    private function putFileToTempDir(string $url): string
    {
        $file = uniqid("tmp_", true);
        $limit = config("app.fileSizeLimit") * 10e6;

        if (!$rfp = fopen($url, 'r')) {
            throw new FileValidationException("Could not open remote file");
        }
        if (!$lfp = fopen($file, 'w')) {
            throw new FileValidationException("Could not open local file");
        }

        $this->checkFileSize($http_response_header);

        $downloaded = 0;

        //Set timeout per chunk at 5 sec
        stream_set_blocking($rfp, true);
        stream_set_timeout($rfp, 5);
        $info = stream_get_meta_data($rfp);

        while (!feof($rfp) && $downloaded < $limit && (!$info['timed_out'])) {
            $chunk = fgets($rfp, 8192);
            $info = stream_get_meta_data($rfp);
            fwrite($lfp, $chunk);
            $downloaded += strlen($chunk);
        }

        fclose($lfp);

        if ($info['timed_out']) {
            throw new FileValidationException("Connection Timed Out!");
        }

        if ($downloaded > $limit) {
            unlink($file);
            throw new FileValidationException("File to large");
        }

        return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$file;
    }

    private function deleteFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Check the content-length for exceeding the limit
     */
    private function checkFileSize(array $headers)
    {
        foreach ($headers as $header) {
            if (preg_match(
                '/^\s*content-length\s*:\s*(\d+)\s*$/',
                $header,
                $matches
            )
            ) {
                if ($matches[1] > config("app.fileSizeLimit") * 10e6) {
                    throw new FileValidationException("File to large");
                }
            }
        }
    }
}
