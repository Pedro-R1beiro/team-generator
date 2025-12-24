<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/players', [UserController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('players');

Route::post('/players', [UserController::class, 'store'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('players.store');

Route::put('/players/{user}', [UserController::class, 'update'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('players.update');

Route::delete('/players/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('players.destroy');

Route::get('/teams', function () {
    return view('admin.teams');
})->middleware(['auth', 'verified', 'admin'])->name('teams');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
