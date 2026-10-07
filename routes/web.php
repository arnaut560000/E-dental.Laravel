<?php

use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\StaffRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/request', [ClientRequestController::class, 'create'])->name('requests.create');
Route::post('/request', [ClientRequestController::class, 'store'])
    ->middleware('throttle:10,60')
    ->name('requests.store');
Route::get('/request/{code}', [ClientRequestController::class, 'thanks'])->name('requests.thanks');

Route::middleware('guest')->group(function () {
    Route::get('/staff/login', [StaffAuthController::class, 'create'])->name('login');
    Route::post('/staff/login', [StaffAuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::post('/staff/logout', [StaffAuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'staff.access:admin,scheduler'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/requests', [StaffRequestController::class, 'index'])
            ->name('requests.index');
    });
