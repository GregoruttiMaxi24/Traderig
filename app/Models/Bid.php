<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'amount'];


    /**
     * Indica cómo se convierte el monto al leerlo de la base de datos (entero, en pesos).
     */
    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }

/**
 * Relaciona la publicación con su subasta correspondiente
 */    
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
    * Relaciona la publicación con el usuario que la creó
   */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
