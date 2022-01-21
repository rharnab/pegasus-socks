<?php

namespace App\Http\Controllers\Accounts\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransferReport extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  
        return view('accounts.report.transfer.index');
    }

    public function details(Request $request)
    {

         $frm_date = date('Y-m-d', strtotime($request->frm_date)); 
         $to_date= date('Y-m-d', strtotime($request->to_date));
        $sql = "SELECT ga.acc_name                 AS dr_ac,
        tr.trntp                    AS dr_tp,
        ga.acc_no                   AS dr_acc,
        tr.batch_no,
        tr.amount                   AS dr_amount,
        tr.transaction_date         AS dr_trn_date,
        tr.status                   AS dr_status,
        tr.remarks,
        us.name as auth_by,
        tr.authorized_at,
        tr.instrument_no,
        (SELECT Concat(t.acc_no, '-', g.acc_name, '-')
         FROM   transaction t
                LEFT JOIN gl_accounts g
                       ON t.acc_no = g.acc_no
         WHERE  t.batch_no = tr.batch_no
                AND t.trntp =2) AS cr_transaction_info
        FROM   `transaction` tr
                LEFT JOIN gl_accounts ga
                    ON tr.acc_no = ga.acc_no
                LEFT JOIN users us
                    ON tr.authorized_by = us.id
        WHERE   tr.status = 1
                AND tr.trntp =1 AND date(tr.authorized_at) BETWEEN   '$frm_date' and '$to_date' 
        order by tr.id desc  ";

         $transaction_result = DB::select($sql);



         $summation_result = DB::select("SELECT sum(dr_amount) as total_amount from (SELECT ga.acc_name                 AS dr_ac,
         tr.trntp                    AS dr_tp,
         ga.acc_no                   AS dr_acc,
         tr.batch_no,
         tr.amount                   AS dr_amount,
         tr.transaction_date         AS dr_trn_date,
         tr.status                   AS dr_status,
         tr.remarks,
         us.name as auth_by,
         tr.authorized_at,
         (SELECT Concat(t.acc_no, '-', g.acc_name, '-')
          FROM   transaction t
                 LEFT JOIN gl_accounts g
                        ON t.acc_no = g.acc_no
          WHERE  t.batch_no = tr.batch_no
                 AND t.trntp =2) AS cr_transaction_info
         FROM   `transaction` tr
                 LEFT JOIN gl_accounts ga
                     ON tr.acc_no = ga.acc_no
                     LEFT JOIN users us
                     ON tr.authorized_by = us.id
         WHERE   tr.status = 1
                 AND tr.trntp =1
                 AND date(tr.authorized_at) BETWEEN   '2022-01-02' and '2022-01-02'
         order by tr.id desc) as trn_info");

         $data  = [
            'transaction_result' => $transaction_result,
            'frm_date' => $frm_date,
            'to_date'  => $to_date,
            'summation_result' => $summation_result
 
         ];

         return view('accounts.report.transfer.details', $data );
      
        
    }

    public function balance()
    {
       
       

       return view('accounts.report.glbalance.details');
        
    }


    public function balance_2()
    {
       
       

       return view('accounts.report.glbalance.details_2');
        
    }


    public function generatePdt()
    {
        return view('accounts.report.glbalance.details_pdf');
    }



}
