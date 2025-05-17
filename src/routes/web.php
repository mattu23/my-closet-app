<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClothesController;
use App\Http\Controllers\CoordinateController;
use App\Http\Controllers\DashboardController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // カテゴリー管理
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{id}/children', [CategoryController::class, 'children'])->name('categories.children');

    // 洋服管理
    Route::resource('clothes', ClothesController::class);

    // コーディネート管理
    Route::resource('coordinates', CoordinateController::class);
    Route::post('/coordinates/{coordinate}/add-clothes/{clothes}', [CoordinateController::class, 'addClothes'])->name('coordinates.add-clothes');
    Route::delete('/coordinates/{coordinate}/remove-clothes/{clothes}', [CoordinateController::class, 'removeClothes'])->name('coordinates.remove-clothes');
});

require __DIR__.'/auth.php';
