<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Arr;

class VerifyFileSize
{
    public const FILE_SIZE_LIMIT = 5000000;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $expectedFileSize = $this->getExpectedFileSize($request->url);
        } catch (\Exception $e) {
            return response($e->getMessage(), 403);
        }

        if ($expectedFileSize > static::FILE_SIZE_LIMIT) {
            return response("File couldn't be bigger than 5 Mb", 413);
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
            $result = Arr::get($response->getHeaders(),'Content-Length.0');
        }

        if (!isset($result)) {
            throw new \Exception("Can't read file content");
        }

        return $result;
    }
}
