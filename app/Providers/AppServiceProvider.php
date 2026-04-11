<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Ville;
use App\Models\Categorie;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS en production (derrière le proxy Railway)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Utilise le template de pagination personnalisé LBA
        Paginator::defaultView('vendor.pagination.tailwind');

        // Injecte villes et catégories dans TOUTES les vues
        View::composer('*', function ($view) {
            $data = $view->getData();

            if (!isset($data['villesNav'])) {
                $view->with('villesNav', Ville::where('active', true)
                    ->orderBy('nom')
                    ->get(['id', 'nom', 'slug', 'emoji']));
            }

            if (!isset($data['categoriesNav'])) {
                $view->with('categoriesNav', Categorie::where('active', true)
                    ->orderBy('nom')
                    ->get(['id', 'nom', 'slug', 'emoji']));
            }
        });
    }
}
