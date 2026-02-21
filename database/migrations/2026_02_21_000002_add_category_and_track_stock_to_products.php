<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Categoría de producto por empresa (opcional)
            $table->foreignId('product_category_id')
                  ->nullable()
                  ->after('company_id')
                  ->constrained('product_categories')
                  ->nullOnDelete()
                  ->comment('Categoría del producto dentro de la empresa');

            // Indica si este producto lleva control de inventario.
            // false = se puede vender/guardar sin necesidad de stock (ej: platos de comida preparada).
            $table->boolean('track_stock')
                  ->default(true)
                  ->after('status')
                  ->comment('Si es false, el producto no requiere control de stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropColumn(['product_category_id', 'track_stock']);
        });
    }
};
