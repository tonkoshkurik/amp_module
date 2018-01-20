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
      try{
        $api = new Handler(env('AMO_DOMAIN'), env('AMO_LOGIN'));

        $this->api = $api;
//         print_r($this->api->request(new Request(Request::INFO))->result);

        // Here we first should process leads which was payed
        $inleads = \App\Lead::whereNull('status')->whereNull('payed')->get();

        // if we have some leads, let's try push them
        if($inleads->count()){
          foreach ($inleads as $l) {
            $lead = new Lead();
            $lead
              /* Название сделки */
              ->setName('Заявка №' . $l->id )
              /* Назначаем ответственного менеджера */
              ->setResponsibleUserId($this->api->config['ResponsibleUserId'])
              /* Статус сделки */
              ->setStatusId($this->api->config['LeadStatusId'])
              // Пакет участника
              ->setCustomField(
                $this->api->config['LeadFieldPackage'],
                $l->package,
                strtoupper($l->package)
              )
              ->setCustomField(
                $this->api->config['LeadFieldPackageCode'],
                $l->package
              );

              if(trim($l->payment) == 'p4'){
                $lead
                  ->setStatusId(17903028)
                  ->setCustomField($this->api->config['LeadFieldPayment'],
                    'Наличными',
                    4545907
                );
              }

            $l->package = strtolower($l->package);

            $price = array(
                'pro' => 877,
                'standart' => 447,
                'bonus' => 557,
                'bezprizov+' => 347,
                'bezprizov'  => 297,
            );

            $bb = array_key_exists($l->package, $price);
            if($bb){
                $lead
                  ->setPrice($price[$l->package]);
            }

              /*  LeadFieldPayment
                [4545899] => Visa/MasterCard
                [4545901] => Приват24
                [4545907] => Наличными

                'pro' => 877
                'standart' => 447
                'bonus' => 557
                'BezPrizov+' => 347
                'BezPrizov'  => 297
              */

            /* Отправляем данные в AmoCRM
            В случае успешного добавления в результате
            будет объект новой сделки */
            $this->api->request(new Request(Request::SET, $lead));

            /* Сохраняем ID новой сделки для использования в дальнейшем */
            $lead = $this->api->last_insert_id;

            /* Создаем контакт */
            $contact = new Contact();
            $contact
              /* Имя */
              ->setName($l->name)
              /* Назначаем ответственного менеджера */
              ->setResponsibleUserId($this->api->config['ResponsibleUserId'])
              /* Привязка созданной сделки к контакту */
              ->setLinkedLeadsId($lead)
              /* Кастомные поля */
              ->setCustomField(
                $this->api->config['ContactFieldPhone'],
                $l->phone, // Номер телефона
                'MOB' // MOB - это ENUM для этого поля, список доступных значений смотрите в информации об аккаунте
              )
              ->setCustomField(
                $this->api->config['ContactFieldEmail'],
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
            $e = SendPulse::addEmails(1465050, $email);

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
