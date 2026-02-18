<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->integer('current_question_index')->default(0)->after('question_order');
            $table->string('session_token')->nullable()->after('current_question_index');
        });
    }

    public function down()
    {
        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->dropColumn(['current_question_index', 'session_token']);
        });
    }
};

