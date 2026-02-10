<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    protected $fillable = [
        '_id',
        'user_id',
        'name',
        'completed',
        'due_date',
        'priority', // low, medium, high
    ];

    /**
     * The attributes that should be visible in serialization.
     * Explicitly include _id for MongoDB.
     */
    protected $visible = [
        '_id',
        'id',
        'user_id',
        'name',
        'completed',
        'due_date',
        'priority',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
    ];

    /**
     * Get the route key name for Laravel route model binding.
     * This tells Laravel to use '_id' instead of 'id' for MongoDB.
     */
    public function getRouteKeyName()
    {
        return '_id';
    }

    /**
     * Convert the model instance to an array.
     * Ensures _id is properly serialized as a string and creates an 'id' alias.
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Ensure both _id and id are available as strings
        if (isset($array['_id'])) {
            $array['_id'] = (string) $array['_id'];
            $array['id'] = $array['_id'];
        }
        
        return $array;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
