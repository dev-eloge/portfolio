<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Middleware\NoIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/langue/{locale}', LocaleController::class)->name('locale');

Route::get('/a-propos', [PageController::class, 'about'])->name('about');
Route::get('/competences', [PageController::class, 'skills'])->name('skills');
Route::get('/experience', [PageController::class, 'experience'])->name('experience');
Route::get('/certifications', [PageController::class, 'certifications'])->name('certifications');

Route::get('/projets', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projets/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::post('/assistant', AssistantController::class)
    ->middleware('throttle:10,1')
    ->name('assistant');

/*
|--------------------------------------------------------------------------
| Admin (adresse réglable avec ADMIN_PATH dans .env)
|--------------------------------------------------------------------------
*/
Route::prefix(config('portfolio.admin_path'))->name('admin.')->middleware(NoIndex::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('/connexion', [Admin\AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::post('/deconnexion', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::get('/profil', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [Admin\ProfileController::class, 'update'])->name('profile.update');

        foreach ([
            'projets' => ['projects', Admin\ProjectController::class],
            'parcours' => ['experiences', Admin\ExperienceController::class],
            'competences' => ['skills', Admin\SkillController::class],
            'certifications' => ['certifications', Admin\CertificationController::class],
            'services' => ['services', Admin\ServiceController::class],
        ] as $uri => [$name, $controller]) {
            Route::resource($uri, $controller)->except('show')->parameters([$uri => 'id'])->names($name);
        }

        Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::delete('/messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
    });
});
