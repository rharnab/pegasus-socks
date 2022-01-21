<?php

namespace App\Http\Controllers\ParameterSetup\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class BrandController extends Controller
{
     public function index(){

    $get_data = DB::table('brands')->orderBy('id','DESC')->get();

    return view('parameter-setup.brand.index', compact('get_data'));
   }


   public function create(){

   return view('parameter-setup.brand.create');
   }


   public function store(Request $request){

        $name = $request->name;
        $brand_short_code = strtoupper($request->brand_short_code);
       
       $data_count = DB::table('brands')->where('name', $name)->count();

       if ($data_count > 0) {

            return redirect('parameter-setup/brand/index')->with('warning_msg', 'This brand Already Exists ! ');
       }


        DB::table('brands')->insert([

            "name"=>$name,
            "short_code"=>$brand_short_code,
           
            "entry_by"=>Auth::user()->id,
        ]);


       



        return redirect('parameter-setup/brand/index')->with('message', 'Data Inserted Successfully ');

   } //end store function


   public function edit(Request $request){

     $id = $request->id;
    $get_data = DB::table('brands')->where('id',$id)->first();
     return view('parameter-setup.brand.edit', compact('get_data'));

   }


   public function update(Request $request){
    
    $id = $request->hidden_id;
    $name = $request->name;
    $brand_short_code = strtoupper($request->brand_short_code);

    $data_count = DB::table('brands')->where('name', $name)->count();

       if ($data_count > 0) {

            return redirect('parameter-setup/brand/index')->with('warning_msg', 'This brand Already Exists ! ');
       }
       
     DB::table('brands')->where('id', $id)->update([
          "name"=>$name,
          "short_code"=>$brand_short_code,
     ]);

      return redirect('parameter-setup/brand/index')->with('message', 'Data Updated Successfully ');

      
   } // end update function

}
