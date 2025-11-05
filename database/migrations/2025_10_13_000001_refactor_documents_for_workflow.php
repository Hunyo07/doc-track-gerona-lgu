<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Add hold fields if they don't exist
            if (!Schema::hasColumn('documents', 'hold_reason')) {
                $table->text('hold_reason')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('documents', 'hold_at')) {
                $table->timestamp('hold_at')->nullable()->after('hold_reason');
            }
        });
        
        // Add indexes separately to avoid conflicts
        DB::statement('CREATE INDEX IF NOT EXISTS documents_status_workflow_index ON documents (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS documents_current_department_workflow_index ON documents (current_department_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS documents_status_department_workflow_index ON documents (status, current_department_id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS documents_status_workflow_index ON documents');
        DB::statement('DROP INDEX IF EXISTS documents_current_department_workflow_index ON documents');
        DB::statement('DROP INDEX IF EXISTS documents_status_department_workflow_index ON documents');
        
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['hold_reason', 'hold_at']);
        });
    }
};