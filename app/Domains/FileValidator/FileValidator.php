<?php


namespace App\Domains\FileValidator;


class FileValidator
{
    public $file;
    /**
     * Image dimensions limit
     */
    private $dimensionsLimit;

    public function __construct()
    {
        $this->dimensionsLimit = config('app.imageDimensionsLimit');
    }

    /**
     * Runs validations on file
     */
    public function run($file)
    {
        $this->file = $file;
        $this->checkResolution();
    }

    private function checkResolution()
    {
        exec('identify -format "%w %h" ' . escapeshellarg($this->file), $output, $error);
        if (!$error) {
            list($width, $height) = explode(' ', $output[0]);
        } else {
            throw new FileValidationException("Can't read file parameters");
        }
        if ($height > $this->dimensionsLimit || $width > $this->dimensionsLimit) {
            throw new FileValidationException("Image is to big");
        }
    }
}
