<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

// Publik: registrasi & login (login mengembalikan token Sanctum)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Terproteksi: wajib header "Authorization: Bearer <token>"
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD RESTful: index, store, show, update, destroy
    Route::apiResource('notes', NoteController::class)->names('api.notes');
});
