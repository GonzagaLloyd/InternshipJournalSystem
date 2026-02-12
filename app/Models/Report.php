<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'reports';

    protected $fillable = [
        'user_id',
        'report',
        'period',
    ];

    protected $casts = [
        'period' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
