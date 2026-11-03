<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // pasa los montos de $ de decimal(12, 2) a entero ($ enteros, sin centavos).
    // Se hace en una migración nueva porque las originales ya están subidas al repositorio.
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->unsignedInteger('price')->change();
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->unsignedInteger('starting_price')->change();
            $table->unsignedInteger('min_increment')->default(100)->change();
            $table->unsignedInteger('current_price')->change();
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->unsignedInteger('amount')->change();
        });

        Schema::table('trade_offers', function (Blueprint $table) {
            $table->unsignedInteger('cash_amount')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change();
        });

        Schema::table('auctions', function (Blueprint $table) {
            $table->decimal('starting_price', 12, 2)->change();
            $table->decimal('min_increment', 12, 2)->default(100)->change();
            $table->decimal('current_price', 12, 2)->change();
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change();
        });

        Schema::table('trade_offers', function (Blueprint $table) {
            $table->decimal('cash_amount', 12, 2)->default(0)->change();
        });
    }
};