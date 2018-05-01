<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreateAmoContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amo_contacts', function (Blueprint $table) {
            //
	        $table->bigInteger('id')->unique();
	        // id, name, email, phone, fields
	        $table->string('name');
	        $table->string('email');
	        $table->string('phone');
	        $table->text('fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amo_contacts', function (Blueprint $table) {
            //
        });
    }
}
