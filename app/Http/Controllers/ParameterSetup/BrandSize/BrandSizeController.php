<?php

namespace App\Http\Controllers\ParameterSetup\BrandSize;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class BrandSizeController extends Controller
{
    public function index(){

    $get_data =DB::select(DB::raw("SELECT bs.id, bs.name as brand_sise_name FROM `brand_sizes` bs  order by bs.id desc"));

    return view('parameter-setup.brand-size.index', compact('get_data'));
   }


   public function create(){

   return view('parameter-setup.brand-size.create');
   }


   public function store(Request $request){

        $name = $request->name;
      
       $data_count = DB::table('brand_sizes')->where('name',$name)->count();

       if ($data_count > 0) {
           return redirect('parameter-setup/brandsize/index')->with('warning_msg', 'This Size Already Exists ! ');
       }

        DB::table('brand_sizes')->insert([

            "name"=>$name,
          
            "entry_by"=>Auth::user()->id,

        ]);


    

        return redirect('parameter-setup/brandsize/index')->with('message', 'Data Inserted Successfully ');

   } //end store function
}
