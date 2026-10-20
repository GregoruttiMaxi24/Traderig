<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // No tocamos la migración original de users: agregamos columnas con una nueva.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->string('phone', 30)->nullable()->after('role');
            $table->string('city', 100)->nullable()->after('phone');
            $table->boolean('is_blocked')->default(false)->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'city', 'is_blocked']);
        });
    }
};
