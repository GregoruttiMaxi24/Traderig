<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Publicaciones. "price" es el precio de venta, o el precio base si es subasta.
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 150);
            $table->text('description');
            $table->string('brand', 80)->nullable();
            $table->enum('condition', ['new', 'like_new', 'used', 'for_parts'])->default('used');
            $table->enum('type', ['sale', 'auction'])->default('sale');
            $table->decimal('price', 12, 2);
            $table->boolean('accepts_trade')->default(false);
            $table->string('city', 100)->nullable();
            $table->enum('status', ['active', 'paused', 'closed', 'blocked'])->default('active');
            $table->timestamps();

            $table->index(['status', 'category_id']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
