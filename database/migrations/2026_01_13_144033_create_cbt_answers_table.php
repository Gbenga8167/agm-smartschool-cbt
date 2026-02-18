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
        Schema::create('cbt_answers', function (Blueprint $table) {
    $table->id();

    $table->foreignId('cbt_attempt_id')->constrained('cbt_attempts')->cascadeOnDelete();
    $table->foreignId('cbt_question_id')->constrained('cbt_questions')->cascadeOnDelete();

    $table->enum('selected_option', ['a', 'b', 'c', 'd']);
    $table->boolean('is_correct')->default(false);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_answers');
    }
};

