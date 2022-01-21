<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->role_id == 2){
            return redirect()->route('agent.home');
        }

        if(Auth::user()->role_id == 6){
            return redirect()->route('shopkeeper.home');
        }

        if(Auth::user()->role_id == 4){
            return 'fddf';
        }
        

        if((Auth::user()->role_id == 1) or (Auth::user()->role_id == 3)){ // admin
            return redirect()->route('admin.home');            
        }

        return view('home');
        
    }


    public function password_change()
    {
        return view('auth.passwords.change-password');
    }

    public function password_change_save(Request $request)
    {
      $old_password  = $request->input('old_password');
      $password  = $request->input('password');
      $password_confirmation  = $request->input('password_confirmation');

       $validator = $request->validate([
            'old_password' => 'required',
            'password' => 'required',
            'password_confirmation' =>'required',
        ]);

        

        

    if (Hash::check($request->old_password, Auth::user()->password))
    {
         $new_password = Hash::make($password);
         $update= DB::table('users')->where('id', Auth::user()->id)->update([
            'password' => $new_password,
         ]);
         
         if($update)
         {
            //  $data = [
            //      'status' => 200,
            //      'is_error' =>'N',
            //      'message' => 'Password change successuflly'
            //  ];

            $request->session()->flush();
            return redirect()->route('login');
         }

    }else{
        $data = [
            'status' => 400,
            'is_error' =>'Y',
            'message' => 'Sorry ! Current  password not match',
        ];
    }

    return $data;

   
        
      

      //'password' => Hash::make($data['password']),



    }
}
