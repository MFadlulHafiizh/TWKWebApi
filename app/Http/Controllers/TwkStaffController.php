<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Ticket;
use App\NotificationTable;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;

class TwkStaffController extends Controller
{
    public function indexToDo(Request $request){
        $todoData = DB::table('assignment')->select('perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'assignment.created_at')
        ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->where('id_user', $request->id_user)
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('assignment.id_assignment')->paginate(1);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('priority', request('priority'))
                ->whereNOTIn('ticket.status', function($subquery){
                    $subquery->select('ticket.status')->where('ticket.status', "Done");
                })
                ->paginate(2)
                ->appends('priority', request('priority'));
        }

        if(request()->has('apps_name')){
            $userDataFeature = DB::table('application')
                ->join('ticket', 'application.id_apps', '=', 'ticket.id_apps')
                ->where('apps_name', request('apps_name'))
                ->whereNOTIn('ticket.status', function($subquery){
                    $subquery->select('ticket.status')->where('ticket.status', "Done");
                })
                ->paginate(2)
                ->appends('apps_name', request('apps_name'));
        }

        if(request()->has('priority')){
            if(request()->has('apps_name')){
            $userDataFeature = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('apps_name', request('apps_name'))
                ->where('priority', request('priority'))
                ->whereNOTIn('ticket.status', function($subquery){
                    $subquery->select('ticket.status')->where('ticket.status', "Done");
                })
                ->paginate(2);
            }
        }

        $totalPage = $todoData->lastPage();
        $data = $todoData->flatten(1);
        
        return response()->json([
            'staff_todo_page_total'=>$totalPage,
            'todoData'=> $data
        ]);
    }

    public function indexHasDone(Request $request){
        $hasDoneData = DB::table('assignment')
        ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->where('id_user', $request->id_user)
        ->where('ticket.status', "Done")
        ->orderByDesc('assignment.id_assignment')->paginate(2);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('ticket.status', "Done")
                ->where('priority', request('priority'))
                ->paginate(2)
                ->appends('priority', request('priority'));
        }

        if(request()->has('apps_name')){
            $userDataFeature = DB::table('application')
                ->join('ticket', 'application.id_apps', '=', 'ticket.id_apps')
                ->where('ticket.status', "Done")
                ->where('apps_name', request('apps_name'))
                ->paginate(2)
                ->appends('apps_name', request('apps_name'));
        }

        if(request()->has('priority')){
            if(request()->has('apps_name')){
            $userDataFeature = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('ticket.status', "Done")
                ->where('apps_name', request('apps_name'))
                ->where('priority', request('priority'))
                ->paginate(2);
            }
        }

        $totalPage = $hasDoneData->lastPage();
        $data = $hasDoneData->flatten(1);

        return response()->json([
            'staff_done_page_total'=>$totalPage,
            'hasDone'=> $data
        ]);
    }

    public function markAsComplete(Request $request, $id_ticket){
        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);
        
        $getTicketInfo = DB::table('application')->select('ticket.subject', 'application.apps_name')
            ->join('ticket', 'application.id_apps', '=', 'ticket.id_apps')
            ->where('ticket.id_ticket', $id_ticket)->get();
        $clientTarget = DB::table('ticket')->select('users.id','users.fcm_token')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->join('users', 'perusahaan.id_perusahaan', '=', 'users.id_perusahaan')
        ->where('ticket.id_ticket', $id_ticket)
        ->get();
        $adminTarget = DB::table('users')->select('id','fcm_token')->where('role', 'twk-head')->get();

        $fcm_token = $clientTarget->pluck('fcm_token')->toArray();
        array_push($fcm_token, $adminTarget->pluck('fcm_token')[0]);
        $target_user = $clientTarget->pluck('id')->toArray();
        array_push($target_user, $adminTarget->pluck('id')[0]);
        $appsName = $getTicketInfo->pluck('apps_name')[0];
        $subjectTicket = $getTicketInfo->pluck('subject')[0];

        return response()->json([
            "target"      =>  $target_user,
            "notif" => $this->pushNotif($target_user, $fcm_token, $id_ticket, "PT Triwikrama", "Has completed", $appsName . " - " . $subjectTicket)
        ]);

        
        // if($ticket){
        //     $update = Ticket::find($id_ticket);
        //     $update->update([
        //         'status' => 'Done'
        //     ]);
        //     return response()->json([
        //         'message'       => 'Succees.',
        //         'statusUpdate'  => $update,
        //         'notif'         => $this->pushNotif()
        // ], 200);
        // }else{
        //     return response([
        //         'success' => false,
        //         'message' => 'Failed.',
        //     ], 404);
        // }
    }

    public function listNotif(Request $request){
        $getList = DB::table('ticket')->select('notification.id_notif','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'notification.created_at')
        ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
        ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
        ->join('application', 'ticket.id_apps', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')
        ->where('notification.id_user', $request->id_user)
        ->where('assignment.id_user', $request->id_user)
        ->orderByDesc('id_notif')->paginate(1);
        $notifCount = $getList->total();
        $totalPage = $getList->lastPage();
        $data = $getList->flatten(1);

        return response()->json([
            'notifCount' => $notifCount,
            'last_page_notif' => $totalPage,
            'notifData' => $data
        ]);
    }

    public function pushNotif($target_user, $fcm_token, $id_ticket, $from, $title, $message){

        $recipients = $fcm_token;
        $sendNotif = fcm()
        ->to($recipients)
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => $title,
            'body' => $message,
        ]);
        $sendNotif->send();
        
        foreach($target_user as $target_notif){
            if(NotificationTable::where('id_user', '=', $target_notif)->where('id_ticket', '=', $id_ticket)->exists()){
                $update = DB::table('notification')->where('id_ticket', $id_ticket)->where('id_user', $target_notif)
                ->update([
                'from'  => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 1
                ]);
            }
            else{
                $post = NotificationTable::create([
                'id_user' => $target_notif,
                'id_ticket' => $id_ticket,
                'from' => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 0
            ]);
            }
        }
    }
}