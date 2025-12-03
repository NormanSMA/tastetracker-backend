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
        Schema::table('areas', function (Blueprint $table) {
            $table->string('prefix', 5)->after('name'); // Prefijo para las mesas (S, T, B, etc.)
            $table->integer('total_tables')->default(0)->after('prefix'); // Número total de mesas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn(['prefix', 'total_tables']);
        });
    }
};
