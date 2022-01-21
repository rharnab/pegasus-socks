<?php

namespace App\Http\Controllers\Agent\Rack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RackBillVoucherController extends Controller
{
    public function voucherList(){
        $agent_id = Auth::user()->agent_id;
        $vouchers = DB::table('shock_bills as sb')
                ->select([
                    's.name as shop_name',
                    'sb.rack_code', 
                    'sb.voucher_link', 
                    'sb.sales_quantity',
                    'sb.collect_amount', 
                    'sb.shocks_bill_no', 
                    'sb.starting_date',
                    'sb.ending_date',
                    'sb.entry_datetime',
                    'c.shop_commission_percentage',
                    'c.shop_commission_amount',
                    'c.agent_commission_percentage',
                    'c.agent_commission_amount',
                ])
                ->leftJoin('shops as s', 'sb.shop_id', '=', 's.id')
                ->leftJoin('commissions as c', 'sb.shocks_bill_no', '=', 'c.shoks_bill_no')
                ->where('sb.agent_id', $agent_id)
                ->orderBy('sb.entry_datetime', 'desc')
                ->get();
        $data = [
            "vouchers" => $vouchers,
            "sl"       => 1
        ];
        return view('agent.rack.voucher.index', $data);
    }
}
