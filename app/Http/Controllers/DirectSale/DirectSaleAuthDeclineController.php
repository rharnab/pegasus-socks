<?php

namespace App\Http\Controllers\DirectSale;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use PDF;

class DirectSaleAuthDeclineController extends Controller
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
                    where status=0 
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
        return view('direct-sale.auth-decline.index', $data);
    }


    public function voucherDetails($voucher_no){
        $voucher_no = Crypt::decrypt($voucher_no);
        $sql = "SELECT
                b.name as brand_name,
                bz.name as brand_size,
                t.types_name as type_name,
                count(*) as total_socks,
                sum(ss.ind_selling_price) as total_amount 
                from
                single_sales ss 
                LEFT JOIN
                    stocks st 
                    on ss.style_code = st.style_code 
                LEFT JOIN
                    brands b 
                    on st.brand_id = b.id 
                LEFT JOIN
                    brand_sizes bz 
                    on st.brand_size_id = bz.id 
                LEFT JOIN
                    types t 
                    on st.type_id = t.id 
                where ss.voucher_no = $voucher_no
                GROUP by
                st.brand_id,
                st.brand_size_id,
                st.type_id";
        $socks        = DB::select(DB::raw($sql));
        $voucher_link = $this->getVoucherLink($voucher_no);
        $data         = [
            "socks"        => $socks,
            "voucher_no"   => $voucher_no,
            "sl"           => 1,
            "voucher_link" => $voucher_link
        ];
        return view('direct-sale.auth-decline.view', $data);
    }


    public function voucherAuthorize(Request $request){
        $voucher_no = $request->input('voucher_no');

        $voucher_link = $this->generateSingleSalesVocuher($voucher_no);
        DB::table('single_sales_bill')->insert([
            "agent_id" => $this->getAgentCode($voucher_no),
            "voucher_no" => "$voucher_no",
            "voucher_link" =>  "$voucher_link",
            "authorize_user_id" => Auth::user()->id,
            "authorize_datetime" => date('Y-m-d H:i:s')
        ]);

        try{
            DB::table('single_sales')->where('voucher_no', $voucher_no)->update([
                "status"         => 1,
                "sold_timestamp" => date('Y-m-d H:i:s')
            ]);
            $data = [
                "status"   => 200,
                "is_error" => false,
                "message"  => "Sales voucher authorization successfully"
            ];
            return response()->json($data);
        }catch(Exception $e){
            $data = [
                "status"   => 400,
                "is_error" => true,
                "message"  => "Single sales database updated failed"
            ];
            return response()->json($data);
        }       
    }

    public function voucherDeclined(Request $request){
        $voucher_no = $request->input('voucher_no');
        $socks      = DB::table('single_sales')->where('voucher_no', $voucher_no)->get();
        foreach($socks as $sock){
            $single_socks_id = $sock->id;
            $style_code      = $sock->style_code;
            try{
                DB::update("UPDATE stocks SET remaining_socks = (remaining_socks - 1),is_packet_sale=0 WHERE style_code='$style_code'");
                DB::table('single_sales')->where('id', $single_socks_id)->delete();
            }catch(Exception $e){
                $data = [
                    "status"   => 400,
                    "is_error" => true,
                    "message"  => $e->getMessage()
                ];
                return response()->json($data);
            }
        }
        $data = [
            "status"   => 200,
            "is_error" => false,
            "message"  => "Sales voucher decline successfully"
        ];
        return response()->json($data);
    }

    public function generateSingleSalesVocuher($voucher_no){
        $sql = "SELECT
                b.name as brand_name,
                bz.name as brand_size,
                t.types_name as type_name,
                count(*) as total_socks,
                sum(ss.ind_selling_price) as total_amount 
                from
                single_sales ss 
                LEFT JOIN
                    stocks st 
                    on ss.style_code = st.style_code 
                LEFT JOIN
                    brands b 
                    on st.brand_id = b.id 
                LEFT JOIN
                    brand_sizes bz 
                    on st.brand_size_id = bz.id 
                LEFT JOIN
                    types t 
                    on st.type_id = t.id 
                where ss.voucher_no = $voucher_no
                GROUP by
                st.brand_id,
                st.brand_size_id,
                st.type_id";
        $socks = DB::select(DB::raw($sql));
        $data = [
            "shcoks_datas"      => $socks,
            "voucher_no" => $voucher_no,
            "sl"         => 1
        ];

        $pdf      = PDF::loadView('direct-sale.auth-decline.voucher', $data);
        $path     = public_path('backend/assets/voucher/single-sale-bill');
        $fileName = "single_sales_".$voucher_no. '.pdf';
        $filepath = $path . '/' . $fileName;
        $pdf->save($filepath); 

        return $filepath;
    }


    public function getAgentCode($voucher_no){
        $sql = "SELECT agent_id FROM single_sales where voucher_no=$voucher_no GROUP by agent_id";
        $data = DB::select(DB::raw($sql));
        return $data[0]->agent_id;
    }

    public function getVoucherLink($voucher_no){
        $sql = "SELECT voucher_link from single_sales_bill where voucher_no = $voucher_no";
        $data = DB::select(DB::raw($sql));
        if(count($data) > 0){
            return $data[0]->voucher_link;
        }else{
            return false;
        }
    }


    public function voucherDownload($voucher_no){
        $pdf = public_path("backend/assets/voucher/single-sale-bill/single_sales_1.pdf"); 
        return response()->download($pdf);
    }



}
