<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('academic_sessions', function (Blueprint $table) {
        $table->unsignedInteger('test_limit')->default(50); // default 50
    });
}

public function down()
{
    Schema::table('academic_sessions', function (Blueprint $table) {
        $table->dropColumn('test_limit');
    });
}

};
