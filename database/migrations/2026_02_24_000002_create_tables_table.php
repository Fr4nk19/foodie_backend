<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->integer('number');
            $table->integer('capacity')->default(4);
            $table->string('zone', 100)->default('Interior');
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['branch_id', 'number']);
            $table->index(['branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
