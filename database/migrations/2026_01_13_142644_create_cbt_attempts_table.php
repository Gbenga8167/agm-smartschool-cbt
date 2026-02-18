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
    Schema::create('cbt_attempts', function (Blueprint $table) {
    $table->id();

    $table->foreignId('cbt_test_id')->constrained('cbt_tests')->cascadeOnDelete();
    $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

    $table->integer('score')->nullable();
    $table->timestamp('started_at')->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->integer('duration_used')->nullable();
    $table->string('status')->default('in_progress');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_attempts');
    }
};
