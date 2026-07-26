<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path() . '/Helpers/*.php') as $file) {
            require_once($file);
        }

        if (class_exists(\Dedoc\Scramble\Infer\Services\FileParser::class)) {
            $this->app->singleton(\Dedoc\Scramble\Infer\Services\FileParser::class, function ($app) {
                return new \App\Support\ScrambleFileParser($app->make(\PhpParser\Parser::class));
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
