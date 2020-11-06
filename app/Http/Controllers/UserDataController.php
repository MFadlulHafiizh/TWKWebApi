<?php

namespace App\Http\Controllers;

use App\ReportBug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserDataController extends Controller
{
    public function indexBug(Request $request){
        $userDataBug = DB::table('users')->select('application.apps_name','report_bug.priority','report_bug.subject', 'report_bug.detail', 'report_bug.status', 'report_bug.created_at')
        ->join('application','users.id','=','application.id_user')
        ->join('report_bug','application.id_apps','=','report_bug.id_apps')->where('users.email', $request->email)
        ->whereNOTIn('report_bug.status', function($subquery){
            $subquery->select('report_bug.status')->where('report_bug.status', "Done");
        })->get();

        $this ->validate($request, [
            "email"=>"required"
        ]);

        return response()->json([
            "message" => 'success',
            "bugData" => $userDataBug
        ]);
    }

    public function indexFeature(Request $request){
        $userDataFeature = DB::table('users')
        ->select('application.apps_name','feature_request.priority','feature_request.subject', 'feature_request.detail', 'feature_request.status', 'feature_request.created_at', 'feature_request.time_periodic', 'feature_request.price')
        ->join('application','users.id','=','application.id_user')
        ->join('feature_request','application.id_apps','=','feature_request.id_apps')->where('users.email', $request->email)
        ->whereNOTIn('feature_request.status', function($subquery){
            $subquery->select('feature_request.status')->where('feature_request.status', "Done");
        })->get();

        $this ->validate($request, [
            "email"=>"required"
        ]);

        return response()->json([
            "message" => "success",
            "featureData" => $userDataFeature
        ]);
    }

    
    public function indexDone(Request $request){
        $params = $request->email;
        
        $userDataBug = DB::table('users')->select('application.apps_name','report_bug.priority','report_bug.subject', 'report_bug.detail', 'report_bug.status', 'report_bug.created_at')
        ->join('application','users.id','=','application.id_user')
        ->join('report_bug','application.id_apps','=','report_bug.id_apps')->where('users.email', $params)
        ->where('report_bug.status', function($subquery){
            $subquery->select('report_bug.status')->where('report_bug.status', "Done");
        })->get();

        $userDataFeature = DB::table('users')
        ->select('application.apps_name','feature_request.priority','feature_request.subject', 'feature_request.detail', 'feature_request.status', 'feature_request.created_at', 'feature_request.time_periodic', 'feature_request.price')
        ->join('application','users.id','=','application.id_user')
        ->join('feature_request','application.id_apps','=','feature_request.id_apps')->where('users.email', $params)
        ->where('feature_request.status', function($subquery){
            $subquery->select('feature_request.status')->where('feature_request.status', "Done");
        })->get();

        $this ->validate($request, [
            "email"=>"required"
        ]);

        $obj_merged = array_merge($userDataBug->toArray(), $userDataFeature->toArray());
        return response()->json([
            "doneData" => $obj_merged
            ]);
        
    }

    public function userApp(Request $request){
        $getUserApps = DB::table('users')->select('application.apps_name', 'application.id_apps')
        ->join('application', 'users.id', '=', 'application.id_user')
        ->where('users.email', $request->email)->get();

        return response()->json([
            "message" => "Success",
            "userApp" => $getUserApps
            ]);
    }

    public function storeBug(Request $request){

        $input = $request->all();

        $validator = Validator::make($input, [
            'id_apps'=>'required',
            'priority'=> 'required|string',
            'subject'=> 'required|string',
            'detail'=> 'required|string',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            //return $this->sendError('Validation Error', $validator->errors());
            return response()->json([
                'message' => 'Unkwon Error, please try again later'
            ]);
        }

        $bugReport = ReportBug::create($input);
        return response()->json([
            'message' => 'Your report has sended'
        ]);

        
    }

    public function storeFeature(Request $request){

    }

    

}
