<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayedToDriverssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('good_receiveds', function (Blueprint $table) {
            $table->boolean('paid_company')->default(false);
            $table->boolean('paid_customer')->default(false);;
            $table->boolean('paid_driver')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('good_receiveds', function (Blueprint $table) {
            Schema::dropColumn('paid_company');
            Schema::dropColumn('paid_customer');
            Schema::dropColumn('paid_driver');
        });
    }
}