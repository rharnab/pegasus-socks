<?php

namespace App\Http\Controllers\ParameterSetup\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
Use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request){

       $get_data = DB::select(DB::raw("SELECT p.id, b.name as brand_name,bs.name as brand_size_name, types.types_name, p.packet_socks_pair_quantity, p.packet_buying_price,p.packet_selling_price, p.individual_buying_price,p.individual_selling_price, p.entry_date, p.entry_time, p.sale_type   FROM `products` p left join brands b on p.brand_id=b.id left join brand_sizes bs on p.brand_size_id=bs.id left join types on p.type_id=types.id"));

       return view('parameter-setup.product.index', compact('get_data'));   
       
    }

    public function create(){

            $brands =  DB::table('brands')->get();
            $brand_sizes =  DB::table('brand_sizes')->get();
            $types =  DB::table('types')->get();

            return view('parameter-setup.product.create', compact('brands','brand_sizes','types'));

    }   


    public function store(Request $request){
        $brand = $request->brand;
        $brand_sizes = $request->brand_sizes;
        $type = $request->type;
        $packet_socks_pair_quantity = $request->packet_socks_pair_quantity;

        $packet_buying_price = $request->packet_buying_price;
        $packet_selling_price = $request->packet_selling_price;
        $ind_buying_price = $request->ind_buying_price;
        $ind_selling_price = $request->ind_selling_price;
        $select_type = $request->select_type;


        DB::table('products')->insert([
            "brand_id"=>$brand,
            "brand_size_id"=>$brand_sizes,
            "type_id"=>$type,
            "packet_socks_pair_quantity"=>$packet_socks_pair_quantity,
            "packet_buying_price"=>$packet_buying_price,
            "packet_selling_price"=>$packet_selling_price,
            "individual_buying_price"=>$ind_buying_price,
            "individual_selling_price"=>$ind_selling_price,
            "entry_user_id"=>Auth::user()->id,
            "entry_date"=>date('Y-m-d'),
            "entry_time"=>date('h:i:s a'),
            "sale_type"=>$select_type
        ]);


        return redirect('parameter-setup/product/index')->with('message',"Data Inserted Successfully");


    }
}
