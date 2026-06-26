<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_jobs', function (Blueprint $table) {
            $table->float('precision_result')->nullable()->after('loss_result');
            $table->float('recall_result')->nullable()->after('precision_result');
            $table->float('f1_score_result')->nullable()->after('recall_result');
        });
    }

    public function down(): void
    {
        Schema::table('training_jobs', function (Blueprint $table) {
            $table->dropColumn(['precision_result', 'recall_result', 'f1_score_result']);
        });
    }
};
