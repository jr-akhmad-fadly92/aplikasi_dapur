<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
//import Facades Storage
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $header = "index users";
        if (request()->ajax()) {
            //$users = User::query();
            $users = User::select(['id', 'name', 'email', 'level']);
            return DataTables::of($users)
                ->addIndexColumn() // Menambah index
                ->addColumn('action', function ($row) {
                // $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                    $btn = '<a href="'. route('users_crud.edit', $row['id']) .'" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
               
        }
        return view('admin.users',compact('header'));
    }

    public function edit(string $id): View
    {
        $header = "edit users";
        //get product by ID
        $product = User::findOrFail($id);

        //render view with product
        return view('admin.edit_user', compact('product','header'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'name'         => 'required|min:5',
            'level'   => 'required|min:4',
            'email'         => 'required|min:10',
            'password'         => 'required|min:5',
        ]);

        //get product by ID
        $product = User::findOrFail($id);

        

            //update product without image
            $product->update([
                'name'          => $request->name,
                'level'         => $request->level,
                'email'         => $request->email,
                'password' => bcrypt($request->password)
                
            ]);
        

        //redirect to index
        return redirect()->route('users_crud.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
}
