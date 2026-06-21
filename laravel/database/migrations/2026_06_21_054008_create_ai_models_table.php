<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version')->nullable();
            $table->json('class_names');
            $table->string('file_path');
            $table->float('accuracy')->nullable();
            $table->float('loss')->nullable();
            $table->boolean('is_active')->default(false);
            $table->foreignId('training_job_id')->nullable()->constrained('training_jobs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
