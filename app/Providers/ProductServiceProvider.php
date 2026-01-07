<?php

namespace App\Providers;

use App\Services\Impl\ProductServiceImpl;
use App\Services\ProductService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;


class ProductServiceProvider extends ServiceProvider
{

    function provides()
    {
        return [ProductService::class];
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        App::singleton(ProductService::class, function () {
            return new ProductServiceImpl();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
