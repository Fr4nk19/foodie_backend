<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Roles:
     *   - super_admin: Platform administrator, has access to all companies.
     *                  company_id and branch_id are NULL.
     *   - company_admin: Owner/admin of a single company, sees all its branches.
     *                    company_id is set, branch_id is NULL.
     *   - branch_manager: Manager scoped to a specific branch.
     *                     company_id and branch_id are both set.
     *   - employee: Staff member scoped to a specific branch.
     *               company_id and branch_id are both set.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'company_admin', 'branch_manager', 'employee'])
                  ->default('employee')
                  ->after('email');

            $table->foreignId('company_id')
                  ->nullable()
                  ->after('role')
                  ->constrained('companies')
                  ->nullOnDelete();

            $table->foreignId('branch_id')
                  ->nullable()
                  ->after('company_id')
                  ->constrained('branches')
                  ->nullOnDelete();

            $table->boolean('is_active')->default(true)->after('branch_id');

            $table->index(['company_id', 'role']);
            $table->index(['branch_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'role']);
            $table->dropIndex(['branch_id', 'role']);
            $table->dropConstrainedForeignId('company_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
