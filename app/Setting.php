<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  // Let's say that all leads will came in some predicted array,
  // we just need to define it before and save in some JSON object

  // Se Lead is Actually object
  // We need to update it and make a connection with Amo for Updating in Amo as well

  // So in setting we should define our linking for objects
  // And asyns jobs

  // Where we should put manual asyns Jobs for tasks in Amo

  // Lead Journey
  // Funnel structure and some if, else for building fucking funnels (Digital Pipelines)
  // Jobs:
  //    -- Email open triger,
  //    -- link clicked triger,
  //    -- e-mail reply trigger
  //        :add some task into queue
  //    -- Task for sending E-mail, ChatBot Reply
  //    -- Tasks for updating E-mail stage
  //    -- Task and notif for Manager
  //    -- Order in orders with some stage
  //    -- Analytics between stages and Managers
  //    -- Lead Source tracking and Money calculating
  //
  //
  // Asyns, Tasks for ChatBots


  // Setting for connection between Leads and Contacts and Orders

  // Model for orders
  // Leads

  // WTF in AMO
  // Job for sending SMS
  // Job for sending Email's and replying
  // Job for

  // 1) In which stage this lead will come?  Defined in @settings

  // 2) Asyns Job's for add/update this lead in AMO (ProstoSystem)

  // 3) Trigger for updating stage . Defined in @settings

  // 4) Asyns Job's for add/update this lead in AMO (ProstoSystem)

}
