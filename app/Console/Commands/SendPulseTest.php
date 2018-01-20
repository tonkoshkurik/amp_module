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
       $webhook = array (
        'leads' =>
          array (
            'status' =>
              array (
                0 =>
                  array (
                    'id' => '19587494', //'8372949',
                    'status_id' => '142',
                    'pipeline_id' => '917788',
                    'old_status_id' => '17617822',
                    'old_pipeline_id' => '917788',
                  ),
              ),
          ),
        'account' =>
          array (
            'id' => '17617810',
            'subdomain' => 'jediservice',
          ),
      );

      $lead_id = $webhook["leads"]["status"][0]["id"];
      // after we know leadID we should ask about this lead

//      $api = new Handler('jediservice', 'tonkoshkurik@gmail.com');
      $api = new Handler('zhirkiller', 'info@zhirkiller.info');

//      $lead = new Lead();
//      $lead->id = $lead_id;
      $r = $api->request(new Request(Request::GET, ['id'=>$lead_id], ['leads'] ));
      $contact_id = $r->result->leads[0]->main_contact_id;
      $r = $api->request(new Request(Request::GET, ['id'=>$contact_id], ['contacts'] ));
//      dd($r->result->contacts[0]->name);
      $array = json_decode(json_encode($r->result->contacts[0]->custom_fields), true);
      dd($r->result->contacts[0]->custom_fields);

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
