<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    protected $fillable = [
        'listing_id',
        'starting_price',
        'min_increment',
        'current_price',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'min_increment' => 'decimal:2',
            'current_price' => 'decimal:2',
            'ends_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->orderByDesc('amount');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->ends_at->isFuture();
    }

    // Monto mínimo de la próxima puja (en la Fase 6 se valida dentro de la transacción).
    public function minimumNextBid(): string
    {
        if ($this->bids()->doesntExist()) {
            return $this->starting_price;
        }

        return bcadd($this->current_price, $this->min_increment, 2);
    }
}
