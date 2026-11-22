<?php

use App\Http\Controllers\BinanceController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CourseController::class, 'index'])->name('courses.index');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/json', [CourseController::class, 'json'])->name('courses.json');
Route::post('/json', [CourseController::class, 'storeJson'])->name('courses.json.store');
Route::get('/binance', [BinanceController::class, 'index'])->name('binance.index');
