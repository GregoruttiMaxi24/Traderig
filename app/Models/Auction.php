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

    /**
     * Indica cómo se convierten los campos al leerlos de la base de datos.
     * Los montos son números enteros, en pesos, y las fechas se tratan como fechas.
     */
    protected function casts(): array
    {
        return [
            'starting_price' => 'integer',
            'min_increment' => 'integer',
            'current_price' => 'integer',
            'ends_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

/**
* Relaciona la subasta con la publicación correspondiente
*/
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
    * obtiene las ofertas de la subasta, ordenadas de mayor a menor monto
    */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->orderByDesc('amount');
    }

/**
* se encarga de relacionar la subasta con el usuario ganador.
*/
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Compruebar si la subasta está abierta y todavía no ha finalizado.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->ends_at->isFuture();
    }


     /**
     * Calcula cuánto hay que ofertar como mínimo en la próxima puja.
     * Si todavía no hay pujas es el precio inicial; si ya hay, es la puja actual más el incremento mínimo.
     */
    public function minimumNextBid(): int
    {
        if ($this->bids()->doesntExist()) {
            return $this->starting_price;
        }

        return $this->current_price + $this->min_increment;
    }
}
