<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreCard extends Model {
    
    public $test ;
    public $target;
    public $target_points ;
    public $risk_category ;
    public $result_points;

    public function setTest($value){
        $this->test=$value;
    }


   

}
