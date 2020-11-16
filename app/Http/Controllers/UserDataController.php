<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use App\ReportBug;
use App\FeatureRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserDataController extends Controller
{
    public function indexBug(Request $request){
        $userDataBug = DB::table('perusahaan')
        ->select('application.apps_name', 'ticket.type','ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Report')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->get();

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            "message" => 'success',
            "bugData" => $userDataBug
        ]);
    }

    public function indexFeature(Request $request){
        $userDataFeature = DB::table('perusahaan')
        ->select('application.apps_name','ticket.type','ticket.priority','ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.type', 'Request')
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->get();

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            "message" => "success",
            "featureData" => $userDataFeature
        ]);
    }

    
    public function indexDone(Request $request){
        $userDataDone = DB::table('perusahaan')->select('application.apps_name','ticket.priority', 'ticket.type', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at')
        ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
        ->join('ticket','application.id_apps','=','ticket.id_apps')->where('perusahaan.id_perusahaan', $request->id_perusahaan)
        ->where('ticket.status', "Done")->get();

        $this ->validate($request, [
            "id_perusahaan"=>"required"
        ]);

        return response()->json([
            "message" => "success",
            "doneData" => $userDataDone
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

        $input = $request->all();
        $adminToken = 'frNgDWHH_0A:APA91bGrQ1AJCSUADO0vrlAO6myzd9gq4-mDuvMww_4kOS3O2fy4bw0AjIQjDe9crwHkU4DAOHaYS3tYFygp6IDTqkovt7u1IhSnJsCHoRrSFjpzsOE5d1uyq_wGzfIaVVIFMtEJpVHA';

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
            'notif' => $this->pushNotifBug($adminToken)
        ]);
    }

    public function pushNotifBug($adminToken){
        $recipients = [$adminToken];//, 'frNgDWHH_0A:APA91bGrQ1AJCSUADO0vrlAO6myzd9gq4-mDuvMww_4kOS3O2fy4bw0AjIQjDe9crwHkU4DAOHaYS3tYFygp6IDTqkovt7u1IhSnJsCHoRrSFjpzsOE5d1uyq_wGzfIaVVIFMtEJpVHA'];
        fcm()
        ->to($recipients)
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => 'New bugs reported',
            'body' => 'This is a test of FCM',
        ])->send();
    }

    public function storeFeature(Request $request){

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
            'message' => 'Your request has sended'
        ]);
    }

    public function uploadImage(Request $request){
        
        $data = User::create([
            'photo' => $request->file('photo'),
        ]);

        if($request->hasFile('photo')){
            $request->file('photo')->move('uploads/', $request->file('photo')->getUserOriginalName());
            $data->photo = $request->file('photo')->getUserOriginalName();
            $data->save();
        };

        return response()->json([
            "status" => "Created Image",
            "message" => "Success Upload Image",
            "data" => $data
        ], 201);
    }

}
