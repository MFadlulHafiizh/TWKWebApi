<?php

namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function indexBugAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Report");
        })->get();

        return response()->json([
            "message" => 'success',
            "bugData" => $adminDataBug
        ]);
    }

    public function indexFeatureAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Request");
        })->get();

        return response()->json([
            "message" => 'success',
            "featureData" => $adminDataBug
        ]);
    }

    public function indexDoneAdmin(){   
        $userDataBug = DB::table('perusahaan')->select('application.apps_name','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Report");
        })->get();

        $userDataFeature = DB::table('perusahaan')
        ->select('application.apps_name','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Request");
        })->get();

        $obj_merged = array_merge($userDataBug->toArray(), $userDataFeature->toArray());
        return response()->json([
            "doneData" => $obj_merged
        ]);
    }

    public function makeAgreement(Request $request, $id_request){
        $feature_request = FeatureRequest::find($id_request);
        $feature_request->price = $request->input('price');
        $feature_request->time_periodic = $request->input('time_periodic');
        $feature_request->status = 'Need Agreement';

        $feature_request->save();
        return response()->json([
            'message' => 'Successfull make agreement',
            $feature_request
            ]);
    }
}
