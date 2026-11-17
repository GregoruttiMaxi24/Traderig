<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de conversaciones.
     * 
     *Cada conversación pertenece a una publicación y une al comprador interesado con el vendedor
     * Un mismo comprador solo puede tener una conversación por publicación.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete(); //tabla listings
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete(); // fk comprador que apunta al id de la tabla users
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete(); // fk vendedor
            $table->timestamps();

            $table->unique(['listing_id', 'buyer_id']);  //Para evittar que se creen conversaciones duplicadas si el usuario hace clic varias veces en el botón del chat sobre el mismo producto.
        });
    }

    /**
     * Elimina la tabla de conversaciones
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
