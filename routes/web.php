<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::middleware('auth')->group(function () {
    Route::get('/pending-approval', function () {
        return view('auth.pending-approval');
    })->name('pending-approval');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // TraceNova — App management (each user sees only their own apps)
    Route::resource('apps', AppController::class);
    Route::post('apps/{app}/api-keys', [AppController::class, 'storeApiKey'])->name('apps.api-keys.store');
    Route::delete('apps/{app}/api-keys/{api_key}', [AppController::class, 'revokeApiKey'])->name('apps.api-keys.revoke');

    Route::get('apps/{app}/journey/{sessionId}', [AppController::class, 'journey'])->name('apps.journey.show');
    Route::get('apps/{app}/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('apps.reports.index');

    Route::resource('apps.funnels', FunnelController::class)->parameters(['funnels' => 'funnel']);
});

// Super Admin — user approval (only super admins)
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [\App\Http\Controllers\Admin\UserController::class, 'reject'])->name('users.reject');
});

require __DIR__.'/auth.php';
