<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//use NikitaKiselev\SendPulse;

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
    public function handle(\NikitaKiselev\SendPulse\Contracts\SendPulseApi $api)
    {
        //
//      $books = $api->listAddressBooks();
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
//      $books = \SendPulse::listAddressBooks();

      // Registration Book ID: 1465050
      // Members Book ID: 1465048


      $email = array(
        array(
          "email"=> "tonkoshkurik@gmail.com",
          "variables"=> array(
            "name"=> "Serg",
            "phone" => "+380995591095"
          ),
        ),
      );

//      add email to Registration Book
//      $e = $api->addEmails(1465050, $email);

      $e = $api->getTemplates();

      $e = $api->getTempleData($e[0]->id);


      dd($e);
    }
}
