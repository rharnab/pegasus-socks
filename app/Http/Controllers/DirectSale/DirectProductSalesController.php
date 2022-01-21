<?php

namespace App\Http\Controllers\DirectSale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class DirectProductSalesController extends Controller
{
    public function index(){
        $sql = "SELECT
                    ss.*,
                    a.name as agent_name 
                from
                    (
                    SELECT
                        agent_id,
                        voucher_no,
                        status,
                        count(*) total_socks_paid,
                        sum(ind_selling_price) as total_amount,
                        entry_date,
                        entry_time 
                    from
                        single_sales 
                    group by
                        voucher_no,
                        agent_id,
                        status
                    )
                    ss 
                    left join
                    agent_users a 
                    on ss.agent_id = a.id 
                ORDER by
                    entry_date,
                    entry_time";
        $vouchers = DB::select(DB::raw($sql));
        $data = [
            "vouchers" => $vouchers,
            "sl"       => 1
        ];
        return view('direct-sale.index', $data);
    }

    public function saleForm(){
        $agents = DB::table('agent_users')->select('name','id','mobile_number')->get();
        $data = [
            "agents" => $agents
        ];
        return view('direct-sale.single-sale', $data);
    }

    public function addNewRow(Request $request){
        $index       = $request->input('index');
        $style_codes = $request->input('style_codes');
        if(is_array($style_codes)){
            $style_list = "";
            for($i=0; $i<count($style_codes); $i++){
                $style_list .= "'$style_codes[$i]',";
            }   
            $style_list = substr_replace($style_list, "", -1);    
            $styleSql = " and st.style_code not in ($style_list) ";
        }else{
            $styleSql = "";
            $style_list = "";
        }

        $sql         = "SELECT
                st.style_code,
                bd.name as brand_name,
                bz.name as size_name,
                st.per_packet_shocks_quantity,
                st.remaining_socks,
                st.individual_buy_price,
                st.individual_sale_price,
                t.types_name
            FROM
                stocks st 
                left join
                brands bd 
                on st.brand_id = bd.id 
                left join
                brand_sizes bz 
                on st.brand_size_id = bz.id
                LEFT JOIN types t on st.type_id = t.id
                where st.remaining_socks > 0 and st.sale_type = 1 $styleSql";
        $products = DB::select(DB::raw($sql));
        $data = [
            "products" => $products,
            "index"    => $index
        ];

        $output = view('direct-sale.add-new-row', $data);
        return $output;
    }



    public function store(Request $request){
       
        $agent_id               = $request->input('agent_id');
        $products_array         = $request->input('products');
        $remaining_shocks_array = $request->input('remaining_shocks');
        $shocks_take_array      = $request->input('shocks_take');
        $total_row_count        = count($products_array);
        $total_taken_shocks     = array_sum($request->input('shocks_take'));

        $duplicate_products = $this->findDuplicate($products_array);

        $voucher_no      = $this->getVoucherNo();

        if($duplicate_products === false){

            // shocks check remaming 
            for($i=0; $i< $total_row_count; $i++){
                
                $shocks_take      = $shocks_take_array[$i]; 
                $style_code       = $products_array[$i];

                if($this->checkRemainingShocks($style_code) < $shocks_take){
                    $data = [
                        "status"   => 400,
                        "is_error" => true,
                        "message"  => "Insufficient shocks in $style_code packet"
                    ];
                    return response()->json($data);                    
                }
            }


            for($i=0; $i< $total_row_count; $i++){          
                $style_code       = $products_array[$i];
                $shocks_take      = $shocks_take_array[$i];

                if($this->insertRackProducts($agent_id, $style_code, $shocks_take, $voucher_no) === false){
                    $data = [
                        "status"   => 400,
                        "is_error" => true,
                        "message"  => "Failed to insert socks in rack"
                    ];
                    return response()->json($data);
                }                
            }

            

            $data = [
                "status"   => 200,
                "is_error" => false,
                "message"  => "$total_taken_shocks pair shocks sales successfully. Voucher No : $voucher_no"
            ];
            return response()->json($data);



        }else{
            $data = [
                "status"   => 400,
                "is_error" => true,
                "message"  => "You had selected $duplicate_products product multiple times"
            ];
            return response()->json($data);
        }        
    }



    public function insertRackProducts($agent_id, $style_code, $shocks_take, $voucher_no){
      
        $stylecodeprice  = $this->getSellingAndBuyingPrice($style_code, $shocks_take);
        $buying_price    = $stylecodeprice['single_buy'];
        $selling_price   = $stylecodeprice['single_sale'];
        $is_packet_sale  = $stylecodeprice['is_packet_sale'];
        $current_user_id = Auth::user()->id;
        $today           = date('Y-m-d');
        $time            = date('H:i:s');
       

        for($i=0; $i<$shocks_take; $i++){
            $get_style_code_shock_no = $this->getStyleCodeShockNo($style_code);
            $insertSql  = [
                "agent_id"          => $agent_id,
                "style_code"        => $style_code,
                "packet_shocks_no"  => $get_style_code_shock_no,
                "shocks_code"       => $style_code."-".$get_style_code_shock_no,
                "ind_buying_price"  => $buying_price,
                "ind_selling_price" => $selling_price,
                "is_packet_sale"    => $is_packet_sale,
                "status"            => 0,
                "entry_user_id"     => $current_user_id,
                "entry_date"        => $today,
                "entry_time"        => $time,
                "voucher_no"        => $voucher_no
            ];
            DB::table('single_sales')->insert($insertSql);    
        }

        try{
            $today = date('Y-m-d');
            DB::update("UPDATE stocks SET remaining_socks = (remaining_socks - $shocks_take),stock_out_date='$today',sales_agent_id='$agent_id', is_packet_sale=$is_packet_sale WHERE style_code='$style_code'");
            return true;
        }catch(Exception $e){
            return false;
        }
       
       
    }


    public function getSellingAndBuyingPrice($style_code, $shocks_take){
        $packet_info = DB::table('stocks')
                    ->select(
                        'per_packet_shocks_quantity', 
                        'packet_buy_price', 
                        'packet_sale_price', 
                        'individual_buy_price', 
                        'individual_sale_price', 
                        'remaining_socks'
                    )
                    ->where('style_code', $style_code)
                    ->first();
                    
        $per_packet_shocks_quantity = $packet_info->per_packet_shocks_quantity;
        $packet_buy_price           = $packet_info->packet_buy_price;
        $packet_sale_price          = $packet_info->packet_sale_price;
        $individual_buy_price       = $packet_info->individual_buy_price;
        $individual_sale_price      = $packet_info->individual_sale_price;
        $remaining_socks            = $packet_info->remaining_socks;

                        
        // check full packet buy or single buy
        if($per_packet_shocks_quantity == $shocks_take){
            $single_buy  = $packet_buy_price / $per_packet_shocks_quantity;
            $single_sale = $packet_sale_price / $per_packet_shocks_quantity;      
            $data = [
                "single_buy"     => $single_buy,
                "single_sale"    => $single_sale,
                "is_packet_sale" => 1
            ];
            return $data;
        }else{
            $data = [
                "single_buy"     => $individual_buy_price,
                "single_sale"    => $individual_sale_price,
                "is_packet_sale" => 0
            ];
            return $data;
        }
    }

    public function getVoucherNo(){
        $voucher_no = DB::table('single_sales')->max('voucher_no');
        if($voucher_no == ''){
            return 1;
        }else{
           return  $voucher_no + 1;
        }
    }




}
