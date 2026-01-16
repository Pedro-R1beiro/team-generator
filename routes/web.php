<?php

use App\Http\Controllers\AutoTeamsController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamSetController;
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

Route::get('/teams', [UserController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('teams');

Route::post('/players/auto', [AutoTeamsController::class, 'autoPlayers'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('players.auto');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/team-sets', [TeamSetController::class, 'index']);
    Route::post('/team-sets', [TeamSetController::class, 'store']);
    Route::get('/team-sets/{teamSet}', [TeamSetController::class, 'show']);
    Route::put('/team-sets/{teamSet}', [TeamSetController::class, 'update']);
    Route::delete('/team-sets/{teamSet}', [TeamSetController::class, 'destroy']);

    // partidas
    Route::post('/team-sets/{teamSet}/games', [GameController::class, 'store']);
    Route::delete('/games/{game}', [GameController::class, 'destroy']);
});

require __DIR__.'/auth.php';
