<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
use NikitaKiselev\SendPulse;

class AmoUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amo:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status for payed lead';

    public $api;

    public $SendPulse;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function SendPulseApi(\NikitaKiselev\SendPulse\Contracts\SendPulseApi $sendPulseApi)
    {
      return $sendPulseApi;
    }

    /**
     * Обновить лиды в Амо по которым мы получили оплату
     *
     * @return mixed
     */
    public function handle()
    {
      try{

        $api = new Handler('zhirkiller', 'info@zhirkiller.info');

        $this->api = $api;

//         print_r($this->api->request(new Request(Request::INFO))->result);

        // Here we first should process leads which was payed
        $inleads = \App\Lead::whereNull('status')->whereNotNull('payed')->get();

        var_dump($inleads);

        if($inleads->count()) {
          $this->api = $api;
          foreach ($inleads as $l) {
            $lead = new Lead();
//            dd($l, $lead);
            $lead->setUpdate($l->lead_id, time() + 1)
              ->setStatusId(142);

            $email = array(
              array(
                "email"=> $l->email,
                "variables"=> array(
                  "name"=> $l->name,
                  "phone" => $l->phone,
                ),
              ),
            );

            // Delete from SendPulse
            $e = \SendPulse::removeEmails(1465050, $email);

            // Send to SendPulse Members Book
            $e = \SendPulse::addEmails(1465048, $email);

            // Update status in AMO
            $amo = $this->api->request(new Request(Request::SET, $lead));

            var_dump($amo);

            $l->status = true;
            $l->save();
          }
        }

      } catch (\Exception $e) {
        echo $e->getMessage();
      }

    }
}
