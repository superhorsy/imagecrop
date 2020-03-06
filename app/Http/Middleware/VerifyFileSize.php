<?php

namespace App\Http\Middleware;

use App\Domains\FileValidator\FileValidationException;
use Closure;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Lumen\Application;

class VerifyFileSize
{
    /**
     * @var Application|mixed
     */
    private $fileSizeLimit;

    public function __construct()
    {
        $this->fileSizeLimit = config('app.fileSizeLimit');
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure                   $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $expectedFileSize = $this->getExpectedFileSize($request->url);
        } catch (Exception $e) {
            return response(
                "Can't get file size",
                413
            );
        }

        if ($expectedFileSize > $this->fileSizeLimit * 1000000) {
            $formattedFileSize = $expectedFileSize / 1000000;
            return response(
                "File couldn't be bigger than {$this->fileSizeLimit} Mb,
                 your file is $formattedFileSize Mb",
                413
            );
        }

        return $next($request);
    }

    private function getExpectedFileSize(string $url)
    {
        $client = new HttpClient();
        $response = $client->request('HEAD', $url);
        $status = $response->getStatusCode();

        // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        if ($status == 200 || ($status > 300 && $status <= 308)) {
            $result = Arr::get($response->getHeaders(), 'Content-Length.0');
        }

        if (!isset($result)) {
            throw new FileValidationException("Can't read file content");
        }

        return $result;
    }
}
