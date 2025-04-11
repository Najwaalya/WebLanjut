<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        View::composer('*', function ($view) {
            $currentRouteName = Route::currentRouteName();
            $activeMenu = match (true) {
                str_starts_with($currentRouteName, 'dashboard') => 'dashboard',
                str_starts_with($currentRouteName, 'level') => 'level',
                str_starts_with($currentRouteName, 'user') => 'user',
                str_starts_with($currentRouteName, 'kategori') => 'kategori',
                str_starts_with($currentRouteName, 'barang') => 'barang',
                str_starts_with($currentRouteName, 'supplier') => 'supplier',
                str_starts_with($currentRouteName, 'stok') => 'stok',
                str_starts_with($currentRouteName, 'penjualan') => 'penjualan',
                str_starts_with($currentRouteName, 'profile') => 'profile',
                default => '',
            };
    
            $view->with('activeMenu', $activeMenu);
        });

        View::composer('*', function ($view) {
            $view->with('breadcrumb', (object)[
                'title' => 'Halaman',
                'list' => ['Home', 'Halaman']
            ]);
        });
        
    }
}
