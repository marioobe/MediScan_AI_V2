<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('dataset_path');
            $table->enum('status', ['pending', 'validating', 'extracting', 'training', 'completed', 'failed'])->default('pending');
            $table->integer('current_epoch')->nullable();
            $table->integer('total_epoch')->nullable();
            $table->float('progress_percent')->nullable();
            $table->float('accuracy_result')->nullable();
            $table->float('loss_result')->nullable();
            $table->text('error_message')->nullable();
            $table->text('log')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_jobs');
    }
};
