<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Helpers;

class VisitController extends Controller {

	
  public function getpatientvisits($medicalno)
    {
        $results= DB::select("select datecreated,visitno,hba1c,totalcholesterol,hdlc,ldlc,triglycerides,bloodpressure,waist,bmi,medicalno from vpatientvisits where medicalno=:medicalno order by visitno asc",["medicalno"=>$medicalno]);
        
        return $results;
    }   
    public function deletepatientvisit($medicalno,$visitno){
        $where = array('visitno' =>$visitno,'medicalno' =>$medicalno);

        DB::table('visits')->where($where)->delete();

        DB::table('survey_v1')->where($where)->delete();

    }  

    

    public function getresults($medicalno,$visitno)
    {
        $report=[];
        $patient= Helpers::getPatientData($medicalno);
        $visit= DB::select("select medicalno,visitno,diabeticcategory,hba1c,hdlc,
        triglycerides,
        ldlc,
        totalcholesterol,
        bpsystolic,
        bpdiastolic,
        bmi,
        waist,
        lipidprofile_status,
        hba1c_status,
        bloodpressure_status,
        bmi_status,
        surveyno,
        status,
        datecreated,
        createdby,
        Date_Format(datecreated, '%e-%b-%Y') as visitdate,
        datecompleted,
        datemodified,
        modifiedby,
        height,
        weight from visits where medicalno=:medicalno and visitno=:visitno ",["medicalno"=>$medicalno,"visitno"=>$visitno]);
        if(count($visit)>0){
            $report=array_merge($patient, (array)$visit[0]);    
        }
        else{
            $report=$patient;
        }
        return json_encode($report);
    }  
    
    

    public function updateresults()
    {

        DB::table('visits')
                ->where('medicalno', Input::get('medicalno'))
                ->where('visitno', Input::get('visitno'))
                ->update(['hba1c' => Input::get('hba1c'),
                'hdlc' => Input::get('hdlc'),
                'triglycerides' => Input::get('triglycerides'),
                'ldlc' => Input::get('ldlc'),
                'totalcholesterol' => Input::get('totalcholesterol'),
                'bpsystolic' => Input::get('bpsystolic'),
                'bpdiastolic' => Input::get('bpdiastolic'),
                'height' => Input::get('height'),
                'weight' => Input::get('weight'),
                'bmi' => Input::get('bmi'),
                'waist' => Input::get('waist'),
                'datemodified' => date("Y-m-d H:m:s"),
                'modifiedby' => 'user']);

               $response= "Patient information successfully updated";        
        
               return json_encode($response);
              
    }   

    public function savesurvey_v1()
    {
  

        $results= DB::select("select id from survey_v1 where medicalno=:medicalno and visitno=:visitno",["medicalno"=>Input::get('medicalno'),"visitno"=>Input::get('visitno')]);
       
        if(count($results)<=0){
           DB::table('survey_v1')->insert([
                'medicalno' => Input::get('medicalno'),
                'visitno' => Input::get('visitno'),
                'physicallyactive' => Input::get('physicallyactive'),
                'diabeteswhenpregnant' => Input::get('diabeteswhenpregnant'),
                'alreadyhavediabetic' => Input::get('alreadyhavediabetic'),
                'typeofdiabetic' => Input::get('typeofdiabetic'),
                'familywithdiabetic' => Input::get('familywithdiabetic'),
                'familyheartdesease' => Input::get('familyheartdesease'),
                'diagnosedwithhighbloodpressure' => Input::get('diagnosedwithhighbloodpressure'),
                'doyousmokecigarette' => Input::get('doyousmokecigarette'),
                'doyousmokeshisha' => Input::get('doyousmokeshisha'),
                'typeofexercise' =>Input::get('typeofexercise'),
                'exerciseperweek30min' =>Input::get('exerciseperweek30min'),
                'exerciseperweek15min' =>Input::get('exerciseperweek15min'),
                'doyousmoke' => Input::get('doyousmoke'),
                'dateencoded' => date("Y-m-d H:m:s"),
                'encodedby' => 'user',
                'datemodified' => date("Y-m-d H:m:s"),
                'modifiedby' => 'user']);
           }else{
            DB::table('survey_v1')
                ->where('medicalno', Input::get('medicalno'))
                ->where('visitno', Input::get('visitno'))
                ->update(['physicallyactive' => Input::get('physicallyactive'),
                'diabeteswhenpregnant' => Input::get('diabeteswhenpregnant'),
                'alreadyhavediabetic' => Input::get('alreadyhavediabetic'),
                'typeofdiabetic' => Input::get('typeofdiabetic'),
                'familywithdiabetic' => Input::get('familywithdiabetic'),
                'familyheartdesease' => Input::get('familyheartdesease'),
                'diagnosedwithhighbloodpressure' => Input::get('diagnosedwithhighbloodpressure'),
                'doyousmokecigarette' => Input::get('doyousmokecigarette'),
                'doyousmokeshisha' => Input::get('doyousmokeshisha'),
                'doyousmoke' => Input::get('doyousmoke'),
                'typeofexercise' =>Input::get('typeofexercise'),
                'exerciseperweek30min' =>Input::get('exerciseperweek30min'),
                'exerciseperweek15min' =>Input::get('exerciseperweek15min'),
                'datemodified' => date("Y-m-d H:m:s"),
                'modifiedby' => 'user']);
           }
               $response= "";        
        
               return json_encode($response);
    }   

    public function createpatientvisit()
    {
            $response="";
            if(Helpers::checkPatient(Input::get('medicalno'))>0){
       
                DB::table('visits')->insert([
                        'medicalno' => Input::get('medicalno'),
                        'datecreated' => date("Y-m-d H:m:s"),
                        'createdby' => 'user',
                        'datemodified' => date("Y-m-d H:m:s"),
                        'modifiedby' => 'user']);
          
               $response= "Patient visit successully created";        
           }else{
                $response="Invalid medical no";
           }
               return json_encode($response);
    }   

    public function getsurvey_v1($medicalno,$visitno)
    {
        $results= DB::select("select * from survey_v1 where medicalno=:medicalno and visitno=:visitno ",["medicalno"=>$medicalno,"visitno"=>$visitno]);
        if(count($results)>0){
            return json_encode($results[0]);
        }else{
            return json_encode($results);
        }
    }  
	
}
