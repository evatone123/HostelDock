<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Blade::directive('checked', function ($expression) {
            return "<?php if ({$expression}) { echo 'checked'; } ?>";
        });
        Blade::directive('selected', function ($expression) {
            return "<?php if ({$expression}) { echo 'selected'; } ?>";
        });
        Blade::directive('disabled', function ($expression) {
            return "<?php if ({$expression}) { echo 'disabled'; } ?>";
        });
    }
}
