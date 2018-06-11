<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix'=>'analysis'],function()
{
    Route::get('prediabetic_v1/{medicalno}/{visitno}',['uses'=>'AnalysisController@prediabetic_v1']);
    Route::get('healthscore_v1/{medicalno}/{visitno}',['uses'=>'AnalysisController@healthscore_v1']);
    Route::get('healthscore_v2/{medicalno}/{visitno}',['uses'=>'AnalysisController@healthscore_v2']);
    Route::get('cvdreport_v1/{medicalno}/{visitno}',['uses'=>'AnalysisController@cvdreport_v1']);
});

Route::group(['prefix'=>'data'],function()
{
    Route::get('getmaritalstatus',['uses'=>'DataController@getmaritalstatus']);
    Route::get('getcountry',['uses'=>'DataController@getcountry']);
});

Route::group(['prefix'=>'visit'],function()
{
    Route::get('getpatientvisits/{medicalno}',['uses'=>'VisitController@getpatientvisits']);
    Route::get('getresults/{medicalno}/{visitno}',['uses'=>'VisitController@getresults']);
    Route::get('deletepatientvisit/{medicalno}/{visitno}',['uses'=>'VisitController@deletepatientvisit']);
    Route::get('getsurvey_v1/{medicalno}/{visitno}',['uses'=>'VisitController@getsurvey_v1']);
    Route::post('updateresults',['uses'=>'VisitController@updateresults']);
    Route::post('savesurvey_v1',['uses'=>'VisitController@savesurvey_v1']);
    Route::post('createpatientvisit',['uses'=>'VisitController@createpatientvisit']);
    
    
});



Route::group(['prefix'=>'patient'],function()
{
    Route::post('savepatientregistration',['uses'=>'PatientController@savepatientregistration']);
    Route::post('updatepatientregistration',['uses'=>'PatientController@updatepatientregistration']);
    Route::get('getpatient',['uses'=>'PatientController@getpatient']);
    Route::get('getpatientdetails/{medicalno}/{gid}',['uses'=>'PatientController@getpatientdetails']);
    Route::get('getpatientdetails_view/{medicalno}',['uses'=>'PatientController@getpatientdetails_view']);
});

