<?php

namespace App\Http\Controllers;

use App\Assignment;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;

class TwkStaffController extends Controller
{
    public function indexToDo(Request $request){
        $todoData = DB::table('assignment')
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
        ->orderByDesc('assignment.id_assignment')->paginate(1);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('priority', request('priority'))
                ->where('ticket.status', "Done")
                ->paginate(2)
                ->appends('priority', request('priority'));
        }

        $totalPage = $hasDoneData->lastPage();
        $data = $hasDoneData->flatten(1);

        return response()->json([
            'staff_done_page_total'=>$totalPage,
            'hasDone'=> $data
        ]);
    }

    public function listNotif(Request $request){
        $getList = DB::table('ticket')
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
}