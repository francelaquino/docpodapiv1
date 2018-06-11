<?php

namespace App\Http\Controllers;


use DB;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Scorecard;
class AnalysisController extends Controller {

	
 
    public function prediabetic_v1($medicalno,$visitno)
    {
        $prediabetic=array(
            'age'=>'',
            'agescore'=>'0',
            'gender'=>'',
            'genderscore'=>'0',
            'bmimark'=>'0',
            'bmiscore'=>'0',
            'bmi'=>'0',
            'familywithdiabeticscore'=>'0',
            'diabeteswhenpregnantscore'=>'0',
            'alreadyhavediabeticscore'=>'0',
            'bloodpressurescore'=>'0',
            'physicallyactivescore'=>'0',
            'totalscore'=>'0',
            'message'=>'',
            'color'=>'',
        );
        $patient=Helpers::getPatientData($medicalno);
        $visit=Helpers::getPatientVisitData($medicalno,$visitno);

        $results= DB::select("select points from prediabeticscore_age where :age>=agefrom and :age<=ageto",
        ['age'=>(int)$patient["age"]]);

        $prediabetic["age"]=$patient["age"];
        $prediabetic["gender"]=$patient["gender"];
        $prediabetic["bmi"]=$visit["bmi"]."m/kg2";

        if($results){
            $prediabetic["agescore"]=$results[0]->points;
        }

        if($patient["gender"]=="Male"){
            $prediabetic["genderscore"]=1;
        }else{
            $prediabetic["genderscore"]=0;
        }
        $results= DB::select("select mark,points from prediabeticbmiscore where :bmi>=bmifrom and :bmi<=bmito and nationality=:nationality",
        ['bmi'=>$visit["bmi"],'nationality'=>$patient["southasian"]]);
        if($results){
            $prediabetic["bmiscore"]=$results[0]->points;
            $prediabetic["bmimark"]=$results[0]->mark;
        }


        $results= DB::select("select diagnosedwithhighbloodpressure,familywithdiabetic,alreadyhavediabetic,physicallyactive,diabeteswhenpregnant from survey_v1 where medicalno=:medicalno and visitno=:visitno",
        ['medicalno'=>$medicalno,'visitno'=>$visitno]);
        if($results){
            if((int)$results[0]->diagnosedwithhighbloodpressure =="Y"){
                $prediabetic["bloodpressurescore"]=1;
            }else{
                $prediabetic["bloodpressurescore"]=0;
            }
            if((int)$results[0]->familywithdiabetic =="Y"){
                $prediabetic["familywithdiabeticscore"]=1;
            }else{
                $prediabetic["familywithdiabeticscore"]=0;
            }
            if((int)$results[0]->diabeteswhenpregnant =="Y"){
                $prediabetic["diabeteswhenpregnantscore"]=1;
            }else{
                $prediabetic["diabeteswhenpregnantscore"]=0;
            }
            if((int)$results[0]->alreadyhavediabetic =="Y"){
                $prediabetic["alreadyhavediabeticscore"]=1;
            }else{
                $prediabetic["alreadyhavediabeticscore"]=0;
            }
            if((int)$results[0]->physicallyactive =="Y"){
                $prediabetic["physicallyactivescore"]=0;
            }else{
                $prediabetic["physicallyactivescore"]=1;
            }
        }
        $prediabetic["totalscore"]=$prediabetic["agescore"]+$prediabetic["genderscore"]+$prediabetic["familywithdiabeticscore"]+$prediabetic["diabeteswhenpregnantscore"]+$prediabetic["bloodpressurescore"]+$prediabetic["physicallyactivescore"];

        $results= DB::select("select color,message from prediabeticscore where :score>=scorefrom and :score<=scoreto",
        ['score'=>$prediabetic["totalscore"]]);
        if($results){
            $prediabetic["color"]=$results[0]->color;
            $prediabetic["message"]=$results[0]->message;
        }

        return json_encode($prediabetic);
    }
    public function healthscore_v1($medicalno,$visitno)
    {
        $patient=Helpers::getPatientData($medicalno);
        $survey_v1=Helpers::getSurveyData_v1($medicalno,$visitno);
        $visit=Helpers::getPatientVisitData($medicalno,$visitno);
        $scorecard[]= Helpers::getBloodPressure($visit["bpsystolic"],$visit["bpdiastolic"]);
        $scorecard[]= Helpers::getHbA1C($visit["hba1c"]);
        $scorecard[]= Helpers::getCholesterol($visit["cholesterol"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getLDLC($visit["ldlc"]);
        $scorecard[]= Helpers::getHDLC($visit["hdlc"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getTriglycerides($visit["triglycerides"]);
        $scorecard[]= Helpers::getBMI($visit["bmi"],$patient["southasian"],$visit["weight"]);
        $scorecard[]= Helpers::getWaist($visit["waist"],$patient["southasian"],$patient["gender"]);
        $scorecard[]= Helpers::getExercise($survey_v1["typeofexercise"],$survey_v1["exerciseperweek30min"]);
        return json_encode($scorecard);
    }

    public function healthscore_v2($medicalno,$visitno)
    {
        $patient=Helpers::getPatientData($medicalno);
        $survey_v1=Helpers::getSurveyData_v1($medicalno,$visitno);
        $visit=Helpers::getPatientVisitData($medicalno,$visitno);
        $scorecard[]= Helpers::getBloodPressure($visit["bpsystolic"],$visit["bpdiastolic"]);
        $scorecard[]= Helpers::getHbA1C($visit["hba1c"]);
        $scorecard[]= Helpers::getBMI($visit["bmi"],$patient["southasian"],$visit["weight"]);
        $scorecard[]= Helpers::getWaist($visit["waist"],$patient["southasian"],$patient["gender"]);
        $scorecard[]= Helpers::getCholesterol($visit["cholesterol"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getHDLC($visit["hdlc"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getLDLC($visit["ldlc"]);
        $scorecard[]= Helpers::getTriglycerides($visit["triglycerides"]);
       
        
        
        
        
        
        
        //$scorecard[]= Helpers::getExercise($survey_v1["typeofexercise"],$survey_v1["exerciseperweek30min"]);*/
        return json_encode($scorecard);
    }
    
    public function cvdreport_v1($medicalno,$visitno)
    {
        $report=[];
        $patient= Helpers::getPatientData($medicalno);
        $visit=Helpers::getPatientVisitData($medicalno,$visitno);
        $survey_v1=Helpers::getSurveyData_v1($medicalno,$visitno);
        $riskmessage=array(
            'riskmessage'=>'',
        );
        

        $cvd=Helpers::getCVDScore($patient["age"],$patient["gender"],$survey_v1["doyousmokecigarette"],$visit["hba1c"],$visit["cholesterol"],$visit["bpsystolic"],$patient["nationalityid"]);

        $results= DB::select("select message from cvdriskmessage where risk=:risk and gender=:gender and smoker=(case when :smoker then 'Y' else 'N' end) and diabetic=(case when :hba1c>=6.5 then 'Y' else 'N' end)",
            ['risk'=>$cvd["riskcategory"],'gender'=>$patient["gender"],'smoker'=>$survey_v1["doyousmoke"],'hba1c'=>$visit["hba1c"]]);
            if($results){
                $riskmessage["riskmessage"]=$results[0]->message;
            }
            $report=array_merge($patient, $visit,$cvd,$survey_v1,$riskmessage);
            
        
        

return $report;


        
    }  



	
}
