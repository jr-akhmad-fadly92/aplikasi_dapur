<?php

namespace App\Http\Controllers;

//use Auth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataDapur;

class LoginController extends Controller
{
    public function halamanlogin(){
        $header = "LOGIN APLIKASI DAPUR";
        $data = DataDapur::where('id', 1)->first();
        return view('Login.Login-aplikasi',compact('header','data'));
    }


    public function postlogin(Request $request){
        if(Auth::attempt($request->only('email','password'))){
            $role = Auth::user();
            if($role->level == 'admin'){ //
                return redirect('/home');
            } else if ($role->level == 'kitchen') {
                return redirect('/dashboard_kitchen');
            } else if ($role->level == 'backoffice') {
                return redirect('/dashboard_office');
                // } else if ($role->level == 'penerimaan') {
            } else if ($role->level == 'penerimaan') {
                return redirect('/dashboard_penerimaan');
            } else if ($role->level == 'warehouse') {
                return redirect('/dashboard_warehouse');
            } else if ($role->level == 'packaging') {
                // return redirect('/dashboard_packaging');
                return redirect('/ompreng');
            } else if ($role->level == 'kepala_dapur') {
                // return redirect('/dashboard_packaging');
                return redirect('/dashboard_office');
            } else if ($role->level == 'ahli_akuntan') {
                // return redirect('/dashboard_packaging');
                return redirect('/dashboard_akuntan');
            }
            
            
            
        }    
        return redirect('/login');
    }

    public function logout(){
        Auth::logout();
        return redirect ('/login');
    }

    public function registrasi(){
        return view('Login.registrasi');
    }

    public function simpanregistrasi(Request $request){
        // dd($request->all());

        User::create([
            'name' => $request->name,
            'level' => $request->role,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'remember_token' => Str::random(60),
        ]);
        
        return redirect('/home');

    }
}
