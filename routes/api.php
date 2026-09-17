<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::post('/authors', [AuthorController::class, 'store']);
Route::get('/authors', [AuthorController::class, 'index']);

Route::post('/books', [BookController::class, 'store']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{uuid}', [BookController::class, 'show']);
Route::post('/books/{uuid}/borrow', [BookController::class, 'borrow']);
Route::post('/books/{uuid}/return', [BookController::class, 'returnBook']);
