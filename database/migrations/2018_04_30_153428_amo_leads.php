<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmoLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('amo_leads', function (Blueprint $table) {
    		$table->bigInteger('id', true, true);
    		$table->bigInteger('lead_id');
    		$table->bigInteger('main_contact_id');
    		$table->integer('responsible_user_id');
			$table->integer('status_id');
			$table->timestamp('date_create')->nullable();
			$table->timestamp('last_modified')->nullable();
			$table->text('custom_fields');

	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	    Schema::dropIfExists('amo_leads');
    }
}
