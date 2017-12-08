<?php

namespace AminYazdanpanah\HLSExporter;

use Illuminate\Support\ServiceProvider;

class HLSExporterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('php-ffmpeg-hls-conversion', function ($app) {
            return $app->make(HLSExporter::class);
        });
    }
}
