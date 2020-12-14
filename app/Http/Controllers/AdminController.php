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

    public function indexBugAdmin(Request $request){
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])) {
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->where('ticket.type', 'Report')
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //1 Kondisi
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')->where('application.apps_name', $request->apps_name)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);

        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }


        //2 kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('ticket.priority', $request->priority)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }


        //3 kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //ada semua
        elseif(@$request['apps_name'] && @$request['priority'] && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataBug = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.type', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Report')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        $totalPage = $adminDataBug->lastPage();
        $data = $adminDataBug->flatten(1);

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

    public function indexFeatureAdmin(Request $request){
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])) {
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //1 Kondisi
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')->where('application.apps_name', $request->apps_name)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);

        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }


        //2 kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['assigned']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.priority', $request->priority)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('ticket.priority', $request->priority)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
    
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }


        //3 kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && @$request['assigned'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('ticket.priority', $request->priority)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['assigned']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //ada semua
        elseif(@$request['apps_name'] && @$request['priority'] && @$request['assigned'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataFeature = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'ticket.id_ticket', 'application.apps_name', 'ticket.priority', 'ticket.subject', 'ticket.detail', 'ticket.status', 'ticket.created_at', 'ticket.time_periodic', 'ticket.price', 'ticket.aproval_stat', 'assignment.assign_at')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->leftJoin('assignment', 'ticket.id_ticket', '=', 'assignment.id_ticket')
            ->where('ticket.type', 'Request')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', $request->assigned)
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->whereNOTIn('ticket.status', function($subquery){
                $subquery->select('ticket.status')->where('ticket.status', "Done");
            })->groupBy('ticket.id_ticket')->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        $totalPage = $adminDataFeature->lastPage();
        $data = $adminDataFeature->flatten(1);

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'fitur_page_total' => $totalPage,
                'featureData' => $data
            ]);            
        } else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }       
    }

    public function indexDoneAdmin(Request $request){
        if(empty($request['apps_name']) && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //1 kondisi
        elseif(@$request['apps_name'] && empty($request['priority']) && empty($request['dari']) && empty($request['sampai'])){
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('ticket.priority', $request->priority)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && empty($request['priority']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        //2 kondisi
        elseif(@$request['apps_name'] && @$request['priority'] && empty($request['dari']) && empty($request['sampai'])){
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(@$request['apps_name'] && empty($request['priority']) && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }
        elseif(empty($request['apps_name']) && @$request['priority'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('ticket.priority', $request->priority)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }

        elseif(@$request['apps_name'] && @$request['priority'] && @$request['dari'] && @$request['sampai']){
            $dari = $request->dari;
            $sampai = $request->sampai;
            $adminDataDone = DB::table('perusahaan')
            ->join('application','perusahaan.id_perusahaan','=','application.id_perusahaan')
            ->join('ticket','application.id_apps','=','ticket.id_apps')
            ->where('ticket.priority', $request->priority)
            ->where('application.apps_name', $request->apps_name)
            ->whereDate('updated_at', '>=', $dari)
            ->whereDate('updated_at', '<=', $sampai)
            ->where('ticket.status', "Done")
            ->orderByDesc('ticket.id_ticket')->paginate(10);
        }


        $totalPage = $adminDataDone->lastPage();
        $data = $adminDataDone->flatten(1);

        if(isset($data[0])) {
            return response()->json([
                'message' => "success",
                'done_page_total' => $totalPage,
                'doneData' => $data
            ]);            
        } else {
            return response()->json([
                'message' => 'No Data Available'
            ]);
        }       
    }

    public function getTicketApps(){
        $ticketAppsAvailable = DB::table('ticket')->select('application.apps_name','application.id_apps')
        ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
        ->groupBy('application.apps_name')->get();

        return response()->json([
            "message" => "Success",
            "userApp" => $ticketAppsAvailable
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
                'notif'   => $this->pushNotif($target_notif, $fcm_token, $id_ticket, "PT.Triwikrama", "Your Request need agreement", $appsName."-".$subjectTicket)
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

    public function changeStatus($id_ticket){
        $ticket = Ticket::firstWhere('id_ticket', $id_ticket);

        if($ticket){
            $update = Ticket::find($id_ticket);
            $update->update([
                'status' => 'On Proccess'
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

    public function assignTask(Request $request){
        $sendNotif = DB::table('ticket')->select('users.id', 'users.fcm_token')
            ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
            ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
            ->join('users', 'perusahaan.id_perusahaan', '=', 'users.id_perusahaan')
            ->where('ticket.id_ticket', $request->id_ticket)->get();
        $getTicketInfo = DB::table('application')->select('ticket.subject', 'application.apps_name', 'ticket.type')
            ->join('ticket', 'application.id_apps', '=', 'ticket.id_apps')
            ->where('ticket.id_ticket', $request->id_ticket)->get();

        $target_notif = $sendNotif->pluck('id');
        $fcm_token = $sendNotif->pluck('fcm_token');
        $appsName = $getTicketInfo->pluck('apps_name')[0];
        $subjectTicket = $getTicketInfo->pluck('subject')[0];
        $type = $getTicketInfo->pluck('type')[0];

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
                if($type == "Report"){
                    $this->pushNotif($target_notif, $fcm_token, $request->id_ticket, "PT.Triwikrama", "Your report was processed", $appsName."-".$subjectTicket);
                }else{
                    $this->pushNotif($target_notif, $fcm_token, $request->id_ticket, "PT.Triwikrama", "Your request was processed", $appsName."-".$subjectTicket);
                }
                return response()->json([
                    'success'       => true,
                    'status'        => $this->changeStatus($request->id_ticket),
                    'message'       => 'Post Berhasil Disimpan!',
                    'notif'         => $this->assignSendNotif($request->id_user, $request->id_ticket, "You have something to do")
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Post Gagal Disimpan!',
                ], 401);
            }
        }
    
    }

    public function assignSendNotif($target_user, $id_ticket, $title){
    $getIdStaff = DB::table('assignment')->select('users.fcm_token')
    ->join('users', 'assignment.id_user', '=', 'users.id')
    ->where('assignment.id_ticket', $id_ticket)->get();

    $getdataTicket = DB::table('ticket')->select('perusahaan.nama_perusahaan', 'application.apps_name', 'ticket.subject')
    ->join('application', 'ticket.id_apps', '=', 'application.id_apps')
    ->join('perusahaan', 'application.id_perusahaan', '=', 'perusahaan.id_perusahaan')
    ->where('ticket.id_ticket', $id_ticket)->get();
    
    $fcm_token = $getIdStaff->pluck("fcm_token");
    $from = $getdataTicket->pluck('nama_perusahaan')[0];
    $appsname = $getdataTicket->pluck('apps_name')[0];
    $subject = $getdataTicket->pluck('subject')[0];
    $message = $appsname ." - ". $subject;
    return $this->pushNotif($target_user, $fcm_token, $id_ticket, $from, $title, $message);
        
    }

    public function pushNotif($target_user, $fcm_token, $id_ticket, $from, $title, $message){

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
        
        foreach($target_user as $target_notif){
            if(NotificationTable::where('id_user', '=', $target_notif)->where('id_ticket', '=', $id_ticket)->exists()){
                $update = DB::table('notification')->where('id_ticket', $id_ticket)->where('id_user', $target_notif)
                ->update([
                'from'  => $from,
                'title' => $title,
                'message' => $message,
                'read_at' => 1
                ]);
            }else{
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