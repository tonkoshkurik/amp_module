<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;

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

        $api = new Handler('zhirkiller', 'info@zhirkiller.info');

        $this->api = $api;
//         print_r($this->api->request(new Request(Request::INFO))->result);

        // Here we first should process leads which was payed
        $inleads = \App\Lead::whereNull('status')->whereNotNull('payed')->get();

        if($inleads->count()) {
          foreach ($inleads as $l) {
            $lead = new Lead();
//            dd($l, $lead);
            $lead->setUpdate($l->lead_id, time() + 1)
              ->setStatusId(142);
            $this->api->request(new Request(Request::SET, $lead));
            $l->status = true;
            $l->save();
          }
        }

      } catch (\Exception $e) {
        echo $e->getMessage();
      }

    }
}
