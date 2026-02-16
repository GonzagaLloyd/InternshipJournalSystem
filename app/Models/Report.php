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
        'report_title',
        'user_name',
        'user_role',
        'company_name',
        'footer_text',
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
