<?php

namespace App\Providers;

use App\Domains\ImageConverter\ImageConverter;
use App\Domains\ImageConverter\ImagicImageConverter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ImageConverter::class,
            ImagicImageConverter::class
        );
    }
}
