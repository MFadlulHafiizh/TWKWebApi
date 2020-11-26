<?php

namespace App\Http\Controllers;

use App\Assignment;
use Illuminate\Http\Request;
use Kawankoding\Fcm\FcmFacade;
use Illuminate\Support\Facades\DB;

class TwkStaffController extends Controller
{
    public function indexToDo(Request $request){
        $todoData = DB::table('assignment')->where('id_user', $request->id_user)->get();
        
    }
}
