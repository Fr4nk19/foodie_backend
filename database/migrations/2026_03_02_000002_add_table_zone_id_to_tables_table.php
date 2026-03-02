<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Nullable: existing tables without a zone keep working
            $table->foreignId('table_zone_id')
                  ->nullable()
                  ->after('branch_id')
                  ->constrained('table_zones')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['table_zone_id']);
            $table->dropColumn('table_zone_id');
        });
    }
};
