<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    protected $fillable = [
        'model_id', 'name', 'version', 'notes', 'class_names', 'file_path',
        'accuracy', 'loss', 'is_active', 'training_job_id'
    ];

    protected $casts = [
        'class_names' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function trainingJob(): BelongsTo
    {
        return $this->belongsTo(TrainingJob::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
