<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeOffer extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'listing_id',
        'user_id',
        'offered_listing_id',
        'message',
        'cash_amount',
        'cash_direction',
    ];

    protected function casts(): array
    {
        return [
            'cash_amount' => 'decimal:2',
            'responded_at' => 'datetime',
        ];
    }

    // Publicación que se quiere conseguir (del dueño que recibe la oferta).
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    // Quien hace la oferta.
    public function offerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Publicación que el oferente entrega a cambio (puede ser null si solo ofrece dinero).
    public function offeredListing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'offered_listing_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
