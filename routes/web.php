<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::get('/app-launcher', function () {
    return view('app-launcher');
})->middleware('auth')->name('app.launcher');

