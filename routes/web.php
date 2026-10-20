<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'categories' => Category::orderBy('name')->get(),
    ]);
})->name('home');

// Solo para visitantes (si ya ingresaste, te lleva al inicio)
Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegisterController::class, 'show'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store']);

    Route::get('/ingresar', [LoginController::class, 'show'])->name('login');
    Route::post('/ingresar', [LoginController::class, 'store']);
});

// Solo para usuarios con sesión y cuenta activa
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::post('/salir', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Solo administradores
    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    });
});
