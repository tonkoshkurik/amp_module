<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Artisan;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function add()
    {
      //{ "season": 222, "package": "pro", name: "Vasya Pupkin", "email": "vasya@pupkin.name", "phone": "+38 050 333 22 11" }

//      dd(\request()->all());

      $validator = Validator::make(request()->all(), [
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>'',
        'payment'=>''
      ]);


      if ($validator->fails()) {
        return response()->json(['status'=>'validation error'], 422);
      }

      $validatedData = request()->validate([
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>'',
        'payment'=>''
      ]);
      $validatedData['phone'] = preg_replace('/\s+/', '', $validatedData['phone']);

      // Check for dublicates

      $dublicates = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season']);

      if($dublicates->count()) {
        $matched_lead = $dublicates->first();
        if(!$matched_lead->payed){
          $matched_lead->fill($validatedData);
          $matched_lead->save();
        }
//        return dd($matched_lead);
        return ['status'=>'dublicate'];
      }

      $lead = new Lead;

      // package uppercase
      if(strlen($validatedData['package'])) {
        $validatedData['package'] = strtoupper( $validatedData['package'] );
      }

      $lead->fill($validatedData);

      $lead->save();

      return ['status'=>'success'];

    }


    /**
     * Update the lead payed status
     *
     */
    public function update()
    {
      // simple validation

      $validator = Validator::make(request()->all(), [
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>'',
        'payment'=>''
      ]);

      if ($validator->fails()) {
        return response()->json(['status'=>'validation error'], 422);
      }

      $validatedData = request()->validate([
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>'',
        'payment'=>''
      ]);

      $validatedData['phone'] = preg_replace('/\s+/', '', $validatedData['phone']);

      // package uppercase
      if(strlen($validatedData['package'])) {
        $validatedData['package'] = strtoupper( $validatedData['package'] );
      }

      // update payed status
      $update_lead = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season']);

      if($update_lead->count()){
        // @todo Before update also need to be sure that we have this lead in AMO
        // Artisan::call('amo:push');
        // It's wrong perception

        $update_lead = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season'])->first();

        $update_lead->status = null;
        $update_lead->payed = true;
        $update_lead->save();

        return ['status'=>'success'];
      } else {

        $lead = new Lead();
        $lead->fill($validatedData);
        $lead->status = null;
        $lead->payed = true;
        $lead->save();

        // Here we should add this lead to Amo CRM in Closed stage
        // Add to Members in Mail

        return response()->json(['status'=>'success'], 200);
//          return response()->json(['status'=>'no matched lead'], 422);
      }
  //      // Do we need to add it.
  //
  //      $update = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season'])->update([
  //        'status' => null,
  //        'payed' => true
  //      ]);

//      if($update){
//        return ['status'=>'success'];
//      } else {
//        return ['status'=>'failed'];
//      }

  //      if($lead->count()){
  //        $lead->status = null;
  //        $lead->payed = true;
  //        $lead->save();
  //      }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }
}
