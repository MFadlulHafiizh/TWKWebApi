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
        ->where('id_user', $request->id_user)
        ->whereNOTIn('ticket.status', function($subquery){
            $subquery->select('ticket.status')->where('ticket.status', "Done");
        })->orderByDesc('assignment.id_assignment')->get();
        
        return response()->json([
            "message" => 'success',
            "todoData" => $todoData
        ]);
    }

    public function indexHasDone(Request $request){
        $hasDoneData = DB::table('assignment')
        ->join('ticket', 'assignment.id_ticket', '=', 'ticket.id_ticket')
        ->where('id_user', $request->id_user)
        ->where('ticket.status', "Done")
        ->orderByDesc('assignment.id_assignment')->get();

        return response()->json([
            'message' => "success",
            'hasDone' => $hasDoneData
        ]);
    }
}
