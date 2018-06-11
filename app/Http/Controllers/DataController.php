<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

class DataController extends Controller {

	
  public function getmaritalstatus()
    {
        $results= DB::select("select id,maritalstatus from maritalstatus where active='A' order by maritalstatus asc");
        
        return $results;
    }   


 public function getcountry()
    {
        $results= DB::select("select id,country from country where active='A' order by country asc");
        
        return $results;
    }   
    

	
}
