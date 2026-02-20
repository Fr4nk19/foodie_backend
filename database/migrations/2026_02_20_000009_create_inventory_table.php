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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('cantidad', 12, 3)->default(0.000)->comment('Cantidad disponible en inventario');
            $table->decimal('cantidad_minima', 12, 3)->nullable()->comment('Alerta de stock mínimo');
            $table->decimal('cantidad_maxima', 12, 3)->nullable()->comment('Capacidad máxima de almacenamiento');
            $table->decimal('costo_unitario', 10, 2)->nullable()->comment('Costo unitario de adquisición');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['branch_id', 'product_id']);
            $table->index(['branch_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
