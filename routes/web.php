<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TradeOfferController;
use App\Http\Controllers\UserProfileController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Detalle de publicación y página pública del vendedor.
Route::get('/publicaciones/{listing}', [ListingController::class, 'show'])
    ->whereNumber('listing')->name('listings.show');
Route::get('/subastas/{auction}/estado', [AuctionController::class, 'state'])
    ->whereNumber('auction')->name('auctions.state');
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

    Route::get('/publicaciones/crear', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/publicaciones', [ListingController::class, 'store'])->name('listings.store');

    //El nombre 'index' queda para el catalogo que muestra todos los productos. -("mine" = mio) 
    Route::get('/mis-publicaciones', [ListingController::class, 'myListings'])
        ->name('listings.mine');

    // -- pujas en subastas ---
    Route::post('/subastas/{auction}/pujas', [BidController::class, 'store'])
        ->whereNumber('auction')->name('auctions.bids.store');

 // ----Chat privado -------------
Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');     

Route::get('/chats/{conversation}', [ChatController::class, 'show'])
        ->whereNumber('conversation')
        ->name('chats.show');

Route::get('/chats/{conversation}/mensajes', [ChatController::class, 'messages'])
        ->whereNumber('conversation')
        ->name('chats.messages');   
    
Route::post('/chats/{conversation}/mensajes', [ChatController::class, 'send'])
        ->whereNumber('conversation')
        ->name('chats.send');        


// -- intercambios ---
Route::get('/publicaciones/{listing}/intercambio', [TradeOfferController::class, 'create'])
    ->whereNumber('listing')
    ->name('trade-offers.create');    

 Route::post('/publicaciones/{listing}/intercambio', [TradeOfferController::class, 'store'])
    ->whereNumber('listing')
    ->name('trade-offers.store');   

    // Solo administradores
    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    });
});
