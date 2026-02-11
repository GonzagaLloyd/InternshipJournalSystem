<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'journal_entries';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'video',
        'audio',
        'file',
        'entry_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
