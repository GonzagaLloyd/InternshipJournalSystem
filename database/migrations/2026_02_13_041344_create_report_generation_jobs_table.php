<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_generation_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->json('entry_ids');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->longText('report')->nullable();
            $table->json('period')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_generation_jobs');
    }
};
