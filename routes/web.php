<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreateController;
use App\Http\Controllers\RDWController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auto's
Route::get('/cars', [CreateController::class, 'index'])->name('cars.index');
Route::get('/cars/create', [CreateController::class, 'create'])->name('cars.create')->middleware('auth');
Route::post('/cars', [CreateController::class, 'store'])->name('cars.store')->middleware('auth');
Route::get('/cars/mine', [CreateController::class, 'mine'])->name('cars.mine')->middleware('auth');

// Let op: {car} ipv {id} voor route model binding
Route::get('/cars/{car}', [CreateController::class, 'show'])->name('cars.show');
Route::get('/cars/{car}/edit', [CreateController::class, 'edit'])->name('cars.edit')->middleware('auth');
Route::put('/cars/{car}', [CreateController::class, 'update'])->name('cars.update')->middleware('auth');
Route::delete('/cars/{car}', [CreateController::class, 'destroy'])->name('cars.destroy')->middleware('auth');

// RDW routes
Route::get('/cars/rdw', [RDWController::class, 'showRdwData'])->name('cars.rdw')->middleware('auth');
Route::get('/cars/rdw/search', [RDWController::class, 'search'])->name('cars.rdw.search')->middleware('auth');

// Admin dashboard
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('auth');
Route::get('/admin/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('admin.dashboard.stats')->middleware('auth');

require __DIR__.'/auth.php';
