<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_model_id')->nullable()->constrained('ai_models')->onDelete('cascade');
            $table->string('image_path');
            $table->string('predicted_class');
            $table->float('confidence');
            $table->json('probabilities');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
