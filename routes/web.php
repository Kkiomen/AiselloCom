<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

// Language switcher / Przełącznik języka
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'pl'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('locale.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes / Trasy profilu
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Keys routes / Trasy kluczy API
    Route::prefix('api-keys')->name('api-keys.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ApiKeyController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ApiKeyController::class, 'store'])->name('store');
        Route::get('/{apiKey}', [\App\Http\Controllers\ApiKeyController::class, 'show'])->name('show');
        Route::delete('/{apiKey}', [\App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('destroy');
    });

    // API Explorer routes / Trasy eksploratora API
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/explorer', [\App\Http\Controllers\ApiExplorerController::class, 'index'])->name('explorer');
        Route::get('/playground/{slug}', [\App\Http\Controllers\ApiPlaygroundController::class, 'show'])->name('playground');
        Route::post('/playground/{slug}/execute', [\App\Http\Controllers\ApiPlaygroundController::class, 'execute'])->name('playground.execute');
    });

    // Usage & Stats routes / Trasy użycia i statystyk
    Route::get('/usage', [\App\Http\Controllers\UsageController::class, 'index'])->name('usage.index');

    // User Prompts routes / Trasy promptów użytkownika
    Route::resource('user-prompts', \App\Http\Controllers\UserPromptController::class);
    Route::post('/user-prompts/{userPrompt}/set-default', [\App\Http\Controllers\UserPromptController::class, 'setDefault'])->name('user-prompts.set-default');
});

require __DIR__.'/auth.php';
