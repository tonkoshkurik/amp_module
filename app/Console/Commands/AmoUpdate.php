<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
use NikitaKiselev\SendPulse;
use Illuminate\Support\Facades\Artisan;

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


    /**
     * Обновить лиды в Амо по которым мы получили оплату
     *
     * @return mixed
     */
    public function handle()
    {
      try{

        $api = new Handler(env('AMO_DOMAIN'), env('AMO_LOGIN'));

        $this->api = $api;

//         print_r($this->api->request(new Request(Request::INFO))->result);

        // Here we first should process leads which was payed
        $inleads = \App\Lead::whereNull('status')->whereNotNull('payed')->get();

        if($inleads->count()) {

          usleep(500000);

          Artisan::call('amo:push');

          $i = 0;
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
            $e = \SendPulse::removeEmails(1465050, array($l->email));
//            dd($e);

            // Send to SendPulse Members Book
            $e = \SendPulse::addEmails(1465048, $email);

            // Update status in AMO
            $amo = $api->request(new Request(Request::SET, $lead));

            var_dump($amo);

            $l->status = true;
            $l->save();
            if($i>0){
              usleep(500000);
            }
            $i++;
          }
        }

      } catch (\Exception $e) {
        echo $e->getMessage();
      }

//    public function SendPulseApi(\NikitaKiselev\SendPulse\Contracts\SendPulseApi $sendPulseApi)
//    {
//      return $sendPulseApi;
//    }

    }
}
