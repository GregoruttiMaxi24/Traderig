<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Detalle de publicación y página pública del vendedor.
// whereNumber evita choques con rutas como /publicaciones/crear (Fase 4).
Route::get('/publicaciones/{listing}', [ListingController::class, 'show'])
    ->whereNumber('listing')->name('listings.show');
Route::get('/vendedores/{user}', [UserProfileController::class, 'show'])
    ->whereNumber('user')->name('users.show');

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
