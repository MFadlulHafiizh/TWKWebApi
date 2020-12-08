<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use App\NotificationTable;
use Illuminate\Support\Facades\Storage;
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
        ->select('ticket.id_ticket','application.apps_name','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.aproval_stat','ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price')
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

    public function storeBug(Request $request){
        $fcmToken = DB::table('users')->select('id','fcm_token')->where('role', 'twk-head')->get();
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();

        $id_admin = $fcmToken->pluck('id');
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
            'notif' => $this->pushNotif($id_admin, $bugReport->id_ticket, $nama_perusahaan, $fcmToken->pluck('fcm_token'), $nama_perusahaan . " Reported some bugs", $apps_name . " - " . $request->subject)
        ]);
    }

    public function storeFeature(Request $request){
        $fcmToken = DB::table('users')->select('id','fcm_token')->where('role', 'twk-head')->get();
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();

        $id_admin = $fcmToken->pluck('id');
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
            'notif' => $this->pushNotif($id_admin,$featureRequest->id_ticket,$nama_perusahaan,$fcmToken->pluck('fcm_token'), $nama_perusahaan . " Request some feature", $apps_name . " - " . $request->subject)
        ]);
    }

    public function agreementAct(Request $request, $id_ticket){
        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);
        $fcmToken = DB::table('users')->select('id','fcm_token')->where('role', 'twk-head')->get();

        $id_admin = $fcmToken->pluck('id');
        $token = $fcmToken->pluck('fcm_token');
        $nama_perusahaan = $request->nama_perusahaan;
        $apps_name = $request->apps_name;
        $title = $nama_perusahaan." ".$request->aproval_stat." Agreement";

        $validator = Validator::make($request->all(), [
            'nama_perusahaan'   => 'required',
            'apps_name'         => 'required',
            'aproval_stat'      => 'required',
            'status'            => 'required'
        ],
            [
                'nama_perusahaan.required'  => 'nama_perusahaan Kosong !, Silahkan Masukkan nama_perusahaan !',
                'apps_name.required'        => 'apps_name Kosong !, Silahkan Masukkan apps_name !',
                'aproval_stat.required'     => 'aproval_stat Kosong, Silahkan Masukkan aproval_stat !',
                'status.required'           => 'status Kosong, Silahkan Masukkan status !',
            ]
        );

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Bidang Yang Kosong',
                'data'    => $validator->errors()
            ], 401);

        }else if($ticket){
            $update = Ticket::find($id_ticket);
            $message = $apps_name." - ".$update->subject;
            $update->update([
                'aproval_stat' => $request->aproval_stat,
                'status'       => $request->status
            ]);
            return response()->json([
                'message'       => 'Succees.',
                'statusUpdate'  => $update,
                'notif'         => $this->pushNotif($id_admin, $id_ticket, $nama_perusahaan, $token, $title, $message)
        ], 200);
        }else{
            return response([
                'success' => false,
                'message' => 'Failed.',
            ], 404);
        }
    }

    public function uploadImage(Request $request, $id){

        $users = User::firstWhere('id', $id);

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ],
            [
                'photo.required'   => 'photo Kosong !, Silahkan Masukkan photo !',
            ]
        );
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Isi Bidang Yang Kosong',
                'data'    => $validator->errors()
            ], 401);
        }
        if($id){
            $photo = User::find($id);
            $photo->update([
                'photo' => $request->file('photo'),
            ]);
        }
         $uploadFolder = 'users';
         $photo = $request->file('photo');
         $image_uploaded_path = $photo->store($uploadFolder, 'public');
         $uploadedImageResponse = array(
            "id_user" => $id,
            "image_name" => basename($image_uploaded_path),
            "image_url" => "http://localhost:8000/storage/".($image_uploaded_path),
            "mime" => $photo->getClientMimeType()
         );
        return response()->json([
            'message' => 'Successfull Uploaded Photo.',
            'data'    => $uploadedImageResponse
        ], 201);

    //     $validator = Validator::make($request->all(), [
    //         'photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
    //     ],
    //         [
    //             'photo.required'   => 'photo Kosong !, Silahkan Masukkan photo !',
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Silahkan Isi Bidang Yang Kosong',
    //             'data'    => $validator->errors()
    //         ], 401);
    //     }else if($id){
    //         $data = User::find($id);
    //         $data->update([
    //             'photo' => $request->file('photo'),
    //         ]);
    //     if ($request->hasFile('photo')) {
    //         $request->file('photo')->move('uploads/', $request->file('photo')->getClientOriginalName());
    //         $data->photo = $request->file('photo')->getClientOriginalName();
    //         $data->save();
    //     };

    //     return response()->json([
    //         'message' => 'Successfull Uploaded Photo.',
    //         'data'    => $data
    //     ], 201);
    //     }
    }

    public function getImage(Request $request){
        $getImage = DB::table('users')
        ->select('users.id', 'users.photo')
        ->where('users.id', $request->id)->get();

        return response()->json([
            'message' => 'User Image.',
            'result'  => $getImage
        ]);
    }

    public function getListNotif(Request $request){
        $getList = DB::table('ticket')
        ->join('notification', 'notification.id_ticket', '=', 'ticket.id_ticket')
        ->join('application', 'ticket.id_apps', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', 'perusahaan.id_perusahaan')
        ->where('notification.id_user', $request->id_user)->orderByDesc('id_notif')->paginate(1);
        $notifCount = $getList->total();
        $totalPage = $getList->lastPage();
        $data = $getList->flatten(1);

        return response()->json([
            'notifCount' => $notifCount,
            'last_page_notif' => $totalPage,
            'notifData' => $data
        ]);
    }

    public function updateNotifReadAt(Request $request, $id_notif){
        $notification = NotificationTable::firstWhere('id_notif', $id_notif);

        if($notification){
            $update = NotificationTable::find($id_notif);
            $update->update([
                'read_at' => $request->read_at,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $update
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "failed update"
            ], 404);
        }
    }
    
    public function pushNotif($id_user, $id_ticket, $from,$adminToken, $title, $message){
        foreach($id_user as $targetNotif){
            $post = NotificationTable::create([
                'id_user' => $targetNotif,
                'id_ticket' => $id_ticket,
                'from' => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 0
            ]);
        }

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

    public function getFcmToken(Request $request, $id_ticket){
        $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        ->where('application.id_apps', $request->id_apps)->get();

        $getApps = $notifData->pluck('apps_name');
        $apps_name = $getApps[0];

        $update = Ticket::find($id_ticket);
        $message = $apps_name." - ".$update->subject;

        return $message;

        // $fcmToken = DB::table('users')->select('id','fcm_token')->where('role', 'twk-head')->get();
        // $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        // ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        // ->where('application.id_apps', $request->id_apps)->get();

        // $sendNotif = DB::table('ticket')->select('users.id', 'users.fcm_token')
        //     ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        //     ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        //     ->join('users', 'perusahaan.id_perusahaan', '=', 'users.id_perusahaan')
        //     ->where('ticket.id_ticket', $request->id_ticket)->get();

        // $object = $sendNotif->pluck('id');
        // $fcm_token = $sendNotif->pluck('fcm_token');
        // return response()->json([
        //     $object,
        //     $fcm_token
        // ]);

        // $id_admin = $fcmToken->pluck('id');
        // $getCompany = $notifData->pluck('nama_perusahaan');
        // $getApps = $notifData->pluck('apps_name');
        // $nama_perusahaan = $getCompany[0];
        // $apps_name = $getApps[0];

        //return $this->pushNotif($id_admin,$request->id_ticket,$nama_perusahaan,$fcmToken->pluck('fcm_token'), $nama_perusahaan . " Reported some bugs", $apps_name . " - " . $request->subject);
    }

    

}
