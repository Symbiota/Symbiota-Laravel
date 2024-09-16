<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     */
    public function register(): void {
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/custom');
        Blade::anonymousComponentPath(__DIR__.'/../../resources/views/core');

        $this->callAfterResolving('blade.compiler', static function (BladeCompiler $compiler) {
            $compiler->extend(static function ($value) {
                return \preg_replace('/\s*@trim\s*/m', '', $value);
            });
        });
    }
}
