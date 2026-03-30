<?php

namespace App\Providers;

use App\Models\Ville;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Injecte les villes actives dans toutes les vues du layout
        // → disponible via $villesNav dans layouts/app.blade.php
        View::composer('layouts.app', function ($view) {
            $view->with('villesNav', Ville::where('active', true)
                ->orderBy('nom')
                ->get(['id', 'nom', 'slug', 'emoji']));
        });
    }
}
