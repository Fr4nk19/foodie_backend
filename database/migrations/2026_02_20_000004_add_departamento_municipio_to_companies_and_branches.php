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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('cat_mh_departamento_id')
                  ->nullable()
                  ->after('state')
                  ->constrained('cat_mh_departamento')
                  ->nullOnDelete();

            $table->foreignId('cat_mh_municipio_id')
                  ->nullable()
                  ->after('cat_mh_departamento_id')
                  ->constrained('cat_mh_municipio')
                  ->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('cat_mh_departamento_id')
                  ->nullable()
                  ->after('state')
                  ->constrained('cat_mh_departamento')
                  ->nullOnDelete();

            $table->foreignId('cat_mh_municipio_id')
                  ->nullable()
                  ->after('cat_mh_departamento_id')
                  ->constrained('cat_mh_municipio')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['cat_mh_municipio_id']);
            $table->dropForeign(['cat_mh_departamento_id']);
            $table->dropColumn(['cat_mh_municipio_id', 'cat_mh_departamento_id']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['cat_mh_municipio_id']);
            $table->dropForeign(['cat_mh_departamento_id']);
            $table->dropColumn(['cat_mh_municipio_id', 'cat_mh_departamento_id']);
        });
    }
};
