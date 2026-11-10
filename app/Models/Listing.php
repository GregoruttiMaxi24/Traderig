<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Listing extends Model
{
    /**
     * Opciones disponibles para el estado de conservación de los productos.
     */
    public const CONDITIONS = [
        'new' => 'Nuevo',
        'like_new' => 'Como nuevo',
        'used' => 'Usado',
        'for_parts' => 'Para repuestos',
    ];

    /**
     * Se encarga de devolver la etiqueta correspondiente al tipo de publicación ('Venta' o 'Subasta')
     */
    public const TYPES = [
        'sale' => 'Venta',
        'auction' => 'Subasta',
    ];

    /**
     * Los estados posibles de una publicación.
     */
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
        'seller_notes',
        'specifications',
        'condition',
        'type',
        'price',
        'accepts_trade',
        'city',
    ];

    /**
     * Indica cómo se convierten los campos al leerlos de la base de datos.
     * El precio se maneja como número entero, en pesos.
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'accepts_trade' => 'boolean',
            'specifications' => 'array',
        ];
    }

    /**
     * Sirve para relacionar la publicación con el usuario que la creó.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Se encarga de relacionar la publicación con su categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * obtener las fotos de la publicación y se ordena según su posición.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('sort_order');
    }

    /**
     * se encarga de obtener la foto principal de la publicación
     */
    public function mainPhoto(): HasOne
    {
        return $this->hasOne(ListingPhoto::class)->where('is_main', true);
    }

    /**
     * Obtiene la relación de la subasta asociada a esta publicación.
     */
    public function auction(): HasOne
    {
        return $this->hasOne(Auction::class);
    }

    // Ofertas de trueque recibidas por esta publicación.
    public function tradeOffers(): HasMany
    {
        return $this->hasMany(TradeOffer::class);
    }

    /**
     *  Filtra las publicaciones que se encuentran activas.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Sirve para comprobar si la publicación corresponde a una subasta
     */
    public function isAuction(): bool
    {
        return $this->type === 'auction';
    }

    /**
     * Obtiene el texto legible del estado del artículo (ej: Nuevo, Usado, Excelente).
     *
     * Traduce el código interno de conservación a su etiqueta comercial correspondiente.
     * Si el código no está registrado, devuelve el valor original como alternativa.
     *
     * @return string Nombre descriptivo del estado de conservación.
     */
    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition] ?? $this->condition;
    }

    /**
     * Obtiene el texto legible del tipo de publicación (ej: Subasta, Venta directa).
     *
     * Traduce el código interno del tipo a su etiqueta correspondiente para el usuario.
     * Si el código no está registrado, devuelve el valor original como alternativa.
     *
     * @return string Nombre descriptivo del tipo de publicación.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
