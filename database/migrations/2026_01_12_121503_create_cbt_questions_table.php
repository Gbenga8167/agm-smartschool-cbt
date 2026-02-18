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
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id();
            
            //Link to the test this question belongs to
            $table->unsignedBigInteger('cbt_test_id');

            //The question text
            $table->text('question_text');

            //Multiple choice options
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();

            //store the correct option (e.g. a, b, c or d)
            $table->enum('correct_option', [
                'a', 'b', 'c', 'd'
            ]);

            //mark assigned to this question 
            $table->integer('mark')->default(1);

            //foreign key
            $table->foreign('cbt_test_id')->references('id')->on('cbt_tests')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_questions');
    }
};
