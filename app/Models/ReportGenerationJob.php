<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ReportGenerationJob extends Model
{
    protected $fillable = [
        'user_id',
        'entry_ids',
        'status',
        'report',
        'period',
        'error_message',
        'completed_at'
    ];

    protected $casts = [
        'entry_ids' => 'array',
        'period' => 'array',
        'completed_at' => 'datetime',
    ];
}
