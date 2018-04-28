<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
//use \AmoCRM\Note;
//use \AmoCRM\Task;
use NikitaKiselev\SendPulse\SendPulse;

class AmoPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amo:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send leads to Amo';

    public $api;

    private $settings;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $amo_conf = config('app.amo');
        $this->settings = $amo_conf['mg2'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      try{
        $api = new Handler(env('AMO_DOMAIN'), env('AMO_LOGIN'));

        $this->api = $api;
//         print_r($this->api->request(new Request(Request::INFO))->result);

        // Here we first should process leads which wasn't payed
        $inleads = \App\Lead::whereNull('status')->whereNull('payed')->get();
//        dd($this->settings);
        // if we have some leads, let's try push them
        if($inleads->count()){
          foreach ($inleads as $l) {
            $lead = new Lead();
            $lead
              /* Название сделки */
              ->setName('Заявка №' . $l->id )
              /* Назначаем ответственного менеджера */
              // @todo Need to replace $this->api->config in to My Settings CRUD
              // let's go
                // 1) I have class with settings
                // 2) I have data about my acc about fields, and enteties
                // 3)
                // In settings I need to have ResponsibleUserID
                // LeadPipelineStatusID => first stage
                // LeadFieldPackage
                // LeadFieldPackageCode
                // Payment [
                //        'p1' => 'карта',
                //        'p2' => 'приват24'
                //        'p4' => 'na kartu'
                //  ];
                // Oplata nalichnimi
                //
  //                 if(trim($l->payment) == 'p4'){
  //                   $lead
  //                     ->setStatusId(17903028) // Status for Leads which should be manualy checked
  //                     ->setCustomField($this->api->config['LeadFieldPayment'],
  //                       'Наличными',
  //                       4545907
  //                     );
  //                 }


                //
              ->setResponsibleUserId($this->settings['ResponsibleUserId'])
              /* Статус сделки */
              ->setStatusId($this->settings['LeadStatusId'])
              // Пакет участника
//              ->setCustomField(
//                $this->settings['LeadFieldPackage'],
//                $l->package,
//                strtolower($l->package)
//              )
              ->setCustomField(
                $this->settings['LeadFieldPackageCode'],
                strtoupper($l->package)
              );

            // @todo set responsible ID to Delivery manager if package == 'kokos'.toUpperCase() || 'FISTASHKI'
//              if(strtolower($l->package) == 'kokos' OR strtolower($l->package) == 'fistashki') {
//                $lead->setResponsibleUserId($this->settings['ResponsibleDeliveryMANAGER']);
//              }
              if(trim($l->payment) == 'p4'){
                $lead
                  ->setStatusId($this->settings['OplataNalichnimi'] ) // 17903028) // Status for Leads which should be manualy checked
                  ->setCustomField($this->settings['LeadFieldPayment'],
                    'Наличными',
                    4692858
                );
              }

              if($l->colour){
                $lead
                  ->setCustomField($this->settings['LeadFieldColour'],
                    $l->colour);
              }
              if($l->address){
                $lead
                  ->setCustomField($this->settings['LeadFieldAddress'],
                    $l->address);
              }

            $l->package = strtolower($l->package);

            $price =  $this->settings['price'];

            $bb = array_key_exists($l->package, $price);
            if($bb){
                $lead
                  ->setPrice($price[$l->package]);
            }

              /*  LeadFieldPayment
                [4545899] => Visa/MasterCard
                [4545901] => Приват24
                [4545907] => Наличными

                // Later will be cool to add check for Season Number
                // and based on choose pipelines and settings for
                //      "enums": {
                //      "4692854": "Visa/MasterCard"
                //      "4692856": "Приват24"
                //     "4692858": "Наличными"
                //      }

                'pro' => 877
                'standart' => 447
                'bonus' => 557
                'BezPrizov+' => 347
                'BezPrizov'  => 297
              */

            /* Отправляем данные в AmoCRM
            В случае успешного добавления в результате
            будет объект новой сделки */
            $rrr = $this->api->request(new Request(Request::SET, $lead));


            /* Сохраняем ID новой сделки для использования в дальнейшем */
            $lead = $this->api->last_insert_id;

            /* Создаем контакт */
            $contact = new Contact();
            $contact
              /* Имя */
              ->setName($l->name)
              /* Назначаем ответственного менеджера */

              ->setResponsibleUserId($this->settings['ResponsibleUserId'])
              /* Привязка созданной сделки к контакту */
              ->setLinkedLeadsId($lead)
              /* Кастомные поля */
              ->setCustomField(
                $this->settings['ContactFieldPhone'],
                $l->phone, // Номер телефона
                'MOB' // MOB - это ENUM для этого поля, список доступных значений смотрите в информации об аккаунте
              )
              ->setCustomField(
                $this->settings['ContactFieldEmail'],
//                $this->api->config['ContactFieldEmail'],
                $l->email, // Email
                'WORK' // WORK - это ENUM для этого поля, список доступных значений смотрите в информации об аккаунте
              );

//            if(strtolower($l->package) == 'kokos' OR strtolower($l->package) == 'fistashki') {
//              $contact->setResponsibleUserId($this->settings['ResponsibleDeliveryMANAGER']);
//            }

            $email = array(
              array(
                "email"=> $l->email,
                "variables"=> array(
                  "name"=> $l->name,
                  "phone" => $l->phone,
                ),
              ),
            );

            // Send to SendPulse
            // $this->settings['fvn']['SendPulseLead']
            $e = \SendPulse::addEmails($this->settings['SendPulseLead'], $email);

            // Some pause and send to Amo Contact
            usleep(500000);
            $this->api->request(new Request(Request::SET, $contact));

            // If lead proceed, let's update it
            $l->lead_id = $lead;
            $l->contact_id =  $this->api->last_insert_id;
            $l->status = true;
            $l->save();
          }
        }


      } catch (\Exception $e) {
        echo $e->getMessage();
      }
    }

}
