<?php

use App\Http\Controllers\CseController;
use App\Http\Controllers\DseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dse', [DseController::class, 'index'])->name('dse');
Route::get('dse-data', [DseController::class, 'fetch'])->name('dse.fetch');


Route::get('cse', [CseController::class, 'index'])->name('cse');
Route::get('cse-data', [CseController::class, 'fetch'])->name('cse.fetch');
