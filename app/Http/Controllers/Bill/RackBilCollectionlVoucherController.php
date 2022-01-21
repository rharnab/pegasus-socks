<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RackBilCollectionlVoucherController extends Controller
{
    public function voucherList(){
        $vouchers = DB::table('shock_bills as sb')
                ->select([
                    's.name as shop_name',
                    'sb.rack_code', 
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
                    'au.name as agent_name',
                    'c.venture_amount',
                    'sb.voucher_link'
                ])
                ->leftJoin('shops as s', 'sb.shop_id', '=', 's.id')
                ->leftJoin('commissions as c', 'sb.shocks_bill_no', '=', 'c.shoks_bill_no')
                ->leftJoin('agent_users as au', 'au.id', '=', 'sb.agent_id')
                ->get();
        $data = [
            "vouchers" => $vouchers,
            "sl"       => 1
        ];
        return view('bill.rack.voucher.index', $data);
    }
}
