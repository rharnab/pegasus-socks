<?php

namespace App\Http\Controllers\ParameterSetup\ProductCategories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductCategoriesController extends Controller
{
        
    public function index(){

    $get_data = DB::select(DB::raw("SELECT * FROM `product_categories`"));

    return view('parameter-setup.products-category.index', compact('get_data'));
   }


   public function create(){

   return view('parameter-setup.products-category.create');
   }


   public function store(Request $request){

        $starting_amt = $request->starting_amt;
        $ending_amt = $request->ending_amt;
        $short_code = $request->short_code;
        $full_name = $request->full_name;
        
       
        

        DB::table('product_categories')->insert([

            "starting_amt"=>$starting_amt,
            "ending_amt"=>$ending_amt,
            "short_code"=>$short_code,
            "full_name"=>$full_name,
            "entry_by"=>Auth::user()->id,

        ]);

      

        return redirect('parameter-setup/product_categories/index')->with('message', 'Data Inserted Successfully ');

   } //end store function



}
