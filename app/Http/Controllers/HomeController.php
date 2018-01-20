<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Handler;
use AmoCRM\Request as AmoRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $api = new Handler(env('AMO_DOMAIN'), env('AMO_LOGIN'));
      echo "<pre>";
      $amo = $api->request(new AmoRequest(AmoRequest::INFO))->result;
      dd($amo->account->pipelines, $amo->account->custom_fields);
//      print_r($amo->custom_fields);
      return view('home');
    }
}
