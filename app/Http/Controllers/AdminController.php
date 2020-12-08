<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Assignment;
use App\NotificationTable;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function indexBugAdmin(){
        $adminDataBug = DB::table('application')->select('perusahaan.nama_perusahaan','application.apps_name','ticket.id_ticket','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.type', 'Report')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(2);

        $totalPage = $adminDataBug->lastPage();
        $data = $adminDataBug->flatten(1);

        return response()->json([
            'bug_page_total'=>$totalPage,
            'dataBug'=> $data
        ]);
    }

    public function indexFeatureAdmin(){
        $adminDataBug = DB::table('application')->select('perusahaan.nama_perusahaan', 'application.apps_name', 'ticket.id_ticket','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.aproval_stat','ticket.created_at')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.type', 'Request')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(2);

        $totalPage = $adminDataBug->lastPage();
        $data = $adminDataBug->flatten(1);

        return response()->json([
            'fitur_page_total'=>$totalPage,
            'featureData'=> $data
        ]);
    }

    public function indexDoneAdmin(){   
        $adminDataDone = DB::table('perusahaan')
        ->select('application.apps_name','ticket.type', 'ticket.id_ticket','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('ticket.status', "Done")->orderByDesc('ticket.id_ticket')->paginate(2);

        $totalPage = $adminDataDone->lastPage();
        $data = $adminDataDone->flatten(1);

        return response()->json([
            'done_page_total'=>$totalPage,
            'doneData'=> $data
        ]);
    }

    public function makeAgreement(Request $request, $id_ticket){
        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);

        if($ticket){
            $update = Ticket::find($id_ticket);
            $sendNotif = DB::table('ticket')->select('users.id', 'users.fcm_token')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->join('users', 'perusahaan.id_perusahaan', '=', 'users.id_perusahaan')
            ->where('ticket.id_ticket', $id_ticket)->get();
            $getTicketInfo = DB::table('application')->select('ticket.subject', 'application.apps_name')
            ->join('ticket', 'application.id_apps', '=', 'ticket.id_apps')
            ->where('ticket.id_ticket', $id_ticket)->get();

            $target_notif = $sendNotif->pluck('id');
            $fcm_token = $sendNotif->pluck('fcm_token');
            $appsName = $getTicketInfo->pluck('apps_name')[0];
            $subjectTicket = $getTicketInfo->pluck('subject')[0];

            $update->update([
                'price' => $request->price,
                'time_periodic' => $request->time_periodic,
                'status' => $request->status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Succeesfull make agreement',
                'data'    => $update,
                'notif'   => $this->pushNotif($target_notif, $fcm_token, $id_ticket, "PT.TRIWIKRAMA", "Your Request need agreement", $appsName."-".$subjectTicket)
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
        foreach($request->id_user as $staff){
            Assignment::create([
                'id_user'     => $staff,
                'id_ticket'   => $request->input('id_ticket'),
                'dead_line'   => $request->input('dead_line')
            ]);
        }

        return response()->json([
            "message" => "Success Input Data"
        ]);
    }

    public function assignTask(Request $request){
        $validator = Validator::make($request->all(), [
            'id_user'   => 'required|array',
            'id_ticket' => 'required',
            'dead_line' => 'required'
        ],
            [
                'id_user.required.array'      => 'id_user Kosong !, Silahkan Masukkan id_user !',
                'id_ticket.required'          => 'id_ticket Kosong !, Silahkan Masukkan id_ticket !',
                'dead_line.required'          => 'dead_line Kosong !, Silahkan Masukkan dead_line !',
            ]
        );

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Bidang Yang Kosong',
                'data'    => $validator->errors()
            ], 401);

        } else {

            foreach ($request->id_user as $idStaffSelected) {
                $post = Assignment::create([
                    'id_user'     => $idStaffSelected,
                    'id_ticket'   => $request->input('id_ticket'),
                    'dead_line'   => $request->input('dead_line'),
                ]);
            }

            if ($post) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'Post Berhasil Disimpan!',
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Post Gagal Disimpan!',
                ], 401);
            }
        }
    
    }
    public function pushNotif($target_user, $fcm_token, $id_ticket, $from, $title, $message){
        foreach($target_user as $targetNotif){
            $post = NotificationTable::create([
                'id_user' => $targetNotif,
                'id_ticket' => $id_ticket,
                'from' => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 0
            ]);
        }

        $recipients = $fcm_token->toArray();
        $sendNotif = fcm()
        ->to($recipients)
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => $title,
            'body' => $message,
        ]);

        $sendNotif->send();
        
    }
    
} 