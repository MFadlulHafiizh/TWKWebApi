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
                'success' => true,
                'message' => 'Succeesfull make agreement',
                'data'    => $update
        ], 200);
        }else{
            return response([
                'success' => false,
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

    public function changeStatus(Request $request, $id_ticket){

        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);

        if($ticket){
            $update = Ticket::find($id_ticket);
            $update->update([
                'status' => $request->status
            ]);
            return response()->json([
                'message' => 'Succees.',
                'statusUpdate'  => $update
        ], 200);
        }else{
            return response([
                'success' => false,
                'message' => 'Failed.',
            ], 404);
        }
    }

    public function assignTaskBackup(Request $request){

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

    public function assignTask(Request $request){
        $status = $request->status;
        $validator = Validator::make($request->all(), [
            'id_user'   => 'required',
            'id_ticket' => 'required',
            'dead_line' => 'required'
        ],
            [
                'id_user.required'      => 'id_user Kosong !, Silahkan Masukkan id_user !',
                'id_ticket.required'    => 'id_ticket Kosong !, Silahkan Masukkan id_ticket !',
                'dead_line.required'    => 'dead_line Kosong !, Silahkan Masukkan dead_line !',
            ]
        );

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Bidang Yang Kosong',
                'data'    => $validator->errors()
            ], 401);

        } else {

            $post = Assignment::create([
                'id_user'     => $request->input('id_user'),
                'id_ticket'   => $request->input('id_ticket'),
                'dead_line'   => $request->input('dead_line'),
            ]);

            if ($post) {
                return response()->json([
                    'success' => true,
                    'message' => 'Post Berhasil Disimpan!',
                    'update'  => $this->changeStatus($request->id_ticket, $request->status),
                    'create'  => $post
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Post Gagal Disimpan!',
                ], 401);
            }
        }
    
    }
}