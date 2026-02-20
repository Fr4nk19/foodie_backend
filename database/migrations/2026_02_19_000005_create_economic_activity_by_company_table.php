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
        // Limpiar tabla rota de intentos anteriores fallidos
        Schema::dropIfExists('economic_activity_by_company');

        Schema::create('economic_activity_by_company', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->cascadeOnDelete();
            $table->foreignId('cat_mhactividad_id')
                  ->constrained('cat_mh_actividades_economicas')
                  ->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Nombre corto para respetar límite de 64 chars de MySQL
            $table->unique(
                ['company_id', 'cat_mhactividad_id', 'deleted_at'],
                'eabc_company_actividad_deleted_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_activity_by_company');
    }
};
