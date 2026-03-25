<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadmDetectionLabel extends Model
{
    protected $fillable = [
        'detection_id',
        'actual_random_answering',
        'label_source',
        'labeled_by',
        'labeled_at',
        'notes',
    ];

    protected $casts = [
        'actual_random_answering' => 'boolean',
        'labeled_at' => 'datetime',
    ];

    /**
     * The RADM detection this label belongs to.
     */
    public function detection(): BelongsTo
    {
        return $this->belongsTo(RadmDetection::class, 'detection_id');
    }

    /**
     * The user who assigned the label, when available.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'labeled_by');
    }
}
