<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
	return view('welcome');
});

Route::prefix('api')->group(function () {
	Route::get('/users', [UserController::class, 'index']);
	Route::post('/users', [UserController::class, 'store']);
});
Route::get('home', [UserController::class, 'home'])->name('home');