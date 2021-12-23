<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('restaurant_id');
            $table->integer('table_id');
            $table->integer('client_id')->nullable();
            $table->float('consumption');
            $table->string('tip')->nullable();
            $table->string('shipping')->nullable();
            $table->tinyInteger('payment_method');
            $table->tinyInteger('document_type')->default(1);
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
        Schema::dropIfExists('payments');
    }
}
