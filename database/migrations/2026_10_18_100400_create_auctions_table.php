<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Una publicación de tipo "auction" tiene exactamente una subasta.
    // current_price se guarda acá para poder bloquear la fila con lockForUpdate() al pujar.
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('starting_price', 12, 2);
            $table->decimal('min_increment', 12, 2)->default(100);
            $table->decimal('current_price', 12, 2);
            $table->dateTime('ends_at');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
