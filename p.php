<?php

namespace App\Http\Controllers\Api\bKash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RemitStatusCallbackController extends Controller
{
	public function remitStatusCallback(Request $request){
		$url        = $request->getUri();
        $payload    = json_encode($request->all());
		$mobile_no 		   = $this->findMobileNumberFromPayload($payload);
		$conversation_id = $request->input('conversionID');
		$transaction_reference = $this->findTransactionReferenceFromConversionId($conversation_id);
		$executionTime = microtime(true) - LARAVEL_START;
		
		$validator = Validator::make($request->all(), [
			'responseCode'        => ['required'],
			'responseDescription' => ['required'],
			'conversionID'        => ['required'],
			'txnId'               => ['required'],
			'approvalCode'        => ['required'],
			'processedTimestamp'  => ['required'],
		]);

		if ($validator->fails()) {
			$validation_error = [
				"status"  => 422,
				"success" => false,
				"message" => $validator->messages()->first()
			];
			// API Log Generate Section Start 
			$response = json_encode($validation_error);
			$this-> customAPILogStore($url, $payload, $response, $mobile_no, $conversation_id, $executionTime, 'REMIT_STATUS_CALLBACK', $transaction_reference, 0, $validator->messages()->first());	
			// API Log Generate Section End
			
			// bKash Transaction history section start
			$this->bkashAPITransactionHistoryLog($transaction_reference, "REMIT_STATUS_CALLBACK", $validator->messages()->first());
			// bKash Transaction history section end
			
			
			return response()->json($validation_error);
		}

		if($request->input('responseCode') == '3000'){
			$this->updateApiStatus($request->input('conversionID'), 6, 3);
			$this->paidTransaction($request->input('conversionID'));
			$this->insertRemitBookTransaction($request->input('conversionID'));
		}
		
		// bKash Transaction history section start
		$this->bkashAPITransactionHistoryLog($transaction_reference, "REMIT_STATUS_CALLBACK", $request->input('responseDescription'));
		// bKash Transaction history section end

		$data = [
			"status"       => 200,
			"success"      => true,
			"message"      => "Your Remit-Status Callback has been received",
			"conversionID" => $request->input('conversionID')
		];
		// API Log Generate Section Start 
		$response = json_encode($data);
		$this-> customAPILogStore($url, $payload, $response, $mobile_no, $conversation_id, $executionTime, 'REMIT_STATUS_CALLBACK', $transaction_reference, $request->input('responseCode'), $request->input('responseDescription'));	
		// API Log Generate Section End		
		return response()->json($data);
	}	
	
		// find out mobile no from payload
	public function findMobileNumberFromPayload($payload){
		$payload_array = json_decode($payload, true);
		$conversation_id = $payload_array['conversionID'] ? $payload_array['conversionID'] : '';			
		$mobile_no = DB::select(DB::raw("SELECT mobile_no FROM bkash_api_log where conversation_id='$conversation_id' and mobile_no!='' LIMIT 1"));	
		return $mobile_no[0]->mobile_no ? $mobile_no[0]->mobile_no : '';
	}
	
	
		//find transaction reference no from payload
	public function findTransactionReferenceFromConversionId($conversion_id){
		$sql = "SELECT transaction_reference FROM bkash_api_log WHERE conversation_id='$conversion_id' and transaction_reference is not null order by id desc limit 1";
		$data = DB::select(DB::raw($sql));
		if(count($data) > 0){
			return $data[0]->transaction_reference;
		}else{
			return '';
		}
	}
	
	
	// data insert into the remit-book
	public function insertRemitBookTransaction($conversionID){
		// transaction information fetching from bkash table 
		$transaction_info = DB::table('bkash_transactions')->where('conversationID', $conversionID)->first();
		
		// transaction table data insert section start
		$transaction = DB::table('transaction')->insert([
			"insKey"                  => "bkash_".date('Ymdhis'),
			"uploadDate"              => $transaction_info->entry_date,
			"uploadBy"                => $transaction_info->entry_user_id,
			"orgFileName"             => $transaction_info->file_name,
			"uploadedFileName"        => $transaction_info->file_name,
			"stLevel"                 => '2',
			"trnTp"                   => 'B',
			"AGENT_CODE"              => $transaction_info->agent_code,
			"ORDER_NO"                => $transaction_info->transaction_reference,
			"TRN_DATE"                => date('Y-m-d'),
			"AMOUNT"                  => $transaction_info->amount,
			"RECEIVER_NAME"           => $transaction_info->beneficiary,
			"RECEIVER_COUNTRY"        => "Bangladesh",
			"RECEIVER_CONTACT"        => $transaction_info->beneficiary_msisdn,
			"RECEIVER_BANK"           => "Southeast Bank Limited",
			"RECEIVER_BANK_BRANCH"    => "HEAD OFFICE",
			"RECEIVER_ACCOUNT_NUMBER" => $transaction_info->beneficiary_msisdn,
			"SENDER_NAME"             => $transaction_info->sender_first_name." ".$transaction_info->sender_last_name,
			"SENDER_COUNTRY"          => $transaction_info->send_country,
			"SENDER_ADDRESS_LINE"     => $transaction_info->address,
			"SENDER_CONTACT"          => $transaction_info->sender_msisdn,
			"PAYMENT_MODE"            => "bKash",
			"TRANSACTION_PIN"         => $transaction_info->transaction_reference,
			"TRANSACTION_MAKE_USER"   => $transaction_info->entry_user_id,
			"DISBURSEMENT_CURRENCY"   => $transaction_info->destination_cur,
			"DISBURSEMENT_AMOUNT"     => $transaction_info->amount,
			"REMARKS"                 => $transaction_info->purpose_of_remittance
		]);
		
		// transaction accepted table data insert section start
		$transaction_data = DB::table('transaction')->select('trnKeyCode')->where('ORDER_NO', $transaction_info->transaction_reference)->first();
		$trnKeyCode = $transaction_data->trnKeyCode;
		
		$transaction_accepted = DB::table('transaction_accepted')->insert([
			"trnKeyCode"     => $trnKeyCode,
			"trnTp"          => "B",
			"accountNo"      => $transaction_info->beneficiary_msisdn,
			"accountName"    => $transaction_info->beneficiary,
			"bnkCode"        => "44",
			"brCode"         => "8887",
			"selDistCode"    => "19",
			"acceptBy"       => $transaction_info->authorize_user_id,
			"authBy"         => $transaction_info->authorize_user_id,
			"sts"            => "2",
			"stsDate"        => date('Y-m-d'),
			"disburseAmt"    => $transaction_info->amount,
			"disburseThru"   => "5",
			"disburseBranch" => "8887",
			"disburseBy"     => $transaction_info->transfer_request_user_id,
			"disburseDate"   => date('Y-m-d'),
			"disburseTime"   => date('H:i:s')
		]);
		
		// incentive transaction insert section start
		$incentive = DB::table('incentive')->insert([
			"order_no"       => $transaction_info->transaction_reference,
			"principal_amt"  => $transaction_info->amount,
			"incentive_amt"  => ($transaction_info->amount/100) * 2,
			"status"         => "7",
			"org_trn_type"   => "2",
			"org_trn_status" => "0",
			"doc_status"     => "0",
			"id_attached"    => "0",
			"entry_dt"       => date('Y-m-d')
		]);

		
	}
	
	
}
