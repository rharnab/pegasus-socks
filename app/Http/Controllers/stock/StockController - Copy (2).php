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

    public function index()
    {
    	$result= DB::select('SELECT s.*, pc.short_code, pc.full_name, b.name as brand_name, bs.name as size_name FROM stocks s left join product_categories pc on pc.id = s.product_category_id left join brands b on b.id= s.brand_id left join brand_sizes bs on bs.id = s.brand_size_id');

    	
    	return view('stock.index', compact('result'));
    }


    public function creat()
    {
    	$brands = DB::table('brands')->orderBy('name', 'asc')->get();
    	$sizes =DB::table('brand_sizes')->orderBy('name', 'asc')->get();
        $types =DB::table('types')->orderBy('types_name', 'asc')->get();
    	$lot_result = DB::table('lots')->select('lot_no')->orderBy('lot_no', 'desc')->first();
    	if(!empty($lot_result))
    	{
    		$lot_no = $lot_result->lot_no + 1;
    	}else{
    		$lot_no=1;
    	}


    	return view('stock.stock_create', compact('brands', 'sizes', 'lot_no', 'types'));
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




    	foreach($request->addmore as $data)
    	{
    		$getProductCat = $this->getProductCat(46.55);
	    	echo $getProductCat['short_code']."<br>";
	    	echo $getProductCat['catgory_id']."<br>";
	    	echo "<hr>";
    	}

    	

    	die;


  
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
    			$individual_buy_price = $productInfo['inv_buy_price'];
    			$individual_sale_price = $productInfo['inv_sale_price'];
    			$lot_no = $productInfo['lot_no'];
    			$rate_date=date('Y-m-d');
    			if(!empty($individual_buy_price))
    			{

    				$product_category_id =  $this->getProductCat((INT) $individual_sale_price); /*product category*/
	    			$p_fist_number =  $this->priceFirstDgt((INT) $individual_sale_price); /*product category*/
	    			$p_last_number =  $this->getlast((INT) $individual_sale_price); /*product category*/

    			}else{

    				$product_category_id =  '';
	    			$p_fist_number =  '';
	    			$p_last_number =  '';
    			}
    			
    			if(!empty($individual_buy_price))
    			{

    				for($i=1; $i <= $pkt_qty; $i++)
	    			{
	    				$style_code = $lot_no.$p_fist_number.$product_category_id['short_code'].$p_last_number.$i;

	    				/*product insert*/
	    				$product_insert = DB::table('stocks')->insert([

	    					'brand_id' => $brand_id,
	    					'style_code'=>$style_code,
	    					'brand_size_id' =>$brand_size_id,
	    					'per_packet_shocks_quantity' =>$per_packet_shocks_quantity,
	    					'packet_buy_price'=>str_replace(',', '', $packet_buy_price),
	    					'packet_sale_price'=>str_replace(',', '', $packet_sale_price),
	    					'individual_buy_price' =>str_replace(',', '',  $individual_buy_price),
	    					'individual_sale_price' => str_replace(',', '', $individual_sale_price),
	    					'product_category_id'=>$product_category_id['catgory_id'],
	    					'remaining_socks'=>$per_packet_shocks_quantity,
	    					'lot_no'=>$lot_no,
	    					'entry_user_id'=>$entry_by,
	    					'entry_date_time'=>$today,
	    					'stock_in_date'=>date('Y-m-d'),


	    				]);

	    				$total_shocks = $total_shocks + $per_packet_shocks_quantity;
	    				$total_product = $total_product + 1;
	    				$total_buying_price = $total_buying_price  + $packet_buy_price;
	    			    $total_saling_price = $total_saling_price  + $packet_sale_price;

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

    			}

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

    		} //lot product

    	} //end if
    }


    public function getProductCat($price)
    {
    	$result = DB::table('product_categories')->select('short_code', 'id' )->where('starting_amt', '<=', $price)->where('ending_amt', '>=', $price)->first();
    	file_put_contents("m.txt", json_encode($result)." = ". $price, FILE_APPEND);
    	return	$result_array = array('short_code'=> $result->short_code, 'catgory_id'=> $result->id);
    	 
    	
    	 

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


    function getProductDetails()
    {
        $product_details = DB::select("SELECT * FROM products where brand_id=19 and brand_size_id=5 and type_id=2 and packet_socks_pair_quantity=12 order by id desc limit ");
    }

}
