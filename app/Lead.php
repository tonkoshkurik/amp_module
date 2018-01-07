<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    //
  protected $table = 'leads';

  protected $fillable = ['name', 'season', 'email', 'phone', 'package'];
}
