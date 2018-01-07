<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        // Check auth, if ok => than check Leads q
        echo 'We are trying to send smth';
        // if we have some leads, let's try push them

       // If lead get paid, let's update them
    }
}
