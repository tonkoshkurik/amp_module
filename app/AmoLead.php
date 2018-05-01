<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmoLead extends Model
{
    //
	protected $table = 'amo_leads';
	protected $fillable = [
		'lead_id', 'main_contact_id', 'date_create',
		'responsible_user_id', 'status_id',
		'last_modified', 'custom_fields'];
}
