<?php

namespace App\Http\Controllers\stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
//use  App\Http\Controllers\stock\getProductCat;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

         
    }

    public function creat()
    {
    	$brands = DB::table('brands')->orderBy('name', 'asc')->get();
    	$sizes =DB::table('brand_sizes')->orderBy('name', 'asc')->get();

    	$lot_result = DB::table('lots')->select('lot_no')->orderBy('lot_no', 'desc')->first();
    	if(!empty($lot_result))
    	{
    		$lot_no = $lot_result->lot_no + 1;
    	}else{
    		$lot_no=0;
    	}


    	return view('stock.stock_create', compact('brands', 'sizes', 'lot_no'));
    }

    public function store(Request $request)
    {
    	$products= $request->addmore;
    	$total_product=0;
    	$total_shocks=0;
    	$total_buying_price=0;
    	$total_saling_price=0;
    	$today = date('Y-m-d h:i:s');
    	$entry_by = Auth::user()->id;

    	/*last lot number*/
    	$lot_result = DB::table('lots')->select('lot_no')->orderBy('lot_no', 'desc')->first();
    	if(!empty($lot_result))
    	{
    		$lot_no = $lot_result->lot_no + 1;
    	}else{
    		$lot_no=0;
    	}


  
    	if(count($products) > 0)
    	{
    		foreach($products as $productInfo)
    		{

    			$pkt_qty = $productInfo['pkt_qty'];

    			$brand_id = $productInfo['brand'];
    			$brand_size_id = $productInfo['size'];
    			$per_packet_shocks_quantity = $productInfo['per_pkt_qty'];
    			$packet_buy_price = $productInfo['pkt_buying_price'];
    			$packet_sale_price = $productInfo['pkt_saling_price'];
    			$individual_buy_price = $packet_buy_price / $per_packet_shocks_quantity;
    			$individual_sale_price = $packet_sale_price / $per_packet_shocks_quantity;
    			$rate_date=date('Y-m-d');
    			$product_category_id =  $this->getProductCat($individual_buy_price); /*product category*/
    			$p_fist_number =  $this->priceFirstDgt($individual_buy_price); /*product category*/
    			$p_last_number =  $this->getlast($individual_buy_price); /*product category*/
    			

    			for($i=0; $i < $pkt_qty; $i++)
    			{
    				$style_code = $lot_no.$p_fist_number.$product_category_id.$p_last_number.'-'.$i;

    				/*product insert*/
    				$product_insert = DB::table('products')->insert([

    					'brand_id' => $brand_id,
    					'style_code'=>$style_code,
    					'brand_size_id' =>$brand_size_id,
    					'per_packet_shocks_quantity' =>$per_packet_shocks_quantity,
    					'packet_buy_price'=>str_replace(',', '', $packet_buy_price),
    					'packet_sale_price'=>str_replace(',', '', $packet_sale_price),
    					'individual_buy_price' =>str_replace(',', '',  $individual_buy_price),
    					'individual_sale_price' => str_replace(',', '', $individual_sale_price),
    					'rate_date'=>$rate_date,
    					'product_category_id'=>'1',
    					'lot_no'=>$lot_no,
    					'entry_user_id'=>$entry_by,
    					'entry_date_time'=>$today,
    					'stock_in_date'=>date('Y-m-d'),


    				]);

    			}

    			if($product_insert)
    			{

    				$lot_product_insert = DB::table('lot_products')->insert([
	    				'lot_no' => $lot_no,
	    				'brand_id'=>$brand_id,
	    				'brand_size_id'=>$brand_size_id,
	    				'packet_qantity'=> $pkt_qty

	    			]);
    			}
    			


    			$total_shocks = $total_shocks + ($pkt_qty * $per_packet_shocks_quantity);
    			$total_product = $total_product + 1;
    			$total_buying_price = $total_buying_price  + ($total_shocks * $individual_buy_price);
    			$total_saling_price = $total_saling_price  + ($total_shocks * $individual_sale_price);
    			


    		}


    		if($lot_product_insert)
    		{

    			/*lots insert*/
    				$lots  = DB::table('lots')->insert([
	    			'lot_no' => $lot_no,
	    			'total_product'=>$total_product,
	    			'total_shocks'=>str_replace(',', '', $total_shocks),
	    			'total_buying_price'=>str_replace(',', '', $total_buying_price),
	    			'total_saling_price'=>str_replace(',', '', $total_saling_price),
	    			'entry_user_id'=>$entry_by,
	    			'entry_datetime' =>$today


	    		]);


    				if($lots)
    				{

    					$response = [
		                    "status" => 200,
		                    "success" => true,
		                    "message" => "Product stock Added Sucessfullyl",
		                ];

		                return response()->json($response);

    				}else{

    					$response = [
		                    "status" => 200,
		                    "success" => true,
		                    "message" => "Product stock not Added yet",
		                ];

		                return response()->json($response);
    				}




    				 

    		}


    		



    		

    	}
    }


    public function getProductCat($price)
    {
    	$result = DB::table('product_categories')->select('*')->where('starting_amt', '<=', $price)->where('ending_amt', '>=', $price)->limit(1)->get();

    	 $cat_result = json_decode($result, true);

    	 return 'S';

    	//return strtoupper($cat_result[0]['short_code']);


    }

    public function priceFirstDgt($num)
    {
    	$str_number=strval($num);
		return $first_number = $str_number[0];
		
    }

    function getlast($num)
	{
	    return $last_number = $num % 10;
	}

}
