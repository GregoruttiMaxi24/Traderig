<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'listing_id',
        'buyer_id',
        'seller_id',
    ];

    /**
     * relacionar la conversación con la publicación sobre la que se consulta
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

  /**
     * Relaciona la conversación con el usuario interesado en la publicación.
     * 
     * 
     * Se aclara 'buyer_id' porque la columna no se llama user_id.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }  



    /**
     * 
     * Se relaciona la conversación con el usuario que publicó el componente.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }


     /**
     * Obtiene los mensajes de la conversación.
     * 
     * lo hace del más viejo al más nuevo.
     * 
     */
        public function messages(): HasMany
        {
            return $this->hasMany(Message::class)->orderBy('id');
        }

    /**
     * Indica si el usuario es el comprador o el vendedor en la conversación.
     * Sirve para que nadie pueda leer ni escribir en chats ajenos.
     * 
    */
    public function hasParticipant(User $user): bool
    {
        return $user->id === $this->buyer_id || $user->id === $this->seller_id;
    }    
}
