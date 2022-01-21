<?php

namespace App\Http\Controllers\ParameterSetup\Racks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RacksController extends Controller
{
    
    public function index(){

    $get_data =DB::select(DB::raw("SELECT r.id,r.rack_code,r.rack_category,r.rack_level,r.per_level_socks,r.total_count,rm.status FROM `racks`  r 
    LEFT JOIN rack_mapping rm on rm.rack_code = r.rack_code"));

    return view('parameter-setup.racks.index', compact('get_data'));
   }


   public function create(){

   return view('parameter-setup.racks.create');
   }


   public function store(Request $request){

        $rack_category = $request->rack_category;
        $rack_level    = $request->rack_level;
        $total_count   = $request->total_count;
       
        $per_level_socks = $total_count/$rack_level;

        $rack_code = DB::table('racks')->count();
        $rack_code = "VLS".($rack_code + 1);

        $last_inserted_id = DB::table('racks')->insertGetId([
            "rack_category"   => $rack_category,
            "rack_level"      => $rack_level,
            "total_count"     => $total_count,
            "rack_code"       => $rack_code,
            "status"          => 0,
            "per_level_socks" => $per_level_socks,
            "entry_user_id"   => Auth::user()->id
        ]);

      

        return redirect('parameter-setup/racks/index')->with('message', 'Data Inserted Successfully ');

   } //end store function

}
