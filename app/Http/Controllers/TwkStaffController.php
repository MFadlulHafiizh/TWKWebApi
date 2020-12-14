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
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('id_user', $request->id_user)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }

        //1kondisi
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('application.apps_name', $request->apps_name)
            ->where('id_user', $request->id_user)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])){
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('ticket.priority', $request->priority)
            ->where('id_user', $request->id_user)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->whereDate('assign_at', '>=', $dari)
            ->whereDate('assign_at', '<=', $sampai)
            ->where('id_user', $request->id_user)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }

        //2kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])){
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('id_user', $request->id_user)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('application.apps_name', $request->apps_name)
            ->where('id_user', $request->id_user)
            ->whereDate('assign_at', '>=', $dari)
            ->whereDate('assign_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('ticket.priority', $request->priority)
            ->where('id_user', $request->id_user)
            ->whereDate('assign_at', '>=', $dari)
            ->whereDate('assign_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }
        elseif(@$request['apps_name'] && @$request['priority'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $todoData = DB::table('assignment')
            ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('id_user', $request->id_user)
            ->whereDate('assign_at', '>=', $dari)
            ->whereDate('assign_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('assignment.id_assignment')->paginate(5);
        }

        $totalPage = $todoData->lastPage();
        $data = $todoData->flatten(1);
        

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'staff_todo_page_total' => $totalPage,
                'todoData' => $data
            ]);            
        } else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }       
    }

    public function indexHasDone(Request $request){
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){
             
            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);
            
        }


        elseif (@$request['apps_name'] && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])) {

            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

             //Request Only
             ->where('application.apps_name', $request->apps_name)
             ->where('id_user', $request->id_user)
             //Request Only
             ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);


            
        }
        elseif (empty($request['apps_name']) && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])) {

            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);

            
        }
        elseif (empty($request['apps_name']) && empty($request['priority']) && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;
            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);

        }
        elseif (@$request['apps_name'] && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])) {

            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);


        }
        elseif (@$request['apps_name'] && empty($request['priority']) && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;
            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);

            
        }
        elseif (empty($request['apps_name']) && @$request['priority'] && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;
            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);

            
        }
      
       
        elseif (@$request['apps_name'] && @$request['priority'] && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;
            $hasDoneData = DB::table('ticket')
            ->join('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->where('ticket.status', "Done")
            ->orderByDesc('assignment.id_assignment')->paginate(2);

            
        }

        $totalPage = $hasDoneData->lastPage();
        $data = $hasDoneData->flatten(1);

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'staff_done_page_total' => $totalPage,
                'hasDone' => $data
            ]);            
        } 
        else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }       
    }

    public function getAssignedApps(Request $request){
        $assignedApps = DB::table('assignment')->select('application.apps_name', 'application.id_apps')
        ->join('users', 'assignment.id_user', '=', 'users.id')
        ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->where('users.id', $request->id_user)
        ->groupBy('application.apps_name')->get();

        return response()->json([
            "message" => "Success",
            "userApp" => $assignedApps
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

        if($ticket){
            $update = Ticket::find($id_ticket);
            $update->update([
                'status' => 'Done'
            ]);
            return response()->json([
                'message'       => 'Succees.',
                'statusUpdate'  => $update,
                'notif' => $this->pushNotif($target_user, $fcm_token, $id_ticket, "PT Triwikrama", "Has completed", $appsName . " - " . $subjectTicket)
        ], 200);
        }else{
            return response([
                'success' => false,
                'message' => 'Failed.',
            ], 404);
        }
    }

    public function listNotif(Request $request){
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')
            
            //Request Only
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);
        }

        elseif (@$request['apps_name'] && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])) {

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')
            
            //Request Only
            ->where('application.apps_name', $request->apps_name)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);


            
        }
        elseif (empty($request['apps_name']) && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])) {

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);


            
        }
        elseif (empty($request['apps_name']) && empty($request['priority']) && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->whereDate('ticket.updated_at', '>=', $dari)
            ->whereDate('ticket.updated_at', '<=', $sampai)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);


        }


        elseif (@$request['apps_name'] && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])) {

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);



        }
        elseif (@$request['apps_name'] && empty($request['priority']) && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('ticket.updated_at', '>=', $dari)
            ->whereDate('ticket.updated_at', '<=', $sampai)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);


            
        }
        elseif (empty($request['apps_name']) && @$request['priority'] && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;

            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->whereDate('ticket.updated_at', '>=', $dari)
            ->whereDate('ticket.updated_at', '<=', $sampai)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);

            
        }
      
       
        elseif (@$request['apps_name'] && @$request['priority'] && @$request['dari'] && @$request['sampai']) {
            $dari = $request->dari;
            $sampai = $request->sampai;
            
            $getList = DB::table('ticket')->select('notification.id_notif', 'notification.read_at','perusahaan.nama_perusahaan', 'assignment.id_assignment', 'ticket.id_ticket', 'assignment.dead_line', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.updated_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
            ->join('application', 'ticket.id_apps', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')

            //Request Only
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('ticket.updated_at', '>=', $dari)
            ->whereDate('ticket.updated_at', '<=', $sampai)
            ->where('notification.id_user', $request->id_user)
            ->where('assignment.id_user', $request->id_user)
            //Request Only
            ->groupBy('ticket.id_ticket')
            ->orderByDesc('id_notif')
            ->paginate(5);
    
        }


        $notifCount = $getList->total();
        $totalPage = $getList->lastPage();
        $data = $getList->flatten(1);

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'notifCount' => $notifCount,
                'last_page_notif' => $totalPage,
                'notifData' => $data
            ]);            
        } 
        else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }       
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