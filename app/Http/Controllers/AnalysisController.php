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
        $prediabetic["bmi"]=$visit["bmi"]."kg/m²";

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

        return $prediabetic;
        //return json_encode($prediabetic);
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
        $scorecard[]= Helpers::getBMI($visit["bmi"],$patient["southasian"],$visit["weight"],$visit["height"]);
        $scorecard[]= Helpers::getWaist($visit["waist"],$patient["southasian"],$patient["gender"]);
        if($survey_v1["physicallyactive"]=="Y"){
            $scorecard[]= Helpers::getExercise($survey_v1["typeofexercise"],$survey_v1["exerciseperweek30min"]);
        }
        if($survey_v1["doyousmoke"]=="Smoker"){
            $scorecard[]= Helpers::getSmoker($survey_v1["doyousmokecigarette"],'Cigarettes',$patient["gender"]);
            $scorecard[]= Helpers::getSmoker($survey_v1["doyousmokeshisha"],'Shisha',$patient["gender"]);
        }else{
            $scorecard[]= Helpers::getSmoker('Non-Smoker','Cigarettes','Male');
            $scorecard[]= Helpers::getSmoker('Non-Smoker','Shisha','Male');
        }
        return $scorecard;
        //return json_encode($scorecard);
    }

    public function goalsetting_v1($medicalno,$visitno1,$visitno2)
    {
        $goalsetting=array(
            'hba1c_message'=>'',
            'hba1c_status'=>'',
            'hdlc_message'=>'',
            'hdlc_status'=>'',
            'triglycerides_message'=>'',
            'triglycerides_status'=>'',
            'ldlc_message'=>'',
            'ldlc_status'=>'',
            'totalcholesterol_message'=>'',
            'totalcholesterol_status'=>'',
            'bloodpressure_message'=>'',
            'bloodpressure_status'=>'',
            'exercise_message'=>'',
            'exercise_status'=>'',
            'smoker_cigarette_message'=>'',
            'smoker_cigarette_status'=>'',
            'smoker_shisha_message'=>'',
            'smoker_shisha_status'=>'',
            'bmi_message'=>'',
            'bmi_status'=>'',
        );

       
        $patient=Helpers::getPatientData($medicalno);
        $visit1=Helpers::getPatientVisitData($medicalno,$visitno1);
        $visit2=Helpers::getPatientVisitData($medicalno,$visitno2);
        $survey_v1_1=Helpers::getSurveyData_v1($medicalno,$visitno1);
        $survey_v1_2=Helpers::getSurveyData_v1($medicalno,$visitno2);
        
        $hba1c= Helpers::getHbA1C($visit2["hba1c"]);
        $bmi1= Helpers::getBMI($visit1["bmi"],$patient["southasian"],$visit1["weight"],$visit1["height"] );
        $bmi2= Helpers::getBMI($visit2["bmi"],$patient["southasian"],$visit2["weight"],$visit2["height"] );
        $hdlc= Helpers::getHDLC($visit2["hdlc"],"N");
        $triglycerides= Helpers::getTriglycerides($visit2["triglycerides"]);
        $ldlc= Helpers::getLDLC($visit2["ldlc"]);
        $totalcholesterol= Helpers::getCholesterol($visit2["cholesterol"],"Y");
        $bloodpresure= Helpers::getBloodPressure($visit2["bpsystolic"],$visit2["bpdiastolic"]);
        if($survey_v1_2["physicallyactive"]=="Y" ){
            if($survey_v1_2["typeofexercise"]=="Moderate"){
                $exercise= Helpers::getExercise("Moderate",$survey_v1_2["exerciseperweek30min"]);
            }else{
                $exercise= Helpers::getExercise("Vigorous",$survey_v1_2["exerciseperweek15min"]);
            }
        }
        if($survey_v1_1["doyousmoke"]=="Non-Smoker"){
            $smoking_cigarette_v1= Helpers::getSmoker(0,'Cigarettes','Male');
            $smoking_shisha_v1= Helpers::getSmoker(0,'Shisha','Male');
        }else{
            $smoking_cigarette_v1= Helpers::getSmoker($survey_v1_1["doyousmokecigarette"],'Cigarettes','Male');
            $smoking_shisha_v1= Helpers::getSmoker($survey_v1_1["doyousmokeshisha"],'Shisha','Male');
        }

        if($survey_v1_2["doyousmoke"]=="Non-Smoker"){
            $smoking_cigarette_v2= Helpers::getSmoker(0,'Cigarettes','Male');
            $smoking_shisha_v2= Helpers::getSmoker(0,'Shisha','Male');
        }else{
            $smoking_cigarette_v2= Helpers::getSmoker($survey_v1_2["doyousmokecigarette"],'Cigarettes','Male');
            $smoking_shisha_v2= Helpers::getSmoker($survey_v1_2["doyousmokeshisha"],'Shisha','Male');
        }
        
       //HbA1c
        if((int)$hba1c["result"]==(int)$hba1c["target_result"]){
            $goalsetting["hba1c_message"]="Congratulations, your HbA1c result is ".$hba1c["result"].$hba1c["unit"].". ".$hba1c["goalachieved"];
            $goalsetting["hba1c_status"]="achieved";
        }
        else if((int)$visit1["hba1c"]==(int)$visit2["hba1c"]){
            $goalsetting["hba1c_message"]=$hba1c["goalnochange"];
            $goalsetting["hba1c_status"]="nochange";
        }else if((int)$visit1["hba1c"]>(int)$visit2["hba1c"]){
            $goalsetting["hba1c_message"]="Your HbA1c is ".$hba1c["result"].$hba1c["unit"].". ".$hba1c["goalimprove"];
            $goalsetting["hba1c_status"]="improve";
        }else if((int)$visit1["hba1c"]<(int)$visit2["hba1c"]){
            $goalsetting["hba1c_message"]="Your HbA1c is ".$hba1c["result"].$hba1c["unit"].". ".$hba1c["goalworsen"];
            $goalsetting["hba1c_status"]="worsen";
        }

        //hdlc
        if((int)$hdlc["result"]==(int)$hdlc["target_result"]){
            $goalsetting["hdlc_message"]="Congratulations, your HDLC result is ".$hdlc["result"].$hdlc["unit"].". ".$hdlc["goalachieved"];
            $goalsetting["hdlc_status"]="achieved";
        }
        else if((int)$visit1["hdlc"]==(int)$visit2["hdlc"]){
            $goalsetting["hdlc_message"]="Your HDLC result is ".$hdlc["result"].$hdlc["unit"].". ".$hdlc["goalnochange"];
            $goalsetting["hdlc_status"]="nochange";
        }else if((int)$visit1["hdlc"]<(int)$visit2["hdlc"]){
            $goalsetting["hdlc_message"]="Your HDLC result has increased from ".$visit1["hdlc"].$hdlc["unit"]."] to ".$visit2["hdlc"].$hdlc["unit"].". ".$hdlc["goalimprove"];
            $goalsetting["hdlc_status"]="improve";
        }else if((int)$visit1["hdlc"]>(int)$visit2["hdlc"]){
            $goalsetting["hdlc_message"]="Your HDLC result has decreased from ".$visit1["hdlc"].$hdlc["unit"]."] to ".$visit2["hdlc"].$hdlc["unit"].". ".$hdlc["goalworsen"];
            $goalsetting["hdlc_status"]="worsen";
        }
        
        //Triglycerides
        if((int)$triglycerides["result"]<=(int)$triglycerides["target_result"]){
            $goalsetting["triglycerides_message"]="Congratulations, your Triglycerides result is ".$triglycerides["result"].$triglycerides["unit"].". ".$triglycerides["goalachieved"];
            $goalsetting["triglycerides_status"]="achieved";
        }
        else if((int)$visit1["triglycerides"]==(int)$visit2["triglycerides"]){
            $goalsetting["triglycerides_message"]="Your Triglycerides result is ".$triglycerides["result"].$triglycerides["unit"].". ".$triglycerides["goalnochange"];
            $goalsetting["triglycerides_status"]="nochange";
        }else if((int)$visit1["triglycerides"]>(int)$visit2["triglycerides"]){
            $goalsetting["triglycerides_message"]="Your Triglycerides result has decreased from ".$visit1["triglycerides"].$triglycerides["unit"]."] to ".$visit2["triglycerides"].$triglycerides["unit"].". ".$triglycerides["goalimprove"];
            $goalsetting["triglycerides_status"]="improve";
        }else if((int)$visit1["triglycerides"]<(int)$visit2["triglycerides"]){
            $goalsetting["triglycerides_message"]="Your Triglycerides result has increased from ".$visit1["triglycerides"].$triglycerides["unit"]."] to ".$visit2["triglycerides"].$triglycerides["unit"].". ".$triglycerides["goalworsen"];
            $goalsetting["triglycerides_status"]="worsen";
        }

        //LDLC
        if((int)$ldlc["result"]<=(int)$ldlc["target_result"]){
            $goalsetting["ldlc_message"]="Congratulations, your LDLC result is ".$ldlc["result"].$ldlc["unit"].". ".$ldlc["goalachieved"];
            $goalsetting["ldlc_status"]="achieved";
        }
        else if((int)$visit1["ldlc"]==(int)$visit2["ldlc"]){
            $goalsetting["ldlc_message"]="Your LDLC result is ".$ldlc["result"].$ldlc["unit"].". ".$ldlc["goalnochange"];
            $goalsetting["ldlc_status"]="nochange";
        }else if((int)$visit1["ldlc"]>(int)$visit2["ldlc"]){
            $goalsetting["ldlc_message"]="Your LDLC result has decreased from ".$visit1["ldlc"].$ldlc["unit"]."] to ".$visit2["ldlc"].$ldlc["unit"].". ".$ldlc["goalimprove"];
            $goalsetting["ldlc_status"]="improve";
        }else if((int)$visit1["ldlc"]<(int)$visit2["ldlc"]){
            $goalsetting["ldlc_message"]="Your LDLC result has increased from ".$visit1["ldlc"].$ldlc["unit"]."] to ".$visit2["ldlc"].$ldlc["unit"].". ".$ldlc["goalworsen"];
            $goalsetting["ldlc_status"]="worsen";
        }

        //Total Cholesterol
        if((int)$totalcholesterol["result"]<=(int)$totalcholesterol["target_result"]){
            $goalsetting["totalcholesterol_message"]="Congratulations, your Total Cholesterol result is ".$totalcholesterol["result"].$totalcholesterol["unit"].". ".$totalcholesterol["goalachieved"];
            $goalsetting["totalcholesterol_status"]="achieved";
        }
        else if((int)$visit1["cholesterol"]==(int)$visit2["cholesterol"]){
            $goalsetting["totalcholesterol_message"]="Your Total Cholesterol result is ".$totalcholesterol["result"].$totalcholesterol["unit"].". ".$totalcholesterol["goalnochange"];
            $goalsetting["totalcholesterol_status"]="nochange";
        }else if((int)$visit1["cholesterol"]>(int)$visit2["cholesterol"]){
            $goalsetting["totalcholesterol_message"]="Your Total Cholesterol result has decreased from ".$visit1["cholesterol"].$totalcholesterol["unit"]."] to ".$visit2["cholesterol"].$totalcholesterol["unit"].". ".$totalcholesterol["goalimprove"];
            $goalsetting["totalcholesterol_status"]="improve";
        }else if((int)$visit1["cholesterol"]<(int)$visit2["cholesterol"]){
            $goalsetting["totalcholesterol_message"]="Your Total Cholesterol result has increased from ".$visit1["cholesterol"].$totalcholesterol["unit"]."] to ".$visit2["cholesterol"].$totalcholesterol["unit"].". ".$totalcholesterol["goalworsen"];
            $goalsetting["totalcholesterol_status"]="worsen";
        }
        

        //Blood Pressure
        if((91>=(int)$visit2["bpsystolic"] && 120<=(int)$visit2["bpsystolic"]) || (61>=(int)$visit2["bpdiastolic"] && 80<=(int)$visit2["bpdiastolic"])){
            $goalsetting["bloodpressure_message"]="Congratulations, your Blood Pressure result is ".$bloodpresure["result_systolic"]."/".$bloodpresure["result_diastolic"].". ".$bloodpresure["goalachieved"];
            $goalsetting["bloodpressure_status"]="achieved";
        }else if((int)$visit1["bpsystolic"]==(int)$visit2["bpsystolic"]  &&  (int)$visit1["bpdiastolic"]==(int)$visit2["bpdiastolic"]){
            $goalsetting["bloodpressure_message"]=$bloodpresure["result1"].". ".$bloodpresure["goalnochange"];
            $goalsetting["bloodpressure_status"]="nochange";
        }else if((int)$visit1["bpsystolic"]<(int)$visit2["bpsystolic"]  ||  (int)$visit1["bpdiastolic"]<(int)$visit2["bpdiastolic"]){
            $goalsetting["bloodpressure_message"]=$bloodpresure["result1"].". ".$bloodpresure["goalincrease"];
            $goalsetting["bloodpressure_status"]="increase";
        }else if((int)$visit1["bpsystolic"]>(int)$visit2["bpsystolic"]  ||  (int)$visit1["bpdiastolic"]>(int)$visit2["bpdiastolic"]){
            $goalsetting["bloodpressure_message"]=$bloodpresure["result1"].". ".$bloodpresure["goaldecrease"];
            $goalsetting["bloodpressure_status"]="decrease";
        }

         //Exercise
         if($survey_v1_2["physicallyactive"]=="Y" ){
            if($survey_v1_2["typeofexercise"]=="Moderate"){
                if((int)$survey_v1_2["exerciseperweek30min"]==7){
                    $goalsetting["exercise_message"]=$exercise["goalachieved"];
                    $goalsetting["exercise_status"]="achieved";
                }else if((int)$survey_v1_1["exerciseperweek30min"]==(int)$survey_v1_2["exerciseperweek30min"]){
                    $goalsetting["exercise_message"]=$exercise["goalnochange"];
                    $goalsetting["exercise_status"]="nochange";
                }else if((int)$survey_v1_1["exerciseperweek30min"]<(int)$survey_v1_2["exerciseperweek30min"]){
                    $goalsetting["exercise_message"]=$exercise["goalimprove"];
                    $goalsetting["exercise_status"]="improve";
                }else if((int)$survey_v1_1["exerciseperweek30min"]>(int)$survey_v1_2["exerciseperweek30min"]){
                    $goalsetting["exercise_message"]=$exercise["goalworsen"];
                    $goalsetting["exercise_status"]="worsen";
                }

            }else if($survey_v1_2["typeofexercise"]=="Vigorous"){
                if((int)$survey_v1_2["exerciseperweek15min"]==7){
                    $goalsetting["exercise_message"]=$exercise["goalachieved"];
                    $goalsetting["exercise_status"]="achieved";
                }else if((int)$survey_v1_1["exerciseperweek15min"]==(int)$survey_v1_2["exerciseperweek15min"]){
                    $goalsetting["exercise_message"]=$exercise["goalnochange"];
                    $goalsetting["exercise_status"]="nochange";
                }else if((int)$survey_v1_1["exerciseperweek15min"]<(int)$survey_v1_2["exerciseperweek15min"]){
                    $goalsetting["exercise_message"]=$exercise["goalimprove"];
                    $goalsetting["exercise_status"]="improve";
                }else if((int)$survey_v1_1["exerciseperweek15min"]>(int)$survey_v1_2["exerciseperweek15min"]){
                    $goalsetting["exercise_message"]=$exercise["goalworsen"];
                    $goalsetting["exercise_status"]="worsen";
                }
            }
        }
        
        if((int)$smoking_cigarette_v2["result_points"]==0){
            $goalsetting["smoker_cigarette_message"]=$smoking_cigarette_v2["goalachieved"];
            $goalsetting["smoker_cigarette_status"]="achieved";
        }else if((int)$smoking_cigarette_v1["result_points"]==(int)$smoking_cigarette_v2["result_points"]){
            $goalsetting["smoker_cigarette_message"]=$smoking_cigarette_v2["goalnochange"];
            $goalsetting["smoker_cigarette_status"]="nochange";
        }else if((int)$smoking_cigarette_v2["result_points"]>(int)$smoking_cigarette_v1["result_points"]){
            $goalsetting["smoker_cigarette_message"]=$smoking_cigarette_v2["goalworsen"];
            $goalsetting["smoker_cigarette_status"]="worsen";
        }else if((int)$smoking_cigarette_v2["result_points"]<(int)$smoking_cigarette_v1["result_points"]){
            $goalsetting["smoker_cigarette_message"]=$smoking_cigarette_v2["goalimprove"];
            $goalsetting["smoker_cigarette_status"]="improve";
        }

        if((int)$smoking_shisha_v2["result_points"]==0){
            $goalsetting["smoker_shisha_message"]=$smoking_shisha_v2["goalachieved"];
            $goalsetting["smoker_shisha_status"]="achieved";
        }else if((int)$smoking_shisha_v1["result_points"]==(int)$smoking_shisha_v2["result_points"]){
            $goalsetting["smoker_shisha_message"]=$smoking_shisha_v2["goalnochange"];
            $goalsetting["smoker_shisha_status"]="nochange";
        }else if((int)$smoking_shisha_v2["result_points"]>(int)$smoking_shisha_v1["result_points"]){
            $goalsetting["smoker_shisha_message"]=$smoking_shisha_v2["goalworsen"];
            $goalsetting["smoker_shisha_status"]="worsen";
        }else if((int)$smoking_shisha_v2["result_points"]<(int)$smoking_shisha_v1["result_points"]){
            $goalsetting["smoker_shisha_message"]=$smoking_shisha_v2["goalimprove"];
            $goalsetting["smoker_shisha_status"]="improve";
        }

        //BMI
        if($visit2["weight"]>=$bmi2["healthyweightfrom"] && $visit2["weight"]<=$bmi2["healthyweightto"]){
            $goalsetting["bmi_message"]="Congratulations, your BMI result is ".$bmi2["result"].$bmi2["unit"].". ".$bmi2["goalachieved"];
            $goalsetting["bmi_status"]="achieved";
        }
        else if($visit2["weight"]<$bmi2["healthyweightfrom"]){
            $goalsetting["bmi_message"]="Your weight is ".$visit2["weight"]."kg. It means you are underweight." .$bmi2["message"];
            $goalsetting["bmi_status"]="worsen";
        }
        else if((int)$visit2["weight"]==(int)$visit1["weight"]){
            $goalsetting["bmi_message"]="Your weight is ".$visit2["weight"]."kg. This is the same as last time." .$bmi2["message"]." ".$bmi2["goalnochange"];
            $goalsetting["bmi_status"]="nochange";
        }else if((int)$visit2["weight"]>(int)$visit1["weight"]){
            $goalsetting["bmi_message"]="Your weight is ".$visit2["weight"]."kg and has increased by ".(string)($visit2["weight"]-$visit1["weight"])."kg since last time.".$bmi2["goalworsen"];
            $goalsetting["bmi_status"]="worsen1";
        }else if((int)$visit2["weight"]<(int)$visit1["weight"]){
            $goalsetting["bmi_message"]="Congratulations you have lost weight! Your weight is ".$visit2["weight"]."kg. It means you have lost ".(string)($visit1["weight"]-$visit2["weight"])."kg. ".$bmi2["goalimprove"];
            $goalsetting["bmi_status"]="improve";
        }

        

        
        
        //return json_encode($goalsetting);
        return $goalsetting;
    }


    public function healthscore_v2($medicalno,$visitno)
    {
        $patient=Helpers::getPatientData($medicalno);
        $survey_v1=Helpers::getSurveyData_v1($medicalno,$visitno);
        $visit=Helpers::getPatientVisitData($medicalno,$visitno);
        $scorecard[]= Helpers::getBloodPressure($visit["bpsystolic"],$visit["bpdiastolic"]);
        $scorecard[]= Helpers::getHbA1C($visit["hba1c"]);
        $scorecard[]= Helpers::getBMI($visit["bmi"],$patient["southasian"],$visit["weight"],$visit["height"]);
        $scorecard[]= Helpers::getWaist($visit["waist"],$patient["southasian"],$patient["gender"]);
        $scorecard[]= Helpers::getCholesterol($visit["cholesterol"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getHDLC($visit["hdlc"],$survey_v1["doyousmoke"]);
        $scorecard[]= Helpers::getLDLC($visit["ldlc"]);
        $scorecard[]= Helpers::getTriglycerides($visit["triglycerides"]);
       
        
        
        
        
        
        
        return $scorecard;
        //return json_encode($scorecard);
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
        

        $cvd=Helpers::getCVDScore($patient["age"],$patient["gender"],$survey_v1["doyousmoke"],$visit["hba1c"],$visit["cholesterol"],$visit["bpsystolic"],$patient["nationalityid"]);

        $results= DB::select("select message from cvdriskmessage where risk=:risk and gender=:gender and smoker=(case when :smoker then 'Y' else 'N' end) and diabetic=(case when :hba1c>=6.5 then 'Y' else 'N' end)",
            ['risk'=>$cvd["riskcategory"],'gender'=>$patient["gender"],'smoker'=>$survey_v1["doyousmoke"],'hba1c'=>$visit["hba1c"]]);
            if($results){
                $riskmessage["riskmessage"]=$results[0]->message;
            }
            $report=array_merge($patient, $visit,$cvd,$survey_v1,$riskmessage);
            
        
        

        return $report;


        
    }  



	
}
