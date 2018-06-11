<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Helpers;
class PatientController extends Controller {

	
  public function savepatientregistration()
    {
        $gid=md5(uniqid(rand(), true));
        $birthdate=date('Y-m-d', strtotime(Input::get('dateofbirth'))); 
  
           $id=DB::table('patient')->insertGetId([
                'gid'=>$gid,
                'firstname' => Input::get('firstname'),
                'secondname' => Input::get('secondname'),
                'lastname' => Input::get('lastname'),
                'grandfathername' => Input::get('grandfathername'),
                'birthdate' => $birthdate,
                'nationality' => Input::get('nationality'),
                'gender' => Input::get('gender'),
                'maritalstatus' => Input::get('maritalstatus'),
                'emailaddress' => Input::get('emailaddress'),
                'address1' => Input::get('address1'),
                'address2' => Input::get('address2'),
                'active' => 'A',
                'homephone' =>Input::get('homephone'),
                'mobileno' =>Input::get('mobileno'),
                'company' =>Input::get('company'),
                'employeeno' =>Input::get('employeeno'),
                'dateencoded' => date("Y-m-d H:m:s"),
                'encodedby' => 'user',
                'datemodified' => date("Y-m-d H:m:s"),
                'modifiedby' => 'user']);
               $response= "Patient with Medical No. ".$id. " has been registered.";        
        
               return json_encode($response);
    }   


    public function updatepatientregistration()
    {
        $birthdate=date('Y-m-d', strtotime(Input::get('dateofbirth'))); 
        DB::table('patient')
                ->where('medicalno', Input::get('medicalno'))
                ->where('gid', Input::get('gid'))
                ->update(['firstname' => Input::get('firstname'),
                'secondname' => Input::get('secondname'),
                'lastname' => Input::get('lastname'),
                'grandfathername' => Input::get('grandfathername'),
                'birthdate' => $birthdate,
                'nationality' => Input::get('nationality'),
                'gender' => Input::get('gender'),
                'maritalstatus' => Input::get('maritalstatus'),
                'emailaddress' => Input::get('emailaddress'),
                'address1' => Input::get('address1'),
                'address2' => Input::get('address2'),
                'active' => 'A',
                'homephone' =>Input::get('homephone'),
                'mobileno' =>Input::get('mobileno'),
                'company' =>Input::get('company'),
                'employeeno' =>Input::get('employeeno'),
                'datemodified' => date("Y-m-d H:m:s"),
                'modifiedby' => 'user']);

               $response= "Patient information successfully updated";        
        
               return json_encode($response);
    }   

    public function getpatientdetails_view($medicalno)
    {
        $results=Helpers::getPatientData($medicalno);
        
        return json_encode($results);
    }


    public function getpatient()
    {
        $results= DB::select("select gid,medicalno,firstname,secondname,lastname,gender,birthdate,nationality,mobileno,emailaddress,active from vpatient ");
        
        return $results;
    }   

    
    

    public function getpatientdetails($medicalno,$gid)
    {
        $results= DB::select("select gid,medicalno,firstname,secondname,grandfathername,lastname,gender,Date_Format(birthdate, '%e-%b-%Y') as birthdate,maritalstatus,nationality,
        homephone,mobileno,emailaddress,address1,address2,company,employeeno,active from patient where medicalno=:medicalno and gid=:gid",['medicalno'=>$medicalno,'gid'=>$gid]);
        $response="";
        if(count($results)>0){
            $response=json_encode($results[0]);
        }
        return $response;
    }  

	
}
