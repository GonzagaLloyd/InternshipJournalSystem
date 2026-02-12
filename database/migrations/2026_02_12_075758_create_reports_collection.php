<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MongoDB collections are created automatically when first document is inserted
        // This migration creates indexes for better query performance
        
        $collection = \DB::connection('mongodb')
            ->getCollection('reports');
        
        // Create indexes for common queries
        $collection->createIndex(['user_id' => 1]); // Index for user's reports
        $collection->createIndex(['created_at' => -1]); // Index for sorting by date (descending)
        $collection->createIndex(['deleted_at' => 1]); // Index for soft deletes
        $collection->createIndex(['user_id' => 1, 'deleted_at' => 1]); // Compound index for vault queries
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the collection
        \DB::connection('mongodb')
            ->getCollection('reports')
            ->drop();
    }
};
