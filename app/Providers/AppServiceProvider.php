<?php

namespace App\Providers;

use App\Models\Ville;
use App\Models\Categorie;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS en production (derrière le proxy Railway)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Injecte villes et catégories dans le layout
        View::composer('layouts.app', function ($view) {
            $view->with('villesNav', Ville::where('active', true)
                ->orderBy('nom')
                ->get(['id', 'nom', 'slug', 'emoji']));

            $view->with('categoriesNav', Categorie::where('active', true)
                ->orderBy('nom')
                ->get(['id', 'nom', 'slug', 'emoji']));
        });
    }
}
