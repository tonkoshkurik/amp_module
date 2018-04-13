<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendPulse extends Controller
{
    // webhook for changing SendPulse members
  public function confirm(Request $request)
  {
    $lead_id = $request["leads"]["status"][0]["id"];

    Log::info($request["leads"]["status"]);
    Log::info($lead_id);
    $amo_conf = config('app.amo');
    $settings = $amo_conf['fvn'];

    Log::info($settings["SendPulseMember"]);

    $lead = \App\Lead::where('lead_id', $lead_id);

    if($lead->count()){

      $lead = $lead->first();

      $email = array(
        array(
          "email"=> $lead->email,
          "variables"=> array(
            "name"=> $lead->name,
            "phone" => $lead->phone,
          ),
        ),
      );

      // Delete from SendPulse
      // $this->settings['fvn']['SendPulseLead']
      // $this->settings['zhir']['SendPulseLead']
      // 1465050
      $e = \SendPulse::removeEmails($settings['SendPulseLead'], array($lead->email));

      // Send to SendPulse Members Book
      // $this->settings['fvn']['SendPulseMember']
      // $this->settings['zhir']['SendPulseMember']
      $e = \SendPulse::addEmails($settings['SendPulseMember'], $email);

      // Lead was moved
//    Log::info($lead_id);

    } else {
      // ask Amo for contact
      // If don't have need to create
    }

    // after we know leadID we should ask about this lead

  }
}
