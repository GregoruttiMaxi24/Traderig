<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Auction extends Model
{
     protected $fillable = [
        'listing_id',
        'starting_price',
        'min_increment',
        'current_price',
        'ends_at',
        'status',
        'winner_id',
        'closed_at',
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
     * Cierra la subasta si ya venció y todavía figura como abierta.
     * El ganador es quien hizo la puja más alta; si nadie pujó, queda sin ganador.
     * Se usa un bloqueo para que, si dos personas la consultan a la vez, se cierre una sola vez.
     */
    public function closeIfExpired(): void
    {
        if ($this->status !== 'open' || $this->ends_at->isFuture()) {
            return;
        }

        DB::transaction(function () {
            $locked = static::lockForUpdate()->findOrFail($this->id);

            if ($locked->status === 'open') {
                // bids() ya viene ordenadas de mayor a menor monto
                                $topBid = $locked->bids()->first();

                // Si nadie pujó, no hay ganador.
                $winnerId = null;

                if ($topBid !== null) {
                    $winnerId = $topBid->user_id;
                }

                $locked->update([
                    'status' => 'closed',
                    'winner_id' => $winnerId,
                    'closed_at' => now(),
                ]);
            }
        });

        // Se vuelve a leer para que este objeto tenga los datos ya cerrados
        $this->refresh();
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
