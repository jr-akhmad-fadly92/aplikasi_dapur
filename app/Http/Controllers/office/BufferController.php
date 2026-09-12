<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Models\Buffer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BufferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header     = "Data Buffer";
       
        if (request()->ajax()) {
            //$users = User::query();

            $Buffer = Buffer::all();


            return DataTables::of($Buffer)
                ->addIndexColumn() // Menambah index
                ->addColumn('action', function ($row) {
                    $action = '<button class="btn btn-sm btn-warning btn-edit-buffer" 
                        data-id="' . $row->id . '" 
                        data-menu="' . $row->buffer_menu . '"
                        data-po="' . $row->buffer_po . '">
                        Edit
                    </button>
                    ';
                    return $action;
                    
                })
                ->addColumn('menu', function ($row) {
                   
                    return $row->buffer_menu . ' %';

                })
                ->addColumn('po', function ($row) {

                    return $row->buffer_po . ' %';
                })
                ->rawColumns(['action', 'po', 'menu'])
                ->make(true);
        }
        return view('office/buffer.index', compact('header'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'menu' => 'required|integer|min:0|max:2',
            'po' => 'required|integer|min:0|max:2',
        ]);

        $Buffer = Buffer::findOrFail($id);
        $Buffer->buffer_menu = $request->menu;
        $Buffer->buffer_po = $request->po;
        $Buffer->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
