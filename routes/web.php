<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // TraceNova — App management
    Route::resource('apps', AppController::class);
    Route::post('apps/{app}/api-keys', [AppController::class, 'storeApiKey'])->name('apps.api-keys.store');
    Route::delete('apps/{app}/api-keys/{api_key}', [AppController::class, 'revokeApiKey'])->name('apps.api-keys.revoke');

    Route::get('apps/{app}/journey/{sessionId}', [AppController::class, 'journey'])->name('apps.journey.show');
    Route::get('apps/{app}/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('apps.reports.index');

    Route::resource('apps.funnels', FunnelController::class)->parameters(['funnels' => 'funnel']);
});

require __DIR__.'/auth.php';
