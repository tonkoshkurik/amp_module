<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendPulse extends Controller
{
    // webhook for changing SendPulse members
  public function confirm(Request $request)
  {
    //      $books = \SendPulse::listAddressBooks();
    //      dd($books);

//    $webhook = array (
//      'leads' =>
//        array (
//          'status' =>
//            array (
//              0 =>
//                array (
//                  'id' => '19587494', //'8372949',
//                  'status_id' => '142',
//                  'pipeline_id' => '917788',
//                  'old_status_id' => '17617822',
//                  'old_pipeline_id' => '917788',
//                ),
//            ),
//        ),
//      'account' =>
//        array (
//          'id' => '17617810',
//          'subdomain' => 'jediservice',
//        ),
//    );

    $lead_id = $request["leads"]["status"][0]["id"];

    Log::info($lead_id);

    $lead = \App\Lead::where('lead_id', $lead_id);

    if($lead->count()){
      Log::info($lead->first());
    }

    // after we know leadID we should ask about this lead

  }
}
