<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Adds cat_mh_departamento_id FK to cat_mh_municipio.
     * - Drops the global unique index on `codigo`.
     * - Adds a composite unique index on (codigo, cat_mh_departamento_id)
     *   so that the same municipality code can exist in different departments.
     */
    public function up(): void
    {
        Schema::table('cat_mh_municipio', function (Blueprint $table) {
            // Remove old global unique constraint on codigo
            $table->dropUnique(['codigo']);

            // Add FK column (nullable for existing rows without a department)
            $table->foreignId('cat_mh_departamento_id')
                ->nullable()
                ->after('id')
                ->constrained('cat_mh_departamento')
                ->nullOnDelete();

            // Composite unique: same codigo can exist per department only
            $table->unique(['cat_mh_departamento_id', 'codigo'], 'municipio_departamento_codigo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_mh_municipio', function (Blueprint $table) {
            $table->dropUnique('municipio_departamento_codigo_unique');
            $table->dropConstrainedForeignId('cat_mh_departamento_id');
            $table->unique('codigo');
        });
    }
};
