<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;

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

      $validatedData = request()->validate([
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>''
        ]);

      // Check for dublicates

      $dublicates = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season'])->count();

      if($dublicates) {
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
     * Update the lead payed status
     *
     */
    public function update()
    {
        //
      $validatedData = request()->validate([
        'name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|max:255',
        'season'=> 'required',
        'package'=>''
      ]);

      // Check for dublicates

      $update = \App\Lead::where('email', $validatedData['email'])->where('season', $validatedData['season'])->update([
        'status' => null,
        'payed' => true
      ]);

      if($update){
        return ['status'=>'success'];
      } else {
        return ['status'=>'failed'];
      }

//      if($lead->count()){
//        $lead->status = null;
//        $lead->payed = true;
//        $lead->save();
//      }

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
