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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('cat_mh_unidad_de_medida_id')->constrained('cat_mh_unidades_de_medida')->restrictOnDelete();
            $table->string('codigo', 50)->nullable()->comment('SKU o código interno del producto');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->decimal('peso', 10, 3)->nullable()->comment('Peso en kilogramos');
            $table->string('tamanio', 100)->nullable()->comment('Dimensiones o talla (ej: 30x20x10cm, XL, etc.)');
            $table->string('imagen')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('atributos')->nullable()->comment('Atributos adicionales del producto');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'codigo']);
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
