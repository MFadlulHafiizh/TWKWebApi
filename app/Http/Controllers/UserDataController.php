<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use App\NotificationTable;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserDataController extends Controller
{
    public function indexBug(Request $request){
        $userDataBug = DB::table('perusahaan')
        ->select('ticket.id_ticket', 'application.apps_name', 'ticket.type','ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Report')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(2);

        $totalPage = $userDataBug->lastPage();
        $data = $userDataBug->flatten(1);

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            'bug_page_total' => $totalPage,
            'dataBug' => $data
        ]);
    }

    public function indexFeature(Request $request){
        $userDataFeature = DB::table('perusahaan')
        ->select('ticket.id_ticket','application.apps_name','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Request')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(1);

        
        $totalPage = $userDataFeature->lastPage();
        $data = $userDataFeature->flatten(1);
        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            'fitur_page_total'=>$totalPage,
            'featureData'=> $data
        ]);
    }

    
    public function indexDone(Request $request){
        $userDataDone = DB::table('perusahaan')->select('application.apps_name' ,'ticket.priority', 'ticket.type', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.status', "Done")->orderByDesc('ticket.id_ticket')->paginate(2);

        $totalPage = $userDataDone->lastPage();
        $data = $userDataDone->flatten(1);

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            'done_page_total'=>$totalPage,
            'doneData'=> $data
        ]);
    }

    public function userApp(Request $request){
        $getUserApps = DB::table('perusahaan')->select('application.apps_name', 'application.id_apps')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('perusahaan.id_perusahaan', $request->id_perusahaan)->get();

        return response()->json([
            "message" => "Success",
            "userApp" => $getUserApps
            ]);
    }

    public function getFcmToken(Request $request){
        $fcmToken = DB::table('users')->select('fcm_token')->where('role', 'twk-head')->get();
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();

        $getCompany = $notifData->pluck('nama_perusahaan');
        $getApps = $notifData->pluck('apps_name');
        $nama_perusahaan = $getCompany[0];
        $apps_name = $getApps[0];

        return $this->pushNotif($fcmToken->pluck('fcm_token'), $nama_perusahaan . " Reported some bugs", $apps_name . " - " . $request->subject);
    }

    public function storeBug(Request $request){
        $fcmToken = DB::table('users')->select('fcm_token')->where('role', 'twk-head')->get();
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();
        $getCompany = $notifData->pluck('nama_perusahaan');
        $getApps = $notifData->pluck('apps_name');
        $nama_perusahaan = $getCompany[0];
        $apps_name = $getApps[0];
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_apps'=>'required',
            'type' => 'required',
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

        $bugReport = Ticket::create($input);
        return response()->json([
            'message' => 'Your report has sended',
            'notif' => $this->pushNotif($fcmToken->pluck('fcm_token'), $nama_perusahaan . " Reported some bugs", $apps_name . " - " . $request->subject)
        ]);
    }

    public function storeFeature(Request $request){
        $fcmToken = DB::table('users')->select('fcm_token')->where('role', 'twk-head')->get();
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();
        $getCompany = $notifData->pluck('nama_perusahaan');
        $getApps = $notifData->pluck('apps_name');
        $nama_perusahaan = $getCompany[0];
        $apps_name = $getApps[0];
        $input = $request->all();

        $validator = Validator::make($input, [
            'id_apps'=>'required',
            'type' => 'required',
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

        $featureRequest = Ticket::create($input);
        return response()->json([
            'message' => 'Your request has sended',
            'notif' => $this->pushNotif($fcmToken->pluck('fcm_token'), $nama_perusahaan . " Request some feature", $apps_name . " - " . $request->subject)
        ]);
    }

    public function uploadImage(Request $request, $id){

        $users = User::firstWhere('id', $id);

        if($id){
            $data = User::find($id);
            $data->update([
                'photo' => $request->file('photo'),
            ]);
        if ($request->hasFile('photo')) {
            $request->file('photo')->move('uploads/', $request->file('photo')->getClientOriginalName());
            $data->photo = $request->file('photo')->getClientOriginalName();
            $data->save();
        };

        return response()->json([
            'message' => 'Successfull Upload Photo.',
            'data'    => $data
        ], 201);
        }
    }

    public function getListNotif(Request $request){
        $getList = DB::table('ticket')
        ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
        ->where('notification.id_user', $request->id_user)->paginate(1);
        $notifCount = $getList->total();
        $totalPage = $getList->lastPage();
        $data = $getList->flatten(1);


        return response()->json([
            'notifCount' => $notifCount,
            'last_page_notif' => $totalPage,
            'notifData' => $data
        ]);

    }
    
    public function pushNotif($adminToken, $title, $message){
        $recipients = $adminToken->toArray();
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
