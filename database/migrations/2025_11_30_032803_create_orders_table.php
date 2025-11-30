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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('waiter_id')->constrained('users'); // Empleado
            
            // --- NUEVO: Ubicación del pedido ---
            $table->foreignId('area_id')->nullable()->constrained(); // Relación con Áreas
            $table->string('table_number')->nullable(); // Ej: "Mesa 4" o "T-01"
            // -----------------------------------

            $table->enum('status', ['pending', 'preparing', 'ready', 'served', 'paid', 'cancelled'])->default('pending');
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery'])->default('dine_in');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
