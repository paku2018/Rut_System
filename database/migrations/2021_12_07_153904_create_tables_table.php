<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->integer('restaurant_id');
            $table->integer('t_number');
            $table->string('name');
            $table->enum('status', ['open', 'closed', 'pend'])->default('closed');
            $table->integer('current_client_id')->nullable();
            $table->enum('type', ['real', 'delivery'])->default('real');
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
        Schema::dropIfExists('tables');
    }
}
