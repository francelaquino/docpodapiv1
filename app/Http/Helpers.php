<?php



class Helpers{


    public static function getBloodPressure($systolic,$diastolic){

        $data=array(
            'seq'=>'',
            'test'=>'Blood Pressure',
            'target'=>'120/80 mmHg',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>''
        );


        $results= DB::select("select message,mark,points,color from bloodpressurescore where
        (:systolic>=systolicfrom and :systolic <=systolicto) or (:diastolic>=diastolicfrom and :diastolic<=diastolicto) order by points desc limit 1",
        ['systolic'=>$systolic,'diastolic'=>$diastolic]);
        
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Blood Pressure',
                'target'=>'120/80 mmHg',
                'target_points'=>'0',
                'result'=>$systolic.'/'.$diastolic,
                'result1'=>'Your blood pressure today is '.$systolic.'/'.$diastolic,
                'risk_category'=>$results[0]->mark,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
                'message'=>$results[0]->message,
            );
        }

        return $data;
    }

    public static function getHbA1C($value){

        $data=array(
            'seq'=>'',
            'test'=>'HbA1c',
            'target'=>'Below 5.9%',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>'',
        );
        $results= DB::select("select messagenondiabetic,messagediabetic,diabeticrisk,points,color from hba1cscore
        where :value>=perfrom AND :value<=perto",
        ['value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'HbA1c',
                'target'=>'Below 5.9%',
                'target_points'=>'0',
                'result'=>$value.'%',
                'result1'=>'Your HbA1c is  '.$value.'%.',
                'risk_category'=>$results[0]->diabeticrisk,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
            );
            if($value>=6.5){
                $data["message"]=$results[0]->messagediabetic;
            }else{
                $data["message"]=$results[0]->messagenondiabetic;
            }
        }

        return $data;
    }

    public static function getCholesterol($value,$smoker){

        $data=array(
            'seq'=>'',
            'test'=>'Cholesterol',
            'target'=>'Below 189mg/dL',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>'',
        );

        $results= DB::select("select message,messageforsmoker,riskcategory,points,color from lipidscore 
            where test='TotalCholesterol' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Cholesterol',
                'target'=>'Below 189mg/dL',
                'target_points'=>'0',
                'result'=>$value.'mg/dL',
                'result1'=>'Your total cholesterol result is '.$value.' mg/dL',
                'risk_category'=>$results[0]->riskcategory,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color
            );
            if($smoker=="Y"){
                $data["message"]=$results[0]->messageforsmoker;
            }else{
                $data["message"]=$results[0]->message;
            }
        }

        return $data;
    }

    public static function getLDLC($value){

        $data=array(
            'seq'=>'',
            'test'=>'LDLC',
            'target'=>'Below 99 mg/dL',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>'',
        );


        $results= DB::select("select message,riskcategory,points,color from lipidscore 
            where test='LDLC' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'LDLC',
                'target'=>'Below 99 mg/dL',
                'target_points'=>'0',
                'result'=>$value.'mg/dL',
                'result1'=>'Your LDLC (or bad cholesterol) result is '.$value.'mg/dL.',
                'risk_category'=>$results[0]->riskcategory,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
                'message'=>$results[0]->message,
            );  
        }

        return $data;
    }

    public static function getHDLC($value,$smoker){

        $data=array(
            'seq'=>'',
            'test'=>'HDLC',
            'target'=>'Above 60 mg/dL',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>''
        );

        $results= DB::select("select message,messageforsmoker,riskcategory,points,color from lipidscore 
            where test='HDLC' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'HDLC',
                'target'=>'Above 60 mg/dL',
                'target_points'=>'0',
                'result'=>$value.'mg/dL',
                'result1'=>'Your HDLC (or good cholesterol) result is '.$value.'mg/dL',
                'risk_category'=>$results[0]->riskcategory,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color
            );

            if($smoker=="Y"){
                $data["message"]=$results[0]->messageforsmoker;
            }else{
                $data["message"]=$results[0]->message;
            }
        }

        return $data;
    }

    public static function getTriglycerides($value){

        $data=array(
            'seq'=>'',
            'test'=>'Triglycerides',
            'target'=>'Below 150mg/dL',
            'target_points'=>'0',
            'result'=>$value.'mg/dL',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>''
        );


        $results= DB::select("select message,riskcategory,points,color from lipidscore 
            where test='Triglycerides' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Triglycerides',
                'target'=>'Below 150mg/dL',
                'target_points'=>'0',
                'result'=>$value.'mg/dL',
                'result1'=>'Your Triglyceride (TG) result is '.$value.'mg/dL.',
                'risk_category'=>$results[0]->riskcategory,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
                'message'=>$results[0]->message,
            );
        }

        return $data;
    }

    public static function getBMI($value,$nationality,$weight){

        $data=array(
            'seq'=>'',
            'test'=>'BMI',
            'target'=>'18.5-24.9kg/m2',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>''
        );
        $value=round($value,2);


        $bmifrom=0;
        $bmito=0;
        $healthyweightfrom=0;
        $healthyweightto=0;
        if($nationality=="SA"){
            $bmifrom=18.50000;
            $bmito=22.99999;
        }else{
            $bmifrom=18.50000;
            $bmito=24.99990;
        }

        $healthyweightfrom=round(($bmifrom/$value)*$weight,2);
        $healthyweightto=round(($bmito/$value)*$weight,2);
        $needtolose=$weight-$healthyweightto;
        $extramessage="";
        if($weight>$healthyweightto){
            $extramessage='To reach a healthy BMI you need to lose about '.$needtolose.'kg.';
        }




        
        $results= DB::select("select message,mark,points,color from bmiscore
        where nationality=:nationality  and :value>=bmifrom AND :value<=bmito",
        ['nationality'=>$nationality,'value'=>$value]);
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'BMI',
                'target'=>'18.5-24.9kg/m2',
                'target_points'=>'0',
                'result'=>$value.' kg/m2',
                'result1'=>'Your BMI is '.$value.'kg/m2.',
                'risk_category'=>$results[0]->mark,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
                'message'=>'A healthy weight for someone with your height is between '.$healthyweightfrom.'kg and '.$healthyweightto.'kg. '.$extramessage.$results[0]->message
            );
        }

        return $data;
    }

    public static function getWaist($value,$nationality,$gender){

        $data=array(
            'seq'=>'',
            'test'=>'Waist Size',
            'target'=>'Less than 94cm',
            'target_points'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'>=''
        );

        $results= DB::select("select message,mark,points,color from waistscore
        where nationality=:nationality and gender=:gender and :value>=waistfrom AND :value<=waistto",
        ['nationality'=>$nationality,'gender'=>$gender,'value'=>$value]);
        

        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Waist Size',
                'target'=>'Less than 94cm',
                'target_points'=>'0',
                'result'=>$value.'cm',
                'result1'=>'Your waist measurement is '.$value.' cm.',
                'risk_category'=>$results[0]->mark,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color,
                'message'=>$results[0]->message
            );

        }

        return $data;
    }

    public static function getSmoker($value){

        $data=array(
            'seq'=>'',
            'test'=>'Smoking',
            'target'=>'Non-smoker',
            'target_points'=>'0',
            'result'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>''
        );

        $results= DB::select("select mark,points,color from waistscore
        where nationality=:nationality and gender=:gender and :value>=waistfrom AND :value<=waistto",
        ['nationality'=>$nationality,'gender'=>$gender,'value'=>$value]);
        
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Smoking',
                'target'=>'Non-smoker',
                'target_points'=>'0',
                'result'=>$value,
                'risk_category'=>'',
                'result_points'=>'',
                'color'=>''
            );
        }

        return $data;
    }

    public static function getExercise($exercise,$days){

        $data=array(
            'seq'=>'',
            'test'=>'Exercise',
            'target'=>'Everyday',
            'target_points'=>'0',
            'result'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>''
        );

        $results= DB::select("select distinct riskcategory,points,color from lifestylescore_exercise 
        where exercise=:exercise and days=:days",
        ['exercise'=>$exercise,'days'=>$days]);
        
        if($results>0){
            $data=array(
                'seq'=>'',
                'test'=>'Exercise',
                'target'=>'Everyday',
                'target_points'=>'0',
                'result'=>$days.' day(s)',
                'risk_category'=>$results[0]->riskcategory,
                'result_points'=>$results[0]->points,
                'color'=>$results[0]->color
        );
    }

        return $data;
    }

    public static function getColor($colorname){

        $colorcode="";

        $results= DB::select("select colorcode from colorcodes
        where colorname=:colorname",
        ['colorname'=>$colorname]);
        if($results){
            $colorcode=$results[0]->colorcode;
        }

        return $colorcode;
    }

    public static function getPatientVisitData($medicalno,$visitno){
        $data=array(
            'visitno'=>'',
            'bloodpressure'=>'',
            'bpsystolic'=>'',
            'bpdiastolic'=>'',
            'hba1c'=>'',
            'cholesterol'=>'',
            'ldlc'=>'',
            'triglycerides'=>'',
            'hdlc'=>'',
            'bmi'=>'',
            'waist'=>'',
            'weight'=>'',
            'height'=>'',
            'typeofexercise'=>'',
            'exercisedays'=>''
        );
        $results= DB::select("select weight,height,visitno,bpsystolic,bpdiastolic,hba1c,totalcholesterol,hdlc,waist,bmi,ldlc,triglycerides from visits  where medicalno=:medicalno and visitno=:visitno",
        ['medicalno'=>$medicalno,'visitno'=>$visitno]);
        if($results){
            $data["visitno"]=$results[0]->visitno;
            $data["bpsystolic"]=$results[0]->bpsystolic;
            $data["bpdiastolic"]=$results[0]->bpdiastolic;
            $data["bloodpressure"]=$results[0]->bpsystolic.' - '.$results[0]->bpdiastolic;
            $data["hba1c"]=$results[0]->hba1c;
            $data["cholesterol"]=$results[0]->totalcholesterol;
            $data["hdlc"]=$results[0]->hdlc;
            $data["waist"]=$results[0]->waist;
            $data["weight"]=$results[0]->weight;
            $data["height"]=$results[0]->height;
            $data["bmi"]=round($results[0]->weight/($results[0]->height*$results[0]->height),2);
            $data["ldlc"]=$results[0]->ldlc;
            $data["triglycerides"]=$results[0]->triglycerides;
        }

        return $data;
    }

    public static function getSurveyData_v1($medicalno,$visitno){
        $data=array(
            'typeofexercise'=>'',
            'exerciseperweek30min'=>'',
            'doyousmokecigarette'=>'',
            'doyousmoke'=>''
        );

        $results= DB::select("select case when doyousmokecigarette='No' and doyousmokeshisha='No' then 'Non-Smoker' else 'Smoker' end as doyousmoke,case when doyousmokecigarette='No' then 'N' else 'Y' end as doyousmokecigarette, typeofexercise,exerciseperweek30min from survey_v1  where medicalno=:medicalno and visitno=:visitno",
        ['medicalno'=>$medicalno,'visitno'=>$visitno]);
        if($results){
            $data["typeofexercise"]=$results[0]->typeofexercise;
            $data["doyousmokecigarette"]=$results[0]->doyousmokecigarette;
            $data["exerciseperweek30min"]=$results[0]->exerciseperweek30min;
            $data["doyousmoke"]=$results[0]->doyousmoke;
        }

        return $data;
    }


    public static function getSouthAsian($nationality){
        $param="";

        $results= DB::select("select case when SOUTHASIAN ='Y' then 'SA' else 'NSA' end as param from country where id=:id",
        ["id"=>$nationality]);

        if($results){
            $param=$results[0]->param;
        }

        return $param;



    }


    public static function getPatientData($medicalno){
        $data=array(
            'medicalno'=>'',
            'firstname'=>'',
            'secondname'=>'',
            'grandfathername'=>'',
            'lastname'=>'',
            'gender'=>'',
            'emailaddress'=>'',
            'birthdate'=>'',
            'age'=>'',
            'southasian'=>'',
            'nationalityid'=>'',            
            'nationality'=>''
        );
        $results= DB::select("select medicalno,firstname,secondname,grandfathername,lastname,gender,age,nationality,emailaddress,birthdate,
        nationalityid from vpatient where medicalno=:medicalno",
        ['medicalno'=>$medicalno]);
        if($results){
            $data["nationalityid"]=$results[0]->nationalityid;
            $data["medicalno"]=$results[0]->medicalno;
            $data["firstname"]=$results[0]->firstname;
            $data["secondname"]=$results[0]->secondname;
            $data["grandfathername"]=$results[0]->grandfathername;
            $data["lastname"]=$results[0]->lastname;
            $data["gender"]=$results[0]->gender;
            $data["birthdate"]=$results[0]->birthdate;
            $data["age"]=$results[0]->age;
            $data["southasian"]=Helpers::getSouthAsian($results[0]->nationalityid);
            $data["emailaddress"]=$results[0]->emailaddress;
            $data["nationality"]=$results[0]->nationality;
        }

        return $data;
    }

    public static function getCVDCategory($age,$gender,$smoker,$hba1c){
        $category="";
        if($age>=40 || $hba1c>=6.50){
            $results= DB::select("select category from cvdcategory where :age>=agefrom and :age<=ageto and gender=:gender and smoker=:smoker and hba1c>=6.5",
        ["age"=>$age,"gender"=>$gender,"smoker"=>$smoker]);
        }else{
            $results= DB::select("select category from cvdcategory where :age>=agefrom and :age<=ageto and gender=:gender and smoker=:smoker and hba1c<6.5",
        ["age"=>$age,"gender"=>$gender,"smoker"=>$smoker]);
        }

        if($results){
            $category=$results[0]->category;
        }

        return $category;




    }
    public static function getCountryCategory_CVD($nationality){

        $results= DB::select("select GCC,WPR_A,EUR_A,SE_ASIA  from country where id=:id",
        ['id'=>$nationality]);
        
        $category='';
        
        if($results){
            if($results[0]->GCC=='Y'){
                $category='GCC';
            }else if($results[0]->WPR_A=='Y'){
                $category='WPR_A';
            }else if($results[0]->EUR_A=='Y'){
                $category='EUR_A';
            }else if($results[0]->SE_ASIA=='Y'){
                $category='SE_ASIA';
            }
        }
        
        return $category;
      


    }
    public static function getCVDScore($age,$gender,$smoker,$hba1c,$cholesterol,$systolic,$nationality){

        $data=array(
            'message'=>'',
            'color'=>'',
            'cvdcategory'=>'',
            'riskcategory'=>'',
        );


        $category=Helpers::getCVDCategory($age,$gender,$smoker,$hba1c);
        $data["cvdcategory"]=$category;

        $countrycategory=Helpers::getCountryCategory_CVD($nationality);


        $results= DB::select("select riskgcc,messagegcc, riskeur_a,riskwpr_a,riskseasia from cvdscore where category=:category  and totalcholesterol=:totalcholesterol and :bloodpressure>=bpfrom and :bloodpressure<=bpto",
        ['nationality'=>$nationality,'category'=>$category,'totalcholesterol'=>$cholesterol,'bloodpressure'=>$systolic]);
        if($results){
          
            if($countrycategory=='GCC'){
                $data["riskcategory"]=$results[0]->riskgcc;
                $data["message"]=$results[0]->messagegcc;
            }else if($countrycategory=='WPR_A'){
                $data["riskcategory"]=$results[0]->riskwpr_a;
            }else if($countrycategory=='EUR_A'){
                $data["riskcategory"]=$results[0]->riskeur_a;
            }else if($countrycategory=='SE_ASIA'){
                $data["riskcategory"]=$results[0]->riskseasia;
            } 
            
            $results= DB::select("select colorname as color from cvdcolor where risk=:risk",
            ['risk'=>$data["riskcategory"]]);
            if($results){
                $data["color"]=$results[0]->color;
            }

        }

        return $data;

    }

    public static function checkPatient($medicalno){
        $results= DB::select("select medicalno from patient  where medicalno=:medicalno",
        ['medicalno'=>$medicalno]);
        return $results;
    }

   



}