<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function indexBugAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name','report_bug.priority','report_bug.subject', 'report_bug.detail', 'report_bug.status', 'report_bug.created_at')
        ->join('report_bug','application.id_apps','=','report_bug.id_apps')
        ->whereNOTIn('report_bug.status', function($subquery){
            $subquery->select('report_bug.status')->where('report_bug.status', "Done");
        })->get();

        return response()->json([
            "message" => 'success',
            "bugData" => $adminDataBug
        ]);
    }

    public function indexFeatureAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name','feature_request.priority','feature_request.subject', 'feature_request.detail', 'feature_request.status', 'feature_request.created_at')
        ->join('feature_request','application.id_apps','=','feature_request.id_apps')
        ->whereNOTIn('feature_request.status', function($subquery){
            $subquery->select('feature_request.status')->where('feature_request.status', "Done");
        })->get();

        return response()->json([
            "message" => 'success',
            "featureData" => $adminDataBug
        ]);
    }

    public function indexDoneAdmin(){   
        $userDataBug = DB::table('users')->select('application.apps_name','report_bug.priority','report_bug.subject', 'report_bug.detail', 'report_bug.status', 'report_bug.created_at')
        ->join('application','users.id','=','application.id_user')
        ->join('report_bug','application.id_apps','=','report_bug.id_apps')
        ->where('report_bug.status', function($subquery){
            $subquery->select('report_bug.status')->where('report_bug.status', "Done");
        })->get();

        $userDataFeature = DB::table('users')
        ->select('application.apps_name','feature_request.priority','feature_request.subject', 'feature_request.detail', 'feature_request.status', 'feature_request.created_at', 'feature_request.time_periodic', 'feature_request.price')
        ->join('application','users.id','=','application.id_user')
        ->join('feature_request','application.id_apps','=','feature_request.id_apps')
        ->where('feature_request.status', function($subquery){
            $subquery->select('feature_request.status')->where('feature_request.status', "Done");
        })->get();

        $obj_merged = array_merge($userDataBug->toArray(), $userDataFeature->toArray());
        return response()->json([
            "doneData" => $obj_merged
        ]);
    }

    public function makeAgreement(){

    }
}
