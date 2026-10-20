<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Oferta de trueque: user_id ofrece "offered_listing_id" a cambio de "listing_id".
    // cash_amount es la diferencia de dinero opcional y cash_direction indica quién la paga.
    public function up(): void
    {
        Schema::create('trade_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offered_listing_id')->nullable()->constrained('listings')->nullOnDelete();
            $table->text('message')->nullable();
            $table->decimal('cash_amount', 12, 2)->default(0);
            $table->enum('cash_direction', ['offerer_pays', 'owner_pays'])->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_offers');
    }
};
