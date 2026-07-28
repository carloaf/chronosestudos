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
        Schema::create('study_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->unique()->constrained()->cascadeOnDelete();
            $table->dateTime('last_studied_at')->nullable();
            $table->date('next_review_at');
            $table->unsignedSmallInteger('interval_days')->default(1);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_schedules');
    }
};
