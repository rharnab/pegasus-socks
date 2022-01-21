<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getStyleCodeShockNo($shock_code){
        $max_code_no = DB::table('rack_products')->where('style_code', $shock_code)->max('packet_shocks_no');
        $max_single_code = DB::table('single_sales')->where('style_code', $shock_code)->max('packet_shocks_no');


        if($max_code_no == '' && $max_single_code == ''){
            return 1;                  
        }else{
            if($max_code_no > $max_single_code){
                return $max_code_no + 1;
            }else{
                return $max_single_code + 1;
            }
        }
    }

    public function maxTodayTypeCode($rack_code, $date, $type_id){
        $max = DB::table('rack_products')
        ->where('rack_code', $rack_code)
        ->where('entry_date', $date)
        ->where('type_id', $type_id)
        ->max('shop_socks_no');
        if(!empty($max)){
            return $max + 1;
        }else{
            return 1;
        }
       
    }


    function findDuplicate ($array) {
        while (($item = array_shift($array)) !== null) {
            if (in_array($item, $array)) {
                return $item;
            }
        }
        return false;
    }



    public function checkRemainingShocks($style_code){
        $shocks = DB::table('stocks')->where('style_code', $style_code)->select('remaining_socks')->first();
        return $shocks->remaining_socks;
    }


    public function rackStoreRemainingShocks($rack_code){
        $sql = "SELECT
                (total_count - already_shock_found) as remainnig_shocks 
                FROM
                (
                    SELECT
                        total_count,
                        (
                            SELECT
                            count(*) 
                            FROM
                            rack_products 
                            WHERE
                            rack_code = '$rack_code' 
                            and (status = 0 or  status = 2)
                        )
                        as already_shock_found 
                    from
                        racks 
                    where
                        rack_code = '$rack_code' 
                )
                rack";
        $racks = DB::select(DB::raw($sql));
        return $racks[0]->remainnig_shocks;
    }


    public function getTotalShocksCommissions($total_shocks, $shop_id= 0){
        $checkDataCount = DB::table('commission_setups')->where('shop_id', $shop_id)->count();
        if($checkDataCount > 0){
            $shocks_commission = DB::table('commission_setups')->select('agent_commission_persentage', 'shop_commission_persentage')->where('shop_id', $shop_id
            )->where('statring_range', '<=', $total_shocks)->where('ending_range', '>=', $total_shocks)->first();
        }else{
            $shocks_commission = DB::table('commission_setups')->select('agent_commission_persentage', 'shop_commission_persentage')->where('statring_range', '<=', $total_shocks)->where('ending_range', '>=', $total_shocks)->first();
        }
        
        
        $data = [
            "agent_commission_persentage" => $shocks_commission->agent_commission_persentage ?? 0,
            "shop_commission_persentage" => $shocks_commission->shop_commission_persentage ?? 0
        ];
        file_put_contents('m.txt', json_encode($data));
        return $data;
    }

    public function  singleShockSellPrice($shocks_code){
        $shock = DB::table('rack_products')->where('shocks_code', $shocks_code)->select('selling_price')->first();
        return $shock->selling_price ?? 0;
    }


    public function rackFillVoucherStore($rack_code='', $date=''){
        
    }


    public function socksLog($shock_code, $message){
        DB::table('socks_log')->insert([
            "socks_code"         => $shock_code,
            "message"            => $message,
            "operation_user_id"  => Auth::user()->id,
            "operation_datetime" => date('Y-m-d H:i:s')
        ]);
    }



}
