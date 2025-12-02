<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Agregar campo guest_name después de user_id
            $table->string('guest_name')->nullable()->after('user_id');

            // Hacer user_id nullable (ya lo es, pero lo confirmamos)
            // Si ya es nullable, esto no causará error
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Eliminar campo guest_name
            $table->dropColumn('guest_name');
        });
    }
};
