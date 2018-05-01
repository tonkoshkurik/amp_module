<?php

// New case: 1) We just got only once lead/update without lead/add,
// so we need at first add this lead to Amo("Успешно реализовано")
// and Members in SendPulse


// Second case: 2) Lead just moved to "Успешно реализовано"
// We need to remove from
// Registration book (SendPulse) and
// add to Member Book (SendPulse)


namespace App\Console\Commands;

use Illuminate\Console\Command;
use NikitaKiselev\SendPulse;
use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
use Illuminate\Support\Facades\DB;

class SendPulseTest extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'amo:s';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Testing SendPulse API';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		ini_set('memory_limit', '256M');
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
//      $books = \SendPulse::listAddressBooks();
//      dd($books);

		$api = new Handler('zhirkiller', 'info@zhirkiller.info');

		$status = 19324269;
//          'status'=>1096302,
//			'status'=>1096302,
//	        'status'=>19493775,
//	        'status'=>19512612,
//	        'status'=>19324269, // otpravleni
//		'status'=>19535256, // по иностранцем
		$offset = 0;
		$obj = ['leads'];

		$r = new Request(Request::GET, ['status'=>$status, 'limit_offset'=>$offset, 'limit_rows'=>500], $obj);
		$r->setIfModifiedSince((new \DateTime('2018-04-01'))->format(\DateTime::RFC1123));


		$resp = $api->request($r);


		$response_array = [];


		while ( $resp->result ){
			foreach($resp->result->leads as $lead) {
				$response_array[]  = $lead;
			}
			$offset += 500;
			$r = new Request(Request::GET, ['status'=>$status, 'limit_offset'=>$offset, 'limit_rows'=>500], $obj);
			$r->setIfModifiedSince((new \DateTime('2018-04-01'))->format(\DateTime::RFC1123));
			$resp = $api->request($r);

		}

		if($response_array){

			$leads_array = array();

			foreach($response_array as $lead)
			{
//      	save to Database
				$lead = collect($lead);

				$l = $lead
					->only('id',
						'main_contact_id',
						'date_create',
						'responsible_user_id',
						'status_id',
						'last_modified',
						'custom_fields')
					->map(function($i, $k){
						if($k=='custom_fields'){
							return collect($i)->toJson();
						} else if($k === 'last_modified' or $k === 'date_create') {
							return date('Y-m-d H:i:s', $i);
						} else {
							return $i;
						}
					})
					->keyBy(function ($value, $key) {
						if ($key == 'id') {
							return 'lead_id';
						} else {
							return $key;
						}
					})
					->toArray();

				\App\AmoLead::updateOrCreate(['lead_id'=>$l['lead_id']], $l);

				$leads_array[] = $l;
			}


			$contacts =  collect($leads_array)
				->map(function($i, $k){
//					if(isset($i['main_contact_id'])){
						return $i['main_contact_id'];
//					}
				});

			$amo_contacts = [];

			if($contacts->count() > 500 ){

				$contacts_req = new Request(Request::GET,
					[
						'id' => $contacts->take(500)->toArray()
					],
					['contacts']);

				$contacts_response = $api->request($contacts_req)->result;


				$next_contacts = $contacts->slice(500);

				$i = 0;
				while($contacts_response){
					var_dump($i);
					$i++;
					foreach($contacts_response->contacts as $c) {
						$amo_contacts[]  = $c;
					}


					if($next_contacts->count()){
						$contacts_req = new Request(Request::GET,
							[
								'id' => $next_contacts->take(500)->toArray()
							],
							['contacts']);

						$contacts_response = $api->request($contacts_req)->result;

						$next_contacts = $next_contacts->slice(500);
					} else {
						$contacts_response = false;
					}
				}

			} else {

				$contacts_req = new Request(Request::GET,
					[
						'id' => $contacts->take(500)->toArray()
					],
					['contacts']);

				$contacts_response = $api->request($contacts_req)->result;
				foreach($contacts_response->contacts as $c) {
					$amo_contacts[]  = $c;
				}

			}

			dd($amo_contacts, count($amo_contacts));


		}

	}


	public function sendpulse(\NikitaKiselev\SendPulse\Contracts\SendPulseApi $api)
	{
//        $books = $api->listAddressBooks();
		//      [0] => stdClass Object
		//    (
		//      [id] => 1465050
		//            [name] => ЖК 6 (регистрации)
		//    [all_email_qty] => 0
		//            [active_email_qty] => 0
		//            [inactive_email_qty] => 0
		//            [creationdate] => 2018-01-08 14:43:20
		//            [status] => 0
		//            [status_explain] => Active
		//        )
		//
		//    [1] => stdClass Object
		//    (
		//      [id] => 1465048
		//            [name] => ЖК 6 (участники)
		//    [all_email_qty] => 0
		//            [active_email_qty] => 0
		//            [inactive_email_qty] => 0
		//            [creationdate] => 2018-01-08 14:42:33
		//            [status] => 0
		//            [status_explain] => Active
		//        )
//        $books = \SendPulse::listAddressBooks();

		//

		// Registration Book ID: 1465050
		// Members Book ID: 1465048


		//      $email = array(
		//        array(
		//          "email"=> "tonkoshkurik@gmail.com",
		//          "variables"=> array(
		//            "name"=> "Serg",
		//            "phone" => "+380995591095"
		//          ),
		//        ),
		//      );

		//      add email to Registration Book
		//      $e = $api->addEmails(1465050, $email);

		//      $e = $api->getTemplates();
		//
		//      $e = $api->getTempleData($e[4]->id);
		//
		//
		//      dd(base64_decode($e->body));
	}
}
