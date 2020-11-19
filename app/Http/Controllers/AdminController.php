<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function indexBugAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name','ticket.id_ticket','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.type', 'Report')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->get();

        return response()->json([
            "message" => 'success',
            "bugData" => $adminDataBug
        ]);
    }

    public function indexFeatureAdmin(){
        $adminDataBug = DB::table('application')->select('application.apps_name', 'ticket.id_ticket','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.type', 'Request')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->get();

        return response()->json([
            "message" => 'success',
            "featureData" => $adminDataBug
        ]);
    }

    public function indexDoneAdmin(){   
        $adminDataDone = DB::table('perusahaan')
        ->select('application.apps_name','ticket.type', 'ticket.id_ticket','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.status', "Done")->get();

        return response()->json([
            "message" => "success",
            "doneData" => $adminDataDone
        ]);
    }

    public function makeAgreement(Request $request, $id_ticket){

        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);

        if($ticket){
            $update = Ticket::find($id_ticket);
            $update->update([
                'price' => $request->price,
                'time_periodic' => $request->time_periodic,
                'status' => $request->status
            ]);
            return response()->json([
                'message' => 'Succeesfull make agreement',
                $update
        ], 200);
        }else{
            return response([
                'status' => 'ERROR',
                'message' => 'Failed update data',
            ], 404);
        }
    }

    public function getTwkStaff(){
        $twkstaff = DB::table('users')->where('users.role', 'twk-staff')->get();

        return response()->json([
            'user' => $twkstaff,
        ]);
    }

    public function assignTask(Request $request){

        $input = $request->all();

        $validator = Validator::make($input, [
            'id_user' => 'required',
            'id_ticket' => 'required',
            'dead_line' => 'required'
        ]);

        $assignment = Assignment::create($input);
        return response()->json([
            "status" => "Created",
            "message" => "Success"
        ]);
    }
    
}
