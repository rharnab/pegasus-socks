<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
class AgentHomeController extends Controller
{
    public function agentHome(){
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
                            agent_id = '$agent_user_id' 
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
        return view('agent.home', $data);
    }

    

    public  function shop_update($rack_code){
        $rack_shocks_array = [];
        $rack_code         = Crypt::decrypt($rack_code);
        $rack_shocks_array = [];

       $sql ="SELECT ty.types_name, ty.id, count(rp.style_code) as socks_pair FROM rack_products rp 
                left join users sp on sp.shop_id = rp.shop_id
                left join types ty on ty.id = rp.type_id
                where rp.status='1' and rack_code='$rack_code'
                group by ty.types_name order by ty.sl_priority asc";


                

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
                            and (rp.status = 1)"));
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

        return view('agent.shop_update', $data);
    }






     public function calculateShocksBill(Request $request){
        if($request->has('shocks')){
            $shocks       = $request->input('shocks');
            $shop_id      = $request->input('shop_id');
            $total_shocks = count($shocks);
            $total_amount = 0;

            $commissions      = $this->getTotalShocksCommissions($total_shocks, $shop_id);
            file_put_contents('m.txt', json_encode($commissions));
            $agent_commission = $commissions['agent_commission_persentage'];
            $shop_commission  = $commissions['shop_commission_persentage'];

            for($i=0; $i<$total_shocks; $i++){
                $total_amount += $this->singleShockSellPrice($shocks[$i]);
            }
            $data = [
                "total_shocks" => $total_shocks,
                "total_amount" => $total_amount,
                "agent_amount" => ($total_amount / 100) * $agent_commission,
                "shop_amount"  => ($total_amount / 100) * $shop_commission
            ];
            $html_render = view('agent.update_rack_commission_calculate', $data);
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
        <div class="col-6 ">
            <div class="p-3 bg-success-200 rounded overflow-hidden position-relative text-white mb-g">
                <div class="">
                    <h3 class="display-4 d-block l-h-n m-0 fw-500">
                        <span id="selected_shocks" style="font-size: 18px;">0 TK</span>
                        <small class="m-0 l-h-n">Shop Comission</small>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 ">
            <div class="p-3 bg-info-200 rounded overflow-hidden position-relative text-white mb-g">
                <div class="">
                    <h3 class="display-4 d-block l-h-n m-0 fw-500">
                        <span id="selected_shocks" style="font-size: 18px;">0 TK</span>
                        <small class="m-0 l-h-n">Agent Comission</small>
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



    public function billCollect(Request $request){
        if($request->has('shocks')){

            $shocks                  = $request->input('shocks');
            $rack_code               = $request->input('rack_code');
            $shop_id                 = $request->input('shop_id');
            $total_shocks            = count($shocks);
            $total_amount            = 0;       

            $entry_user_id  = Auth::user()->id;
            $entry_datetime = date('Y-m-d H:i:s');
            
            if(date('d') > 5){
                $sold_date    = date("Y-m-d");
            }else{
                $sold_date    = date('Y-m-d', strtotime('last day of previous month'));
            }
            

          
            for($i=0; $i<$total_shocks; $i++){
                $shock_code = $shocks[$i];

                DB::table('rack_products')->where('shocks_code', $shock_code)->update([
                    "status"              => 0,
                    "sold_mark_date_time" => '',
                    "sold_date"           => '',
                    "sold_mark_user_id"   => '',
                    "is_shopkeeper_sold"  => 0,
                ]);

                $shocks_info = DB::table('rack_products')->where('shocks_code', $shock_code)->first();
                $this->socksLog($shocks_info->id, "UNSOLD_BY_AGENT_FROM_SHOPKEEPER_SOLD_MARK");
            }


            $data = [
                "status"   => 200,
                "is_error" => false,
                "message"  => "$total_shocks pair socks sold mark successfully"
            ];
            return response()->json($data);
            
            
        }
    }



}
