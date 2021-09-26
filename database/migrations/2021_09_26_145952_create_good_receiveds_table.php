<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodReceivedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_receiveds', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('customer_id');
            $table->uuid('delivery_price_id');
            $table->integer('type_deliver');
            $table->string('buyers_address');
            $table->string('buyers_name');
            $table->string('buyers_phone1');
            $table->string('buyers_phone2')->nullable();
            $table->string('content');
            $table->text('note')->nullable();
            $table->integer('quantity');
            $table->double('price');
            $table->integer('order_status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_receiveds');
    }
}