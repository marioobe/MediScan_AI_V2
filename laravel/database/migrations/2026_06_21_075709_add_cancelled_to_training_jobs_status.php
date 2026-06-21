<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE training_jobs MODIFY COLUMN status ENUM('pending', 'validating', 'extracting', 'training', 'completed', 'failed', 'cancelled') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE training_jobs MODIFY COLUMN status ENUM('pending', 'validating', 'extracting', 'training', 'completed', 'failed') DEFAULT 'pending'");
        }
    }
};
