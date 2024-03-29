<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('restaurant_id');
            $table->integer('product_id');
            $table->integer('order_count')->default(1);
            $table->integer('client_id')->nullable();
            $table->integer('assigned_table_id')->nullable();
            $table->enum('status', ['open', 'done', 'cancel', 'ignore'])->default('open');
            $table->text('comment')->nullable();
            $table->integer('final_payment_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
