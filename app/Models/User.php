<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    // "role" e "is_blocked" NO están en $fillable a propósito: un usuario no debe
    // poder asignarse el rol admin desde un formulario. Se cambian de forma explícita.
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'city',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    // Ofertas de trueque que este usuario hizo a publicaciones de otros.
    public function tradeOffers(): HasMany
    {
        return $this->hasMany(TradeOffer::class);
    }

    public function wonAuctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }
}
