<?php

namespace App\Console\Commands;
use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
use NikitaKiselev\SendPulse;
use Illuminate\Console\Command;

class AmoPayedLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amo:payed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $settings;

    public $api;

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

        $inleads = \App\Lead::whereNull('status')->whereNotNull('payed')->whereNull('lead_id')->get();

        if($inleads->count()) {
          foreach ($inleads as $l) {

            $lead = new Lead();
            $lead
              /* Название сделки */
              ->setName('Заявка №' . $l->id )

              ->setResponsibleUserId($this->settings['ResponsibleUserId'])
              /* Статус сделки */
              ->setStatusId(142)
              // Пакет участника
              ->setCustomField(
                $this->settings['LeadFieldPackage'],
                $l->package,
                strtoupper($l->package)
              )
              ->setCustomField(
                $this->settings['LeadFieldPackageCode'],
                strtoupper($l->package)
              );

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
            $e = \SendPulse::addEmails($this->settings['SendPulseMember'], $email);

            // Some pause and send to Amo Contact
            usleep(500000);
            $this->api->request(new Request(Request::SET, $contact));

            // If lead proceed, let's update it
            $l->lead_id = $lead;
            $l->contact_id = $this->api->last_insert_id;
            $l->status = true;
            $l->save();
          }

        }

      } catch (\Exception $e) {
        echo $e->getMessage();
      }
    }
}
