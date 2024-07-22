<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UserController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');

Route::get('/beritas', [BeritaController::class, 'index']);
Route::get('/umkms', [UmkmController::class, 'index']);

Route::group(['middleware' => 'auth:api'], function(){
    //Berita
    Route::post('/beritas', [BeritaController::class, 'store']);
    Route::put('/beritas/{id}', [BeritaController::class, 'update']);
    Route::delete('/beritas/{id}', [BeritaController::class, 'destroy']);

    //UMKM
    Route::post('/umkms', [UmkmController::class, 'store']);
    Route::put('/umkms/{id}', [UmkmController::class, 'update']);
    Route::put('/umkms/status/{id}', [UmkmController::class, 'updateStatus']);
    Route::delete('/umkms/{id}', [UmkmController::class, 'destroy']);
});



