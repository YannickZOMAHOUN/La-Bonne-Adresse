<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisiteurController;
use App\Http\Controllers\Proprietaire\EtablissementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| ESPACE PUBLIC — Visiteurs (sans connexion)
|--------------------------------------------------------------------------
*/
Route::get('/', [VisiteurController::class, 'index'])->name('home');
Route::get('/adresses', [VisiteurController::class, 'liste'])->name('adresses.liste');
Route::get('/adresses/{slug}', [VisiteurController::class, 'show'])->name('adresses.show');


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| ESPACE PROPRIÉTAIRE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:proprietaire'])
    ->prefix('mon-espace')
    ->name('proprietaire.')
    ->group(function () {

    Route::get('/',                          [EtablissementController::class, 'dashboard'])->name('dashboard');
    Route::get('/nouvelle-fiche',            [EtablissementController::class, 'create'])->name('create');
    Route::post('/nouvelle-fiche',           [EtablissementController::class, 'store'])->name('store');
    Route::get('/fiche/{etablissement}/modifier', [EtablissementController::class, 'edit'])->name('edit');
    Route::put('/fiche/{etablissement}',     [EtablissementController::class, 'update'])->name('update');

    // Suppression d'une photo de galerie
    Route::delete('/photo/{photo}',          [EtablissementController::class, 'deletePhoto'])->name('photo.delete');
});


/*
|--------------------------------------------------------------------------
| ESPACE ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Établissements
    Route::get('/etablissements',                          [AdminController::class, 'etablissements'])->name('etablissements');
    Route::post('/etablissements/{etablissement}/valider', [AdminController::class, 'valider'])->name('valider');
    Route::post('/etablissements/{etablissement}/suspendre',[AdminController::class, 'suspendre'])->name('suspendre');
    Route::post('/etablissements/{etablissement}/vedette', [AdminController::class, 'toggleVedette'])->name('vedette');

    // Propriétaires
    Route::get('/proprietaires',                          [AdminController::class, 'proprietaires'])->name('proprietaires');
    Route::post('/proprietaires/{user}/activer',          [AdminController::class, 'activerProprietaire'])->name('activer-proprio');
    Route::post('/proprietaires/{user}/suspendre',        [AdminController::class, 'suspendrePropietaire'])->name('suspendre-proprio');
});
