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
        Schema::create('cbt_tests', function (Blueprint $table) {
            $table->id();
                        //Foreign keys to associate test with class, subject, and teacher.
            $table->unsignedBigInteger('student_classes_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable(); // 👈 allow null for admin

            //Title of the CBT e.g. Js2 Mathematics 1st Test.
            $table->string('title');

            //Term and Session stored as plain strings.
            $table->string('term');
            $table->string('session');

            //Test type e.g. 'test1, test2, exam. 
            $table->string('assessment_type');

            //duration of the test in minutes(e.g., 30 minutes)
            $table->integer('duration_minutes')->default(30);

            //optional start and end time for the test.
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            
            $table->timestamps();

            //setup foreign key constraints(optinal for now)
            $table->foreign('student_classes_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_tests');
    }
};
