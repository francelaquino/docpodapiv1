<?php



class Helpers{


    public static function getBloodPressure($systolic,$diastolic){

        $data=array(
            'seq'=>'',
            'test'=>'Blood Pressure',
            'unit'=>'mmHg',
            'target'=>'120/80mmHg',
            'target_points'=>'0',
            'target_result_diastolic'=>'120',
            'target_result_systolic'=>'80',
            'result_diastolic'=>$diastolic,
            'result_systolic'=>$systolic,
            'result1'=>'Your blood pressure today is '.$systolic.'/'.$diastolic,
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>'',
            'goalincrease'=>'',
            'goaldecrease'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );

        



        $results= DB::select("select goalincrease,goaldecrease,goalnochange,goalachieved,message,mark,points,color from bloodpressurescore where
        (:systolic>=systolicfrom and :systolic <=systolicto) or (:diastolic>=diastolicfrom and :diastolic<=diastolicto) order by points desc limit 1",
        ['systolic'=>$systolic,'diastolic'=>$diastolic]);
        
        if($results>0){
            $data["result"]=$systolic.'/'.$diastolic;
            $data["risk_category"]=$results[0]->mark;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["message"]=$results[0]->message;
            $data["goalincrease"]=$results[0]->goalincrease;
            $data["goaldecrease"]=$results[0]->goaldecrease;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
        }

        return $data;
    }

    public static function getHbA1C($value){
        

        $data=array(
            'seq'=>'',
            'test'=>'HbA1c',
            'target'=>'Below 6.5%',
            'unit'=>'%',
            'target_points'=>'0',
            'target_result'=>'6.5',
            'result'=>$value,
            'result1'=>'Your HbA1c is  '.$value.'%.',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );

        $results= DB::select("select points from hba1cscore where diabeticrisk='Healthy'");
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }

        $results= DB::select("select colorcode,goalimprove,goalworsen,goalnochange,goalachieved,messagenondiabetic,messagediabetic,diabeticrisk,points,color from hba1cscore
        where :value>=perfrom AND :value<=perto",
        ['value'=>$value]);
        if($results>0){
            $data["risk_category"]=$results[0]->diabeticrisk;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
            $data["colorcode"]=$results[0]->colorcode;
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
            'unit'=>'mg/dL',
            'target_points'=>'0',
            'target_result'=>'189',
            'result'=>$value,
            'result1'=>'Your total cholesterol result is '.$value.' mg/dL',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );
        $results= DB::select("select points from lipidscore where riskcategory='Healthy' and test='TotalCholesterol'");
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }

        $results= DB::select("select colorcode,goalimprove,goalworsen,goalnochange,goalachieved,message,messageforsmoker,riskcategory,points,color from lipidscore 
            where test='TotalCholesterol' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data["risk_category"]=$results[0]->riskcategory;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["colorcode"]=$results[0]->colorcode;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;

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
            'unit'=>'mg/dL',
            'test'=>'LDLC',
            'target'=>'Below 99mg/dL',
            'target_result'=>'99',
            'target_points'=>'0',
            'result'=>$value,
            'result1'=>'Your LDLC (or bad cholesterol) result is '.$value.'mg/dL.',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );

        $results= DB::select("select points from lipidscore where riskcategory='Healthy' and test='LDLC'");
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }


        $results= DB::select("select colorcode,goalimprove,goalworsen,goalnochange,goalachieved,message,riskcategory,points,color from lipidscore 
            where test='LDLC' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data["risk_category"]=$results[0]->riskcategory;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["message"]=$results[0]->message;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
            $data["colorcode"]=$results[0]->colorcode;
        }

        return $data;
    }

    public static function getHDLC($value,$smoker){

        $data=array(
            'seq'=>'',
            'test'=>'HDLC',
            'unit'=>'mg/dL',
            'target'=>'Above 60 mg/dL',
            'target_points'=>'0',
            'target_result'=>'60',
            'result'=>$value,
            'result1'=>'Your HDLC (or good cholesterol) result is '.$value.'mg/dL',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );


        $results= DB::select("select points from lipidscore where riskcategory='Healthy' and test='HDLC'");
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }


        $results= DB::select("select colorcode,goalimprove,goalworsen,goalnochange,goalachieved,message,messageforsmoker,riskcategory,points,color from lipidscore 
            where test='HDLC' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data["risk_category"]=$results[0]->riskcategory;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["result_points"]=$results[0]->points;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
            $data["colorcode"]=$results[0]->colorcode;
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
            'unit'=>'mg/dL',
            'test'=>'Triglycerides',
            'target'=>'Below 150mg/dL',
            'target_points'=>'0',
            'target_result'=>'150',
            'result'=>$value,
            'result1'=>'',
            'result1'=>'Your Triglyceride (TG) result is '.$value.'mg/dL.',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );



        $results= DB::select("select points from lipidscore where riskcategory='Healthy' and test='Triglycerides'");
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }

        $results= DB::select("select colorcode,goalimprove,goalworsen,goalnochange,goalachieved,message,riskcategory,points,color from lipidscore 
            where test='Triglycerides' and :value>=MGFROM AND :value<=MGTO",
        ['value'=>$value]);
        if($results>0){
            $data["risk_category"]=$results[0]->riskcategory;
            $data["result_points"]=$results[0]->points;
            $data["color"]=$results[0]->color;
            $data["message"]=$results[0]->message;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
            $data["colorcode"]=$results[0]->colorcode;
          
        }

        return $data;
    }

    public static function getBMI($value,$nationality,$weight,$height){

        $data=array(
            'seq'=>'',
            'test'=>'BMI',
            'unit'=>'kg/m²',
            'target'=>'18.5-24.9kg/m²',
            'target_result'=>'',
            'target_points'=>'0',
            'healthyweightfrom'=>'0',
            'healthyweightto'=>'0',
            'result'=>'',
            'result1'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'message'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
        );

        $results= DB::select("select points from bmiscore where mark='Healthy Weight' and nationality=:nationality",['nationality'=>$nationality]);
        if($results>0){
            $data["target_points"]=$results[0]->points;
        }


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
        $healthybmi=$healthyweightto/($height*$height);
        $data["target_result"]=$healthybmi;

        if($weight>$healthyweightto){
            $extramessage='To reach a healthy BMI you need to lose about '.$needtolose.'kg.';
        }else{
            $needtolose=0;
        }




        
        $results= DB::select("select goalimprove,goalworsen,message,goalnochange,goalachieved,mark,points,color from bmiscore
        where nationality=:nationality  and :value>=bmifrom AND :value<=bmito",
        ['nationality'=>$nationality,'value'=>$value]);
        if($results>0){
            $data["result"]=$value;
            $data["result1"]='Your BMI is '.$value.'kg/m².';
            $data["risk_category"]=$results[0]->mark;
            if($results[0]->points>0){
                $data["result_points"]= (int)$data["target_points"]+floor($needtolose);
            }else{
                $data["result_points"]= $results[0]->points;
            }
            $data["color"]=$results[0]->color;
            $data["message"]='A healthy weight for someone with your height is between '.$healthyweightfrom.'kg and '.$healthyweightto.'kg. '.$extramessage.$results[0]->message;
            $data["goalimprove"]=$results[0]->goalimprove;
            $data["goalworsen"]=$results[0]->goalworsen;
            $data["goalnochange"]=$results[0]->goalnochange;
            $data["goalachieved"]=$results[0]->goalachieved;
            $data["healthyweightfrom"]=$healthyweightfrom;
            $data["healthyweightto"]=$healthyweightto;
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

    public static function getSmoker($value,$type,$gender){

        $data=array(
            'seq'=>'',
            'test'=>'Smoking',
            'target'=>'Non-smoker',
            'target_points'=>'0',
            'result'=>'',
            'risk_category'=>'',
            'result_points'=>'',
            'color'=>'',
            'colorcode'=>'',
            'goalimprove'=>'',
            'goalworsen'=>'',
            'goalnochange'=>'',
            'goalachieved'=>'',
            'color'=>'',
        );

        $results= DB::select("select color,colorcode,perday,goalimprove,goalworsen,goalnochange,goalachieved,points from lifestylescore_smoking
        where smoking=:type and gender=:gender and perday=:value",
        ['type'=>$type,'gender'=>$gender,'value'=>$value]);
        
        if($results>0){
                $data["test"]='Smoking '.$type;
                $data["result"]=$results[0]->perday; 
                $data["result_points"]=$results[0]->points;
                $data["goalimprove"]=$results[0]->goalimprove;
                $data["goalworsen"]=$results[0]->goalworsen;
                $data["goalnochange"]=$results[0]->goalnochange;
                $data["goalachieved"]=$results[0]->goalachieved;
                $data["color"]=$results[0]->color;
                $data["colorcode"]=$results[0]->colorcode;
        }

        return $data;
    }
    

    public static function getExercise($exercise,$days){
            $data=array(
                'seq'=>'',
                'test'=>$exercise.' Exercise',
                'target'=>'Everyday',
                'target_points'=>'10',
                'result'=>'',
                'risk_category'=>'',
                'result_points'=>'',
                'color'=>'',
                'colorcode'=>'',
                'goalimprove'=>'',
                'goalworsen'=>'',
                'goalnochange'=>'',
                'goalachieved'=>'',
            );

        $results= DB::select("select distinct goalimprove,goalworsen,goalnochange,goalachieved,riskcategory,points,color,colorcode from lifestylescore_exercise 
        where exercise=:exercise and days=:days",
        ['exercise'=>$exercise,'days'=>$days]);
        
        if($results>0){
                $data=array(
                    'seq'=>'',
                    'test'=>$exercise.' Exercise',
                    'target'=>'Everyday',
                    'target_points'=>'10',
                    'result'=>$days.' day(s)',
                    'risk_category'=>$results[0]->riskcategory,
                    'result_points'=>$results[0]->points,
                    'color'=>$results[0]->color,
                    'colorcode'=>$results[0]->colorcode,
                    'goalimprove'=>$results[0]->goalimprove,
                    'goalworsen'=>$results[0]->goalworsen,
                    'goalnochange'=>$results[0]->goalnochange,
                    'goalachieved'=>$results[0]->goalachieved,
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
            'visitdate'=>'',
            'bloodpressure'=>'',
            'bpsystolic'=>'',
            'bpdiastolic'=>'',
            'hba1c'=>'',
            'hba1c_unit'=>'%',
            'cholesterol'=>'',
            'cholesterol_unit'=>'mg/dL',
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
        $results= DB::select("select Date_Format(datecreated, '%e-%b-%Y') as visitdate,weight,height,visitno,bpsystolic,bpdiastolic,hba1c,totalcholesterol,hdlc,waist,bmi,ldlc,triglycerides from visits  where medicalno=:medicalno and visitno=:visitno",
        ['medicalno'=>$medicalno,'visitno'=>$visitno]);
        if($results){
            $data["visitno"]=$results[0]->visitno;
            $data["visitdate"]=$results[0]->visitdate;
            $data["bpsystolic"]=$results[0]->bpsystolic;
            $data["bpdiastolic"]=$results[0]->bpdiastolic;
            $data["bloodpressure"]=$results[0]->bpsystolic.'/'.$results[0]->bpdiastolic;
            $data["hba1c"]=$results[0]->hba1c;
            $data["cholesterol"]=$results[0]->totalcholesterol;
            $data["hdlc"]=$results[0]->hdlc;
            $data["waist"]=$results[0]->waist;
            $data["weight"]=round($results[0]->weight,2);
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
            'exerciseperweek30min'=>'0',
            'exerciseperweek15min'=>'0',
            'doyousmokecigarette'=>'',
            'doyousmokeshisha'=>'',
            'doyousmoke'=>'',
            'physicallyactive'=>'N',
        );

        $results= DB::select("select doyousmokecigarette,doyousmokeshisha,case when doyousmoke='N' then 'Non-Smoker' else 'Smoker' end as doyousmoke, physicallyactive,typeofexercise,exerciseperweek30min,exerciseperweek15min from survey_v1  where medicalno=:medicalno and visitno=:visitno",
        ['medicalno'=>$medicalno,'visitno'=>$visitno]);
        if($results){
            $data["typeofexercise"]=$results[0]->typeofexercise;
            $data["doyousmokecigarette"]=$results[0]->doyousmokecigarette;
            $data["doyousmokeshisha"]=$results[0]->doyousmokeshisha;
            $data["exerciseperweek30min"]=$results[0]->exerciseperweek30min;
            $data["exerciseperweek15min"]=$results[0]->exerciseperweek15min;
            $data["doyousmoke"]=$results[0]->doyousmoke;
            $data["physicallyactive"]=$results[0]->physicallyactive;
            
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
        ['category'=>$category,'totalcholesterol'=>$cholesterol,'bloodpressure'=>$systolic]);
        if($results){
          
            if($countrycategory=='GCC'){
                $data["riskcategory"]=$results[0]->riskgcc;
                $data["message"]=$results[0]->messagegcc;
            }else if($countrycategory=='WPR_A'){
                $data["riskcategory"]=$results[0]->riskwpr_a;
                $data["message"]=$results[0]->messagegcc;
            }else if($countrycategory=='EUR_A'){
                $data["riskcategory"]=$results[0]->riskeur_a;
                $data["message"]=$results[0]->messagegcc;
            }else if($countrycategory=='SE_ASIA'){
                $data["riskcategory"]=$results[0]->riskseasia;
                $data["message"]=$results[0]->messagegcc;
            }else{
                $data["riskcategory"]=$results[0]->riskseasia;
                $data["message"]=$results[0]->messagegcc;
            }
//David only message gcc was provided
            
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