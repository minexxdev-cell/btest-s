<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;  // Add this line
use App\Models\Produk;                 // Add this line


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('layouts.master', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('layouts.auth', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth.login', function ($view) {
            $view->with('setting', Setting::first());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
 public function boot()
{
    if ($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
       // Share low stock products with all views
        View::composer('*', function ($view) {
            $lowStockProducts = Produk::where('stok', '<=', 1)
                ->leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
                ->select('produk.*', 'nama_kategori')
                ->get();
            
            $view->with('lowStockProducts', $lowStockProducts);
        });
}
}
