<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiLogs;
use Jenssegers\Agent\Agent;
use DataTables;

class ApiLogsController extends Controller
{   
    public function index()
    {
        if (request()->ajax()) {
            $apilogs = ApiLogs::select('id','user_id','ip_address','platform','browser','activity','method_function')->get();


            return DataTables::of($apilogs)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    /*$btn = '<a href="' . route('tingkatansekolah.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('tingkatansekolah.delete', $row->id) . '"
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';
                    */
                    $btn = '';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('apilogs.index');
    }

    public function store(Request $request)
    {
        $agent = new Agent();

        $log = ApiLogs::create([
            'table' => $request->table,
            'user_id' => $request->user_id,
            'ip_address' => $request->ip(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'activity' => $request->activity,
            'method_function' => $request->method_function,
            'browser_version' => $agent->version($agent->browser()),
            'is_mobile' => $agent->isMobile(),
            'is_robot' => $agent->isRobot(),
            'is_desktop' => $agent->isDesktop(),
            'referer' => $request->header('referer'),
            'agent_string' => $request->header('User-Agent'),
        ]);

        return response()->json(['message' => 'Activity logged successfully', 'data' => $log]);
    }

    public function show($id)
    {
        $log = ApiLogs::findOrFail($id);
        return response()->json($log);
    }

    public function destroy($id)
    {
        $log = ApiLogs::findOrFail($id);
        $log->delete();

        return response()->json(['message' => 'Activity log deleted successfully']);
    }
}
