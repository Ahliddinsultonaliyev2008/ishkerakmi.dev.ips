<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');


/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
