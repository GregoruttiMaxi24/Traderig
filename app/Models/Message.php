<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
   protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
    ];



    /**
     * 
    * Relacionar el mensaje con la conversación a la que pertenece
    */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

 /**
 * Relaciona el mensaje con el usuario que lo escribió.
 * 
* Se indica 'sender_id' para aclarar porque la columna no se llama user_id. 
*
*/
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}
