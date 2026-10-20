<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Listing extends Model
{
    public const CONDITIONS = [
        'new' => 'Nuevo',
        'like_new' => 'Como nuevo',
        'used' => 'Usado',
        'for_parts' => 'Para repuestos',
    ];

    public const TYPES = [
        'sale' => 'Venta',
        'auction' => 'Subasta',
    ];

    public const STATUSES = [
        'active' => 'Activa',
        'paused' => 'Pausada',
        'closed' => 'Cerrada',
        'blocked' => 'Bloqueada',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'brand',
        'condition',
        'type',
        'price',
        'accepts_trade',
        'city',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'accepts_trade' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('sort_order');
    }

    public function mainPhoto(): HasOne
    {
        return $this->hasOne(ListingPhoto::class)->where('is_main', true);
    }

    public function auction(): HasOne
    {
        return $this->hasOne(Auction::class);
    }

    // Ofertas de trueque recibidas por esta publicación.
    public function tradeOffers(): HasMany
    {
        return $this->hasMany(TradeOffer::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isAuction(): bool
    {
        return $this->type === 'auction';
    }

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition] ?? $this->condition;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
