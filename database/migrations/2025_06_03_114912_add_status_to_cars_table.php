<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCarsTable extends Migration
{
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->enum('status', ['available', 'sold'])->default('available')->after('price');
        });
    }

    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}