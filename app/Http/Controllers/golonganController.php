<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Golongan;
use App\Models\golonganBahan;
use DataTables;

class golonganController extends Controller
{
    //
    public function v_golongan()
    {
        $header = "Data golongan";
        return view('office.golongan.golongan', compact('header'));
    }

    public function dt_golongan()
    {
        $table = Golongan::get();
        return DataTables::of($table)
            ->addColumn('action', function ($table) {
                return '<a href="#" class="btn btn-xs btn-primary" id="edit" data-id="' . $table->id . '"><i class="fa fa-edit
                "></i> Edit</a>
                <a href="#" class="btn btn-xs btn-danger" id="delete" data-id="' . $table->id . '"><i class="fa fa-trash"></i> Delete</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function ajax_getGolongan()
    {
        $golongan = Golongan::get();
        $data = $golongan->map(function($gol){
            return [
                'id' => $gol->id,
                'parent' => $gol->parent_id,
                'name' => $gol->golongan,
            ];
        });
        return response()->json($data);
    }

    public function v_formGolongan(Request $request)
    {
        //return $request->id;
        $parent_golongan = Golongan::all();
        if(isset($request->id)){
            if($request->id != 'undefined'){
                $golongan = Golongan::find($request->id);
                return view('office.golongan.form.formGolongan', compact('golongan', 'parent_golongan'));
        }}
        $child_golongan_id =$request->id_parent;
        //return $child_golongan_id;
        return view('office.golongan.form.formGolongan', compact('parent_golongan', 'child_golongan_id'));
    }

    public function ajax_simpanGolongan(Request $request)
    {
        //return "adf";
        if(!isset($request->id)){
            $golongan = Golongan::create($request->all());
            if($golongan){
                return response()->json(['message'=>'Berhasil disimpan', 'status'=>'success']);
            }
            return response()->json(['message'=>'gagal disimpan', 'status'=>'error']);
        }
        $golongan = Golongan::find($request->id);
            $golongan->update($request->except('_token'));
            if($golongan){
                return response()->json([
                    'status' => 'success',
                    'message' => 'data berhasil diupdate',
                ]);
            }
            return response()->json(['message'=>'gagal disimpan', 'status'=>'error']);

    }

    public function ajax_deleteGolongan(Request $request)
    {
        //cek punya turunan atau tidak
        //return $request->id;
        $count = Golongan::where('parent_id', $request->id)->count();
        //return $count;
        if($count>0){
            return response()->json([
                'status' => 'error',
                'message' => 'golongan mempunyai turunan & tidak bisa dihapus',
                'title' => 'Gagal'
            ]);
        }
        $golongan = Golongan::destroy($request->id);
        if($golongan){
            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil',
                'message' => 'data berhasil dihapus'
            ]);
        }
    }
}
