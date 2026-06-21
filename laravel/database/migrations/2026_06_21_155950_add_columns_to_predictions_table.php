<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->string('prediction_id', 64)->nullable()->after('id');
            $table->string('original_name')->nullable()->after('image_path');
            $table->string('model_label', 64)->nullable()->after('probabilities');
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE predictions MODIFY COLUMN ai_model_id BIGINT UNSIGNED NULL");
        }
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropColumn(['prediction_id', 'original_name', 'model_label']);
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE predictions MODIFY COLUMN ai_model_id BIGINT UNSIGNED NOT NULL");
        }
    }
};
