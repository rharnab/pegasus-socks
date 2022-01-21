<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
      
    
         $accounts= DB::table('gl_accounts as gl')
                    ->leftJoin('contra_mapping as  cm', 'gl.acc_no', '=', 'cm.acc_no')
                    ->select('gl.acc_name', 'gl.acc_no as gl_account_no')
                    ->where('cm.status', 1)
                    ->orderBy('gl.acc_no', 'asc')->get();
        return view('accounts.transaction.create', compact('accounts'));
    }


    public function store(Request $request)
    {
       
         $account_number = $request->account_name;
         $tran_type = $request->tran_type;
         $amount = $request->amount;
         $user = $request->user;
         $remarks = $request->remarks;
         $payment_type = $request->payment_type;
         $entry_date = date('Y-m-d h:i:s', strtotime($request->entry_date));

         if($payment_type ==2)
         {
            $chq_number = $request->chq_number;
         }else{
             $chq_number = '';
         }

           $last_row = DB::table('transaction')->select('id')->orderBy('id', 'desc')->first();
        
           if(empty($last_row))
           {
               $last_id = 1;
           }else{
            $last_id = $last_row->id + 1;
           }
           
        //    $batch_no = "VLL".$last_id;
        //    $dr_tracer_no = "VLL-Dr-".date('Ymdhis').$last_id;
        //    $cr_tracer_no = "VLL-Cr-".date('Ymdhis').$last_id;

         $sql = " SELECT gl.acc_name, gl.dr_cr_permission,
                cm.*
                FROM   `contra_mapping` cm
                        INNER JOIN gl_accounts  gl
                                ON cm.acc_no = gl.acc_no 
                where gl.acc_no='$account_number'";

        $result = DB::select($sql);
        if(count($result) > 0 )
        {
            $account_result = $result[0];

           

            if($tran_type == 1)
            {
               
                /*-----------------trntp deposit-----------------------*/
                $dr_result = response()->json($this->DebitPermission($account_result->acc_no));
                $cr_resule = response()->json($this->CreditPermission($account_result->contra_acc_no));
                $dr_result->getData()->is_error; 
                $cr_resule->getData()->is_error;
                 
                /* -----------------------balance check ------------------------*/
                // $balance_info_data =  $this->balanceCheck($account_result->acc_no, $amount);
                // if($balance_info_data['is_error'] =='Y')
                // {
                //     return response()->json($balance_info_data);
                // }
                /* -----------------------balance check ------------------------*/

                if(($dr_result->getData()->is_error == 'N') && ($cr_resule->getData()->is_error =='N'))
                {
   
                   try{

                        /*-------------------track code----------------*/
                        $batch_no = substr($account_result->acc_no, 0, 3).substr($account_result->contra_acc_no, 0, 3).$last_id; // first 3 debit last 3 for credit
                        $dr_tracer_no = substr($account_result->acc_no, 0, 3).date('Ymdhis').$last_id;
                        $cr_tracer_no = substr($account_result->contra_acc_no, 0, 3).date('Ymdhis').$last_id;
                          /*-----------------end track code----------------*/

                          $balance_update_info = $this->balanceUpdate($account_result->acc_no, $account_result->contra_acc_no, $amount);

                          if($balance_update_info['is_error'] == 'N')
                          {
                     
                            /*-----------------------------------------------------debit data insert----------------------*/
                            $trn_dr = DB::table('transaction')->insert([
                                'transaction_date' => $entry_date,
                                'batch_no' => $batch_no,
                                'tracer_no' =>$dr_tracer_no,
                                'acc_no' => $account_result->acc_no,
                                'user' => $user,
                                'amount' => $amount,
                                'status' => 0,
                                'remarks' =>$remarks,
                                'created_at' => date('Y-m-d h:i:s'),
                                'created_by' => Auth::user()->id,
                                'trnTp' =>'Dr',
                                'bal_update'=> str_replace('-', '', $balance_update_info['debit_update_balance']),
                                'instrument_no'=> $chq_number,
                                
    
                            ]);
                            /*-----------------------------------------------------debit data insert----------------------*/
                            /*-----------------------------------------------------credit data insert----------------------*/
    
                                $trn_cr = DB::table('transaction')->insert([
                                'transaction_date' => $entry_date,
                                'batch_no' => $batch_no,
                                'tracer_no' =>$cr_tracer_no,
                                'acc_no' => $account_result->contra_acc_no,
                                'user' => $user,
                                'amount' => $amount,
                                'status' => 0,
                                'remarks' =>$remarks,
                                'created_at' => date('Y-m-d h:i:s'),
                                'created_by' => Auth::user()->id,
                                'trnTp' =>'Cr',
                                'bal_update'=> str_replace('-', '', $balance_update_info['credit_update_balance']),
                                'instrument_no'=> $chq_number,
    
                            ]);
    
                            /*-----------------------------------------------------credit data insert----------------------*/

                            /*---------------------------------balane update---------------------------------------------*/
                            
                            // if($trn_dr == true && $trn_cr ==true)
                            // {
                                /*---------------------debit gl_balance_update-------------*/
                                DB::table('gl_accounts')->where('acc_no', $account_result->acc_no)->update([
                                    'balance' => str_replace(',', '', $balance_update_info['debit_update_balance']),

                                ]);


                                 /*---------------------credit gl_balance_update-------------*/
                                 DB::table('gl_accounts')->where('acc_no', $account_result->contra_acc_no)->update([
                                    'balance' => str_replace(',', '', $balance_update_info['credit_update_balance']),
                                ]);


                                $data = [
                                    "status"=>200,
                                    "is_error" =>'N',
                                    "message" =>'Transaction Create successfull'
                    
                                ];
                                return response()->json($data);
                            //}
                                
                            /*---------------------------------balane update---------------------------------------------*/
                           
                        }

                       }catch(Exception $e)
                       {
                       
                           $data = [
                                   "status"=>400,
                                   "is_error" =>'Y',
                                   "message" =>'Sorry Transaction not create'
                   
                               ];
                               return response()->json($data);
                       }
   
   
                   }else{
   
                       $data = [
                           "status"=>400,
                           "is_error" =>'Y',
                           "message" =>'Sorry Dr/Cr. Permission not allowed'
           
                       ];
                       return response()->json($data);
                    }

            }else if($tran_type == 0){
                /*---------------------------trntp withdraw-----------------*/
                $dr_result = response()->json($this->DebitPermission($account_result->contra_acc_no));
                $cr_resule = response()->json($this->CreditPermission($account_result->acc_no));
                 $dr_result->getData()->is_error; 
                 $cr_resule->getData()->is_error; 


                  /* -----------------------balance check ------------------------*/
                  $balance_info_data =  $this->balanceCheck($account_result->contra_acc_no, $amount);
                  if($balance_info_data['is_error'] =='Y')
                  {
                      return response()->json($balance_info_data);
                  }
                  /* -----------------------balance check ------------------------*/

                 
                if(($dr_result->getData()->is_error == 'N') && ($cr_resule->getData()->is_error =='N'))
                {
   
                   try{


                         /*-------------------track code----------------*/

                         $batch_no = substr($account_result->contra_acc_no, 0, 3).substr($account_result->acc_no, 0, 3).$last_id; // first 3 debit last 3 for credit
                         $dr_tracer_no = substr($account_result->contra_acc_no, 0, 3).date('Ymdhis').$last_id;
                         $cr_tracer_no = substr($account_result->acc_no, 0, 3).date('Ymdhis').$last_id;
 
                           /*-----------------end track code----------------*/
                           $balance_update_info = $this->balanceUpdate($account_result->contra_acc_no, $account_result->acc_no, $amount);
                           if($balance_update_info['is_error'] == 'N')
                           {
                                /*-----------------------------------------------------debit data insert----------------------*/
                                DB::table('transaction')->insert([
                                    'transaction_date' => $entry_date,
                                    'batch_no' => $batch_no,
                                    'tracer_no' =>$dr_tracer_no,
                                    'acc_no' => $account_result->contra_acc_no,
                                    'user' => $user,
                                    'amount' => $amount,
                                    'status' => 0,
                                    'remarks' =>$remarks,
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'created_by' => Auth::user()->id,
                                    'trnTp' =>'Dr',
                                    'bal_update'=> str_replace('-', '',$balance_update_info['debit_update_balance']),
                                    'instrument_no'=> $chq_number,
        
                                ]);
                                /*-----------------------------------------------------debit data insert----------------------*/
                                /*-----------------------------------------------------credit data insert----------------------*/
        
                                DB::table('transaction')->insert([
                                    'transaction_date' => $entry_date,
                                    'batch_no' => $batch_no,
                                    'tracer_no' =>$cr_tracer_no,
                                    'acc_no' => $account_result->acc_no,
                                    'user' => $user,
                                    'amount' => $amount,
                                    'status' => 0,
                                    'remarks' =>$remarks,
                                    'created_at' => date('Y-m-d h:i:s'),
                                    'created_by' => Auth::user()->id,
                                    'trnTp' =>'Cr',
                                    'bal_update'=> str_replace('-', '', $balance_update_info['credit_update_balance']),
                                    'instrument_no'=> $chq_number,
        
                                ]);
        
                                /*-----------------------------------------------------credit data insert----------------------*/
                                /*---------------------------------balane update---------------------------------------------*/
                                // if($trn_dr == true && $trn_cr ==true)
                                // {
                                    /*---------------------debit gl_balance_update-------------*/
                                    DB::table('gl_accounts')->where('acc_no', $account_result->contra_acc_no)->update([
                                        'balance' => str_replace(',', '', $balance_update_info['debit_update_balance']),
    
                                    ]);
    
    
                                     /*---------------------credit gl_balance_update-------------*/
                                     DB::table('gl_accounts')->where('acc_no', $account_result->acc_no)->update([
                                        'balance' => str_replace(',', '', $balance_update_info['credit_update_balance']),
                                    ]);


                                    $data = [
                                        "status"=>200,
                                        "is_error" =>'N',
                                        "message" =>'Transaction Create successfull'
                        
                                    ];
                                    return response()->json($data);
                                //}
                                /*---------------------------------balane update---------------------------------------------*/
        
                               
                           }

                       }catch(Exception $e)
                       {
                       
                           $data = [
                                   "status"=>400,
                                   "is_error" =>'Y',
                                   "message" =>'Sorry Transaction not create'
                   
                               ];
                               return response()->json($data);
                       }
   
   
                   }else{
   
                       $data = [
                           "status"=>400,
                           "is_error" =>'Y',
                           "message" =>'Sorry Dr/Cr. Permission not allowed'
           
                       ];
                       return response()->json($data);
                    }


            }else{


            }
                

        }else{
            $data = [
                "status"=>400,
                "is_error" =>'Y',
                "message" =>'Data not found'

            ];
            
            return response()->json($data);
        }

        
    }


    /*----------------debit permission --------------------*/
    public function DebitPermission($account_no)
    {
        $result  = DB::select("select dr_cr_permission from gl_accounts where acc_no = '$account_no' and (dr_cr_permission =0 or dr_cr_permission=2)");
        
        if(!empty($result))
        {
            $data = [
                'status' =>200,
                'is_error' =>'N',
                'message' =>'Dr. allow'
            ];
           
            return $data;
            
        }else{

            $data = [
                'status' =>400,
                'is_error' =>'Y',
                'message' =>'Sorry Permission not allowed'
            ];

            return $data;

        }
    }

     /*------------------ credit permission --------------------*/
    public function CreditPermission($account_no)
    {
        $result  = DB::select("select dr_cr_permission from gl_accounts where acc_no = '$account_no' and (dr_cr_permission =1 or dr_cr_permission=2)");
        
        if(!empty($result))
        {
            $data = [
                'status' =>200,
                'is_error' =>'N',
                'message' =>'CR. allow'
            ];
           
            return $data;
            
        }else{

            $data = [
                'status' =>400,
                'is_error' =>'Y',
                'message' =>'Sorry Permission not allowed'
            ];

            return $data;

        }
    }



    public function balanceCheck($account_no, $amount)
    {
       $balance_info  =  DB::table('gl_accounts')->select('balance', 'acc_no')->where('acc_no', $account_no)->first();

       if(!empty($balance_info))
       {
            if(($balance_info->balance > 0) && ($balance_info->balance >= $amount) )
            {
                $data = [
                    'status' => 200,
                    'is_error' =>'N',
                    'balance' =>$balance_info->balance,
                ];

                return $data;

            }else if($balance_info->balance >= 0) {
                $data = [
                    'status' => 400,
                    'is_error' =>'Y',
                    'message' => 'Sorry ! Insufficient balance ',
                ];
                return $data;
            }else{

                $data = [
                    'status' => 400,
                    'is_error' =>'Y',
                    'message' => 'Sorry blance not match',
                ];
                return $data;

            }

       }else{
            $data = [
                'status' => 400,
                'is_error' =>'Y',
                'message' => 'data not found',
            ];
            return $data;
       }
   
    }


    public function balanceUpdate($debit_account, $credit_account, $amount)
    {
        
        /*------------------------------- debit gl account ----------------------*/
        $debit_gl_balance  = DB::table('gl_accounts')->select('balance', 'acc_no')->where('acc_no', $debit_account)->first();
        
        if(!empty($debit_gl_balance))
        {
            $debit_update_balance  = $debit_gl_balance->balance - $amount; 
            
        }else{

            $data = [
                'status' => 400,
                'is_error' =>'Y',
                'message' => 'data not found',
            ];
            return $data;

        }
    

        /*------------------------------- debit gl account ----------------------*/

        /*------------------------------- Credit gl account ----------------------*/
        $credit_gl_balance  = DB::table('gl_accounts')->select('balance', 'acc_no')->where('acc_no', $credit_account)->first();

        if(!empty($credit_gl_balance))
        {
            $credit_update_balance  = $credit_gl_balance->balance + $amount;
        
        }else{

            $data = [
                'status' => 400,
                'is_error' =>'Y',
                'message' => 'data not found',
            ];
            return $data;

        }

       

        /*------------------------------- Credit gl account ----------------------*/

       
        if(isset($debit_update_balance) && isset($credit_update_balance) )
        {
            $data = [
                'status' => 200,
                'is_error' =>'N',
                'debit_update_balance' => $debit_update_balance,
                'credit_update_balance' => $credit_update_balance,
            ];

           return $data;

       
               
        }
              
       
       


    }



    public function transaction_auth()
    {
        $sql = "SELECT ga.acc_name                 AS dr_ac,
        tr.trntp                    AS dr_tp,
        ga.acc_no                   AS dr_acc,
        tr.batch_no,
        tr.amount                   AS dr_amount,
        tr.transaction_date         AS dr_trn_date,
        tr.status                   AS dr_status,
        (SELECT Concat(t.acc_no, '-', g.acc_name, '-')
         FROM   transaction t
                LEFT JOIN gl_accounts g
                       ON t.acc_no = g.acc_no
         WHERE  t.batch_no = tr.batch_no
                AND t.trntp = 'Cr') AS cr_transaction_info
 FROM   `transaction` tr
        LEFT JOIN gl_accounts ga
               ON tr.acc_no = ga.acc_no
 WHERE   tr.status = 0
        AND tr.trntp = 'Dr'  ";

         $data = DB::select($sql);

         
       
        return view('accounts.transaction.index', compact('data'));
    }

    public function auth_process(Request $request)
    {
        $batch_no = $request->batch_no;
        $authorize_by = Auth::user()->id; 
        $authorized_at = date('Y-m-d h:i:s');
        
        if(!empty($batch_no))
        {
            $batch_array = array();
            
    
            foreach($batch_no as $single_batch_no)
            {
                if($single_batch_no !='on')
                {
                    array_push($batch_array,   "'".$single_batch_no."'");
                }
            }
    
            $all_batch_no = implode(',', $batch_array);

            try{

                $sql = "UPDATE transaction set status=1, authorized_by='$authorize_by', authorized_at='$authorized_at' where batch_no in ($all_batch_no)";
                  DB::update($sql);
                
                $data = [
                    "status"=>200,
                    "is_error" =>'N',
                    "message" =>'Transaction Successfully Authorized'
    
                ];

                return response()->json($data);
    

            }catch(Exception $e)
            {
                $data = [
                    'status'   =>400,
                    'is_error' =>'Y',
                    'message'  =>'Sorry transaction not update'
                ];
                return response()->json($data);

            }
         

        }else{
            $data = [
                'status'   =>400,
                'is_error' =>'Y',
                'message'  =>'Sorry transaction not found'
            ];

            return response()->json($data);
        }
        
    }

}
