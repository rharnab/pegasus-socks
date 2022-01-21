<?php

namespace App\Http\Controllers\Agent\SoldDelete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class RackWrongSoldItemDeteController extends Controller
{
    public function rackList(){
        $agent_user_id = Auth::user()->agent_id;
        $shop_racks_sql = "SELECT
                        rack.rack_code,
                        s.name as shop_name 
                    FROM
                        (
                        SELECT
                            rack_code,
                            shop_id 
                        from
                            rack_products 
                        where
                            agent_id = '$agent_user_id' and status=1
                        GROUP by
                            shop_id,
                            rack_code
                        )
                        rack 
                        left join
                        shops s 
                        on rack.shop_id = s.id";
        $shop_racks = DB::select(DB::raw($shop_racks_sql));
        $data = [
            "shop_racks" => $shop_racks
        ];
        return view('agent.sold-delete.rack-list', $data);
    }

    // show rack sold details
    public function rackSoldInformation($rack_code){
        $rack_code = Crypt::decrypt($rack_code);
        $rack_shocks_array = [];

        $sql ="SELECT
                    t.types_name,
                    t.id,
                    sum(rp.total) as socks_pair 
                FROM
                    (
                    SELECT
                        style_code,
                        count(*) as total 
                    FROM
                        rack_products 
                    WHERE
                        rack_code = '$rack_code' 
                        and 
                        (
                            status = 1
                        )
                    GROUP by
                        style_code 
                    )
                    rp 
                    LEFT JOIN
                    stocks st 
                    on rp.style_code = st.style_code 
                    LEFT JOIN
                    types t 
                    on st.type_id = t.id 
                GROUP by
                    t.types_name 
                ORDER by
                t.sl_priority asc";

        $rack_style_sizes = DB::select(DB::raw($sql));

        foreach($rack_style_sizes as $rack_style_size){
            $rack_shocks_array[$rack_style_size->id] = [
                "type_id" => $rack_style_size->id,
                "name"    => $rack_style_size->types_name,
                "total"   => $rack_style_size->socks_pair,
                "shocks"  => []
            ];
        }


        foreach ($rack_style_sizes as $rack_style_size) {
            $type_id = $rack_style_size->id;
            $shocks = DB::select(DB::raw("SELECT
                            rp.shocks_code,
                            rp.printed_socks_code,
                            rp.shop_socks_code,
                            bd.name as brand_name,
                            bz.name as brand_size_name,
                            t.id as type_id 
                        FROM
                            `rack_products` rp 
                            left JOIN
                            stocks st 
                            on rp.style_code = st.style_code 
                            left JOIN
                            brands bd 
                            on st.brand_id = bd.id 
                            LEFT JOIN
                            brand_sizes bz 
                            on st.brand_size_id = bz.id 
                            LEFT JOIN
                            types t 
                            on st.type_id = t.id 
                        where
                            rp.rack_code = '$rack_code' 
                            and t.id = '$type_id' 
                            and rp.status = 1"));
            foreach ($shocks as $shock) {
                $single_shocks = [
                    "shocks_code"       => $shock->shocks_code,
                    "print_shocks_code" => $shock->printed_socks_code,
                    "shop_socks_code"   => $shock->shop_socks_code,
                    "brand_name"        => $shock->brand_name,
                    "brand_size_name"   => $shock->brand_size_name,
                    "shocks_type_id"    => $shock->type_id
                ];
                array_push($rack_shocks_array[$rack_style_size->id]['shocks'], $single_shocks);
            }
        }


        $rack_info = DB::select(DB::raw("SELECT
                        rp.*,
                        s.name as shop_name 
                    FROM
                        (
                        SELECT
                            shop_id,
                            rack_code 
                        from
                            rack_products 
                        WHERE
                            rack_code = '$rack_code' 
                        GROUP by
                            rack_code,shop_id
                        )
                        rp 
                        left join
                        shops s 
                        on rp.shop_id = s.id"));

        $data = [
            "rack_shocks_array" => $rack_shocks_array,
            "rack_info"         => $rack_info
        ];  
        return view('agent.sold-delete.single-rack', $data);
    }


    // calculate delete socks and amount
    public function calculateSocks(Request $request){
        if($request->has('shocks')){
            $shocks           = $request->input('shocks');
            $shop_id          = $request->input('shop_id');
            $total_shocks     = count($shocks);
            $total_amount     = 0;

            for($i=0; $i<$total_shocks; $i++){
                $total_amount += $this->singleShockSellPrice($shocks[$i]);
            }
            $data = [
                "total_shocks" => $total_shocks,
                "total_amount" => $total_amount
            ];
            $html_render = view('agent.sold-delete.commission-calculation', $data);
            return $html_render;
        }else{
            return ' <div class="col-6 ">
            <div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
                <div class="">
                    <h3 class="display-4 d-block l-h-n m-0 fw-500">
                        <span id="selected_shocks" style="font-size: 18px;">0 Pair</span>
                        <small class="m-0 l-h-n">Sales Socks Pair</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-6 ">
            <div class="p-3 bg-warning-400 rounded overflow-hidden position-relative text-white mb-g">
                <div class="">
                    <h3 class="display-4 d-block l-h-n m-0 fw-500">
                        <span id="selected_shocks" style="font-size: 18px;">0 TK</span>
                        <small class="m-0 l-h-n">Total Bill</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn  btn-sm btn-danger waves-effect waves-themed w-100" disabled >  
                SELECT SOCKS PAIR
            </button>
        </div>
';
        }
    }



    // unsold mark socks
    public function unsoldSoldItems(Request $request){
        if($request->has('shocks')){
            $shocks                  = $request->input('shocks');
            $rack_code               = $request->input('rack_code');
            $shop_id                 = $request->input('shop_id');
            $total_shocks            = count($shocks);
            
            for($i=0; $i<$total_shocks; $i++){
                $shock_code = $shocks[$i];
                DB::table('rack_products')->where('shocks_code', $shock_code)->update([
                    "status"              => 0,
                    "sold_mark_date_time" => '',
                    "sold_date"           => '0000-00-00',
                    "sold_mark_user_id"   => '',
                ]);

                $shocks_info = DB::table('rack_products')->where('shocks_code', $shock_code)->first();
                $this->socksLog($shocks_info->id, "WRONG_SOLD_SOCKS_UNSOLD_MARK_BY_AGENT");
            }


            $data = [
                "status"   => 200,
                "is_error" => false,
                "message"  => "$total_shocks pair socks unsold mark successfully"
            ];
            return response()->json($data);
            
            
        }
    }




}
