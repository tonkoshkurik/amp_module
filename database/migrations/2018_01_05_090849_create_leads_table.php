<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 250);
            $table->string('email', 255);
            $table->string('phone', 30)->nullable();
            $table->string('season', 255)->nullable();
            $table->string('package', 255)->nullable();
            $table->string('code', 150)->nullable();
            $table->string('payment_type', 255)->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('payed')->nullable();
            $table->integer('lead_id')->nullable();
            $table->integer('contact_id')->nullable();
//            $table->text('fields')->nullable();
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
        Schema::dropIfExists('leads');
    }
}
