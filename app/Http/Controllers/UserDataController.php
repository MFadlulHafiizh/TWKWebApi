<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use Illuminate\Support\Str;
use App\NotificationTable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserDataController extends Controller
{
    public function filterData(Request $request) {
        
        if(empty($request['apps_name']) && empty($request['priority'])) {
            $getData = DB::table('perusahaan')
            
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')        
            ->where('perusahaan.id_perusahaan', $request->id_perusahaan)->where('ticket.type', 'Report')        
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->orderByDesc('ticket.id_ticket')->paginate(5);
        }
        
        elseif(@$request['apps_name'] && @$request['priority']) {
            $getData = DB::table('perusahaan')
            
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')        
            ->where('perusahaan.id_perusahaan', $request->id_perusahaan)->where('ticket.type', 'Report')        
            ->where('application.apps_name', $request->apps_name)->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->orderByDesc('ticket.id_ticket')->paginate(5);
        }

        elseif (@$request['apps_name'] && empty($request['priority'])) {
            $getData = DB::table('perusahaan')
            
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')        
            ->where('perusahaan.id_perusahaan', $request->id_perusahaan)->where('ticket.type', 'Report')        
            ->where('application.apps_name', $request->apps_name)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->orderByDesc('ticket.id_ticket')->paginate(5);
        }

        elseif(empty($request['apps_name']) && @$request['priority']) {
            $getData = DB::table('perusahaan')
            
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')        
            ->where('perusahaan.id_perusahaan', $request->id_perusahaan)->where('ticket.type', 'Report')        
            ->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->orderByDesc('ticket.id_ticket')->paginate(5);         
        }        

        $totalPage = $getData->lastPage();
        $data = $getData->flatten(1);

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'bug_page_total' => $totalPage,
                'dataBug' => $data
            ]);            
        } else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }            
    }
    public function indexBug(Request $request){
        $userDataBug = DB::table('perusahaan')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Report')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(2);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('ticket.type', 'Report')
                ->where('priority', request('priority'))
                ->whereNOTIn('ticket.status', function($subquery){
                    $subquery->select('ticket.status')->where('ticket.status', "Done");
                })
                ->paginate(2)
                ->appends('priority', request('priority'));
        }

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
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Request')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('ticket.id_ticket')->paginate(1);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('ticket.type', 'Request')
                ->where('priority', request('priority'))
                ->whereNOTIn('ticket.status', function($subquery){
                    $subquery->select('ticket.status')->where('ticket.status', "Done");
                })
                ->paginate(2)
                ->appends('priority', request('priority'));
        }
        
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
        $userDataDone = DB::table('perusahaan')
        ->select('application.apps_name' ,'ticket.priority', 'ticket.type', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')
        ->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.status', "Done")
        ->orderByDesc('ticket.id_ticket')->paginate(2);

        if(request()->has('priority')){
            $userDataBug = DB::table('ticket')
                ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
                ->where('ticket.status', "Done")
                ->where('priority', request('priority'))
                ->paginate(2)
                ->appends('priority', request('priority'));
        }

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
                'message'       => 'Agreement was '.$update->aproval_stat,
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

    public function uploadImageDecoded(Request $request, $id){
        $users = User::firstWhere('id', $id);
        $uploadFoler = 'userImage';
        $image = $request->photo;
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = str_random(10).'.'.'jpeg';
        Storage::disk('public')->put($imageName, base64_decode($image));
        $uploadedImageResponse = array(
            "image_name" => basename($imageName),
            "image_url" => url("storage/".$imageName),
         );
        $photo_url = $uploadedImageResponse['image_url'];
        if($users){
            $photo = User::find($id);
            $photo->update([
                'photo' => $photo_url,
            ]);
        }
        return response()->json($uploadedImageResponse, 201);
        
    }

    public function uploadImageFile(Request $request, $id){

        $users = User::firstWhere('id', $id);

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image:jpeg,png,jpg|max:2048'
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
         $uploadFolder = 'usersImage';
         $photo = $request->file('photo');
         $image_uploaded_path = $photo->store($uploadFolder, 'public');
         $uploadedImageResponse = array(
            "id_user" => $id,
            "image_name" => basename($image_uploaded_path),
            "image_url" => url("storage/".$image_uploaded_path),
            "mime" => $photo->getClientMimeType()
         );

         $photo_url = $uploadedImageResponse['image_url'];

         if($users){
            $images = User::find($id);
            $images->update([
                'photo' => $photo_url,
            ]);
        }
        return response()->json($uploadedImageResponse, 201);
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
        foreach($id_user as $target_notif){
            if(NotificationTable::where('id_user', '=', $target_notif)->where('id_ticket', '=', $id_ticket)->exists()){
                $update = DB::table('notification')->where('id_ticket', $id_ticket)->where('id_user', $target_notif)
                ->update([
                'from'  => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 1
                ]);

                return response()->json([
                    'message' => 'Success push notif',
                    'update'  => $update
                ], 200);
            }
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

    public function getFcmToken(Request $request){
        $getIdStaff = DB::table('assignment')->select('users.fcm_token')
        ->join('users', 'assignment.id_user', '=', 'users.id')
        ->where('assignment.id_ticket', $request->id_ticket)->get();
    
        $getdataTicket = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'application.apps_name', 'ticket.subject')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
        ->where('ticket.id_ticket', $request->id_ticket)->get();
        
        $fcm_token = $getIdStaff->pluck("fcm_token");
        $from = $getdataTicket->pluck('nama_perusahaan')[0];
        $appsname = $getdataTicket->pluck('apps_name')[0];
        $subject = $getdataTicket->pluck('subject')[0];
        $message = $appsname ." - ". $subject;

        return $subject;
        // $notifData = DB::table('perusahaan')->select('perusahaan.nama_perusahaan', 'application.apps_name')
        // ->join('application', 'perusahaan.id_perusahaan', '=', 'application.id_perusahaan')
        // ->where('application.id_apps', $request->id_apps)->get();

        // $getApps = $notifData->pluck('apps_name');
        // $apps_name = $getApps[0];

        // $update = Ticket::find($id_ticket);
        // $message = $apps_name." - ".$update->subject;

        // return $message;

        // $validator = Validator::make($request->all(), [
        //     'photo' => 'required|image:jpeg,png,jpg,svg|max:2048'
        // ],
        //     [
        //         'photo.required'   => 'photo Kosong !, Silahkan Masukkan photo !',
        //     ]
        // );

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Silahkan Isi Bidang Yang Kosong',
        //         'data'    => $validator->errors()
        //     ], 401);
        // }

        //  $uploadFolder = 'usersImage';
        //  $photo = $request->file('photo');
        //  $image_uploaded_path = $photo->store($uploadFolder, 'public');
        //  $uploadedImageResponse = array(
        //     "image_name" => basename($image_uploaded_path),
        //     "image_url" => Storage::disk('public')->url($image_uploaded_path),
        //     "mime" => $photo->getClientMimeType()
        //  );

        // $photo_url = $uploadedImageResponse['image_url'];
        // return $photo_url;
        // return response()->json([
        //     'message' => 'Successfull Uploaded Photo.',
        //     'data'    => $uploadedImageResponse
        // ], 201);
        // if($notif){
        //     $update = Ticket::find($id_ticket);
        //     $update->update([
        //         'from' => '',
        //         'title' => '',
        //         'message' => '',
        //         'read_at' => 0
        //     ]);
        // }

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

    public function filter(Request $request){
        
        if(request()->has('priority')){
            $filter = Ticket::where('priority', request('priority'))
                ->paginate(5)
                ->appends('priority', request('priority'));
        }else{
            $filter = Ticket::paginate(5);
        }
        return response()->json([
            "Filter Type" => $filter
        ]);
    }

}
