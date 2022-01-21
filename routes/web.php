<?php

use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\Agent\AgentHomeController;
use App\Http\Controllers\Agent\AgentRackController;
use App\Http\Controllers\Agent\AgentRackShocksBillCollectController;
use App\Http\Controllers\Agent\Rack\ProductStatusChangeController;
use App\Http\Controllers\Agent\Rack\RackBillCollectionController;
use App\Http\Controllers\Agent\Rack\RackBillVoucherController;
use App\Http\Controllers\Agent\SoldDelete\RackWrongSoldItemDeteController;
use App\Http\Controllers\Bill\RackBilCollectionlVoucherController;
use App\Http\Controllers\DirectSale\DirectProductSalesController;
use App\Http\Controllers\DirectSale\DirectSaleAuthDeclineController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\ParameterSetup\Agent\AgentController;
use App\Http\Controllers\Rack\RackFillupController;
use App\Http\Controllers\ParameterSetup\Brand\BrandController;
use App\Http\Controllers\ParameterSetup\BrandSize\BrandSizeController;
use App\Http\Controllers\ParameterSetup\Racks\RacksController;
use App\Http\Controllers\ParameterSetup\Shops\ShopsController;
use App\Http\Controllers\ParameterSetup\Types\TypesController;
use App\Http\Controllers\ParameterSetup\Product\ProductController;
use App\Http\Controllers\ParameterSetup\ProductCategories\ProductCategoriesController;
use App\Http\Controllers\ParameterSetup\User\UserController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\Report\LotSummaryController;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\stock\StockController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Report\ProductReport;
use App\Http\Controllers\Rack\RackMappingController;
use App\Http\Controllers\Rack\RackProductDeleteController;
use App\Http\Controllers\Shopkeeper\DashboardController;
use App\Http\Controllers\Shopkeeper\ShopkeeperSingleRackController;
use App\Http\Controllers\Report\PacketCodeController;

use App\Http\Controllers\Finance\Rack\FinaceRackBillCollectionController;
use App\Http\Controllers\Shopkeeper\CustomMessageController;
use App\Http\Controllers\Shopkeeper\SearchRackSocksController;
use App\Http\Controllers\Voucher\ShopRackBillCollectionVoucherController;
use App\Http\Controllers\Report\StockSumarryController;
use App\Http\Controllers\ParameterSetup\commission\CommissionSetUpController;
use App\Http\Controllers\Report\CommissionReport;

use App\Http\Controllers\AccountManager\AccountManagerController;

use App\Http\Controllers\Report\RackFillUpReport;
use App\Http\Controllers\Report\RackProductDetailsReport;
use App\Http\Controllers\Accounts\TransactionController;
use App\Http\Controllers\Accounts\Report\TransferReport;
use App\Http\Controllers\Report\ShopVoucherReport;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/





################################# website ################################

Route::get('/', function () {
   //return view('landing.index');
   return redirect()->route('home');
});


Route::get('/contact', function () {
   return view('landing.contact');
});

Route::get('/condition', function () {
   return view('landing.condition');
});


Route::get('/gallary', function () {
   return view('landing.gallary');
});

################################ end website #############################



Route::get('/generateRackShocksVoucher',[AgentRackShocksBillCollectController::class,'generateRackShocksVoucher']);



Route::get('/login', function () {
   return redirect()->route('home');
});

######################################################## password change ######################################
Route::get('/password-change', [App\Http\Controllers\HomeController::class, 'password_change'])->name('password.change');
Route::post('/password-save', [App\Http\Controllers\HomeController::class, 'password_change_save'])->name('password.change.save');
######################################################## password change ######################################

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/contact-us', function(){

    return view('contact');
});


Route::get('lang/{locale}', [LocalizationController::class, 'index'])->name('language');


Route::get('admin/home',[AdminHomeController::class, 'index'])->name('admin.home');



#################################################
# Agent  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/agent', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Agent', 'as' => 'parameter_setup.agent.'], function(){

    Route:: get('index', [AgentController::class, 'index'])->name('index');
    Route:: get('edit-agent/{id}', [AgentController::class, 'edit_agent_url'])->name('edit_agent_url');
   
    Route:: get('create', [AgentController::class, 'create'])->name('create');
    Route:: post('store', [AgentController::class, 'store'])->name('store');
    Route:: get('edit/{id}', [AgentController::class, 'edit'])->name('edit');
    Route:: post('update', [AgentController::class, 'update'])->name('update');
   
});

#################################################
#  Agent End 
#################################################




#################################################
# Brand  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/brand', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Brand', 'as' => 'parameter_setup.brand.'], function(){

    Route::get('index', [BrandController::class, 'index'])->name('index');
    Route::get('create', [BrandController::class, 'create'])->name('create');
    Route::post('store', [BrandController::class, 'store'])->name('store');
    Route::get('edit-brand/{id}', [BrandController::class, 'edit'])->name('edit');
    Route::post('update', [BrandController::class, 'update'])->name('update');
   
});

#################################################
#  Brand End 
#################################################



#################################################
# Agent Home Section Start
#################################################
Route::group(['prefix' => 'agent', 'namespace' => 'Agent', 'middleware' => 'auth', 'as' => 'agent.rack.'], function(){
    Route::get('details/{rackcode}', [AgentRackController::class, 'rackDetails'])->name('details');
    Route::post('calculate-shocks-bill', [AgentRackController::class, 'calculateShocksBill'])->name('calculate_shocks_bill');
    Route::post('rack/socks/bill-collection', [AgentRackShocksBillCollectController::class, 'billCollect'])->name('socks.bill_collect');
});
#################################################
# Agent Home Section End
#################################################


#################################################
# Agent Rack Section Start
#################################################
Route::group(['prefix' => 'agent', 'namespace' => 'Agent', 'middleware' => 'auth', 'as' => 'agent.'], function(){
    Route::get('home', [AgentHomeController::class, 'agentHome'])->name('home');
    Route::get('/shopkeeper/update/{rack_code}', [AgentHomeController::class, 'shop_update'])->name('shopkeeper.update');
    Route::post('shop-update-shocks-bill', [AgentHomeController::class, 'calculateShocksBill'])->name('calculate_shocks_bill');
    Route::post('/update/sales/socks', [AgentHomeController::class, 'billCollect'])->name('update.sales.socks');
});
#################################################
# Agent Rack Section Start
#################################################
# Brand Size  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/brandsize', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\BrandSize', 'as' => 'parameter_setup.brandsize.'], function(){

    Route::get('index', [BrandSizeController::class, 'index'])->name('index');
    Route::get('create', [BrandSizeController::class, 'create'])->name('create');
    Route::post('store', [BrandSizeController::class, 'store'])->name('store');
    Route::get('edit/{id}', [BrandSizeController::class, 'edit'])->name('edit');
    Route::post('update', [BrandSizeController::class, 'update'])->name('update');
   
});

#################################################
#  Brand End 
#################################################




#################################################
# Racks  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/racks', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Racks', 'as' => 'parameter_setup.racks.'], function(){

    Route::get('index', [RacksController::class, 'index'])->name('index');
    Route::get('create', [RacksController::class, 'create'])->name('create');
    Route::post('store', [RacksController::class, 'store'])->name('store');
    Route::get('edit/{id}', [RacksController::class, 'edit'])->name('edit');
    Route::post('update', [RacksController::class, 'update'])->name('update');
   
});

#################################################
#  Racks End 
#################################################




#################################################
# shops  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/shops', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Shops', 'as' => 'parameter_setup.shops.'], function(){

    Route::get('index', [ShopsController::class, 'index'])->name('index');
    Route::get('create', [ShopsController::class, 'create'])->name('create');
    Route::post('store', [ShopsController::class, 'store'])->name('store');
    Route::get('edit-shop/{id}', [ShopsController::class, 'edit'])->name('edit');
    Route::post('update', [ShopsController::class, 'update'])->name('update');
   
});

#################################################
#  shops End 
#################################################




#################################################
# product categories  Start 
#################################################
Route::group(['prefix' => 'parameter-setup/product_categories', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\ProductCategories', 'as' => 'parameter_setup.product_categories.'], function(){

    Route::get('index', [ProductCategoriesController::class, 'index'])->name('index');
    Route::get('create', [ProductCategoriesController::class, 'create'])->name('create');
    Route::post('store', [ProductCategoriesController::class, 'store'])->name('store');
    Route::get('edit/{id}', [ProductCategoriesController::class, 'edit'])->name('edit');
    Route::post('update', [ProductCategoriesController::class, 'update'])->name('update');
   
});

#################################################
#  product categories End 
#################################################




#################################################
# Types  Start 
#################################################

Route::group(['prefix' => 'parameter-setup/types/', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Types', 'as' => 'parameter_setup.types.'], function(){

    Route::get('index', [TypesController::class, 'index'])->name('index');
    Route::get('create', [TypesController::class, 'create'])->name('create');
    Route::post('store', [TypesController::class, 'store'])->name('store');

   
});

#################################################
#  Types  End 
#################################################



#################################################
# Products  Start 
#################################################

Route::group(['prefix' => 'parameter-setup/product/', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\Product', 'as' => 'parameter_setup.products.'], function(){

    Route::get('index', [ProductController::class, 'index'])->name('index');
    Route::get('create', [ProductController::class, 'create'])->name('create');
    Route::post('store', [ProductController::class, 'store'])->name('store');

   
});

#################################################
#  Products  End 
#################################################



#################################################
# User Entry  Start 
#################################################

Route::group(['prefix' => 'parameter-setup/user/', 'middleware' => 'auth', 'namespace' => 'ParameterSetup\User', 'as' => 'parameter_setup.user.'], function(){

    Route::get('index', [UserController::class, 'index'])->name('index');
    Route::get('create', [UserController::class, 'create'])->name('create');
    Route::post('store', [UserController::class, 'store'])->name('store');

    Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
    Route::post('update', [UserController::class, 'update'])->name('update');

   
});

#################################################
#  User Entry  End 
#################################################



##################################################### stock store ###########################################
Route::group(['prefix'=>'stock', 'as'=>'stock.'], function(){
	Route::get('stock-index', [StockController::class, 'index'])->name('index');
	Route::get('stock-creat', [StockController::class, 'creat'])->name('creat');
	Route::post('stock-creat', [StockController::class, 'store'])->name('store');


    //vailation route

    Route::post('product-check', [StockController::class, 'ProductCheck'])->name('product-check');


    Route::get('lot-voucher', [StockController::class, 'lot_voucher_index'])->name('lot_voucher');
    Route::get('lot-voucher-create', [StockController::class, 'lot_voucher_create'])->name('lot_voucher.create');
    Route::post('lot-voucher-store', [StockController::class, 'lot_voucher_store'])->name('lot_voucher.lot_voucher_store');


});
##################################################### stock store ###########################################


#################################################
# Rack Bill Voucher Section Start
#################################################
Route::group(['prefix' => 'agent/rack/bill-voucher', 'middleware' => 'auth', 'namespace' => 'Agent\Rack', 'as' => 'agent.rack.bill_voucher.'], function(){
    Route::get('voucher-list', [RackBillVoucherController::class, 'voucherList'])->name('voucher_list');   
});

#################################################
#  Rack Bill Voucher Section End
#################################################


#################################################
# Rack Bill Voucher Section Start
#################################################
Route::group(['prefix' => 'rack/bill-voucher', 'middleware' => 'auth', 'namespace' => 'Rack', 'as' => 'bill.rack.bill_voucher.'], function(){
    Route::get('voucher-list', [RackBilCollectionlVoucherController::class, 'voucherList'])->name('voucher_list');   
});

#################################################
#  Rack Bill Voucher Section End
#################################################



#################################################
# Lot Summary Section Start
#################################################
Route::group(['prefix' => 'report/lot', 'middleware' => 'auth', 'namespace' => 'Report', 'as' => 'report.lot.'], function(){
    Route::get('No', [LotSummaryController::class, 'index'])->name('summary');   
    Route::get('details', [LotSummaryController::class, 'details'])->name('details');   
});

#################################################
#  Lot Summary Section End
#################################################

Route::get('agent/details/voucher-download/{bill_no}', function($bill_no){
    $pdf = public_path("backend/assets/voucher/rack-bill/$bill_no.pdf"); 
    return response()->download($pdf); 
});




#################################################
# Report section Section Start
#################################################
Route::group(['prefix' => 'report/', 'middleware' => 'auth', 'namespace' => '', 'as' => 'report.'], function(){
    
    //socks code generate 
    Route::get('socks-code-generate', [ReportController::class, 'socks_code_generate'])->name('socks_code_generate');   
    Route::post('socks-code-generate-pdf', [ReportController::class, 'generate_pdf'])->name('socks_code.generate_pdf');   
    //end socks code generate

    Route::post('find_socks_code', [ReportController::class, 'find_socks_code'])->name('socks_code.find_socks_code');


    // lot brands 

     Route::get('lot-brands', [ReportController::class, 'lot_brands'])->name('lot_brands');   
     Route::post('lot-brands-report-table', [ReportController::class, 'lot_brands_report_table'])->name('lot_brands_report_table');   
    

    //end lot brands 

    
     //start rack product
     
     //start rack product

        Route::get('rack-product', [ReportController::class, 'rack_product'])->name('rack_product');   
        Route::post('rack-product-table', [ReportController::class, 'rack_product_table'])->name('rack_product_table'); 

     //end rack product 


     //start rack product

        Route::get('rack-refil-voucher', [ReportController::class, 'rack_refil_voucher'])->name('rack_refil_voucher');   
        Route::post('rack-refil-voucher-table', [ReportController::class, 'rack_refil_voucher_table'])->name('rack_refil_voucher_table'); 

     
     

});

################################## product report #########################################
Route::group(['prefix' => 'report/product', 'middleware' => 'auth', 'namespace' => '', 'as' => 'report.'], function(){

    Route::get('product', [ProductReport::class, 'product'])->name('product');

});

################################## product report #########################################

################################################# packet code generetor ##########################################
Route::group(['prefix' => 'report/', 'middleware' => 'auth', 'namespace' => '', 'as' => 'report.'], function(){

    //socks code generate 
    Route::get('packet-code-generate', [PacketCodeController::class, 'socks_code_generate'])->name('packet_code_generate');   
    Route::post('generate-pdf', [PacketCodeController::class, 'generate_pdf'])->name('generate_pdf');   
    //end socks code generate
});


#################################################
# Report section Section End
#################################################


Route::get('/brand', function(){
    $handle = fopen("./rr.txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
           $line_array                 = explode(",", $line);
           $brand_id                   = trim($line_array[1]);
           $brand_size_id              = trim($line_array[3]);
           $type_id                    = trim($line_array[2]);
           $packet_socks_pair_quantity = $line_array[4];
           $packet_buying_price        = $line_array[5];
           $packet_selling_price       = $line_array[6];
           $individual_buying_price    = $line_array[7];
           $individual_selling_price   = $line_array[8];
           $sale_type                  = $line_array[9];
            //echo "SELECT id FROM brands where name='$brand_id'".'<br>';
            //echo "SELECT id FROM brand_sizes where name='$brand_size_id'".'<br>';
            //echo "SELECT id FROM types where types_name='$type_id'".'<br>';
           $brands = DB::select(DB::raw("SELECT id FROM brands where  name like '%$brand_id%' limit 1"))[0];
           $brand_sizes = DB::select(DB::raw("SELECT id FROM brand_sizes where  name like '%$brand_size_id%' limit 1"))[0];
           $types = DB::select(DB::raw("SELECT id FROM types where types_name like '$type_id%' limit 1"))[0];
                
           $insert = [
               "brand_id"                   => (empty($brands->id) ? '' : $brands->id),
               "brand_size_id"              => (empty($brand_sizes->id) ? '' : $brand_sizes->id),
               "type_id"                    => (empty($types->id) ? '' : $types->id),
               "packet_socks_pair_quantity" => $packet_socks_pair_quantity,
               "packet_buying_price"        => $packet_buying_price,
               "individual_buying_price"       => $packet_selling_price,
               "packet_selling_price"    => $individual_buying_price,
               "individual_selling_price"   => $individual_selling_price,
               "sale_type"                  => $sale_type,
               "entry_user_id"              => 1,
               "entry_date"                 => "2021-11-25",
               "entry_time"                 => "5:00:39",
           ];
           echo "<pre>";
           print_r($insert);
          // DB::table('products')->insert($insert);
        }
    
        fclose($handle);
    }
    
});

#################################################
# React Fillup Section Start
#################################################
Route::group(['prefix' => 'rack/rack-fillup', 'namespace' => 'Rack', 'middleware' => 'auth', 'as' => 'rack.rack-fillup.'], function(){
    Route:: get('index', [RackFillupController::class, 'index'])->name('index');
    Route:: get('create', [RackFillupController::class, 'create'])->name('create');
    Route:: post('rack-socks-history', [RackFillupController::class, 'rackSocksHistory'])->name('rack_socks_history');
    Route:: post('add-new-row', [RackFillupController::class, 'addNewRow'])->name('add_new_row');
    Route:: post('types-find-packet', [RackFillupController::class, 'socksTypeFindPacket'])->name('types_find_packet');
    Route:: post('style-remaining-product', [RackFillupController::class, 'styleCodeRemainingProduct'])->name('style_remaining_product');   
    Route:: post('store', [RackFillupController::class, 'store'])->name('store');
    Route:: get('show-details/{id}', [RackFillupController::class, 'showDetails'])->name('show.details');
    Route:: get('generate-pdf-socks-code/{id}', [RackFillupController::class, 'generate_pdf_socks_code'])->name('generate_pdf_socks_code');

    Route:: POST('rack-socks-details', [RackFillupController::class, 'rack_socks_details'])->name('rack_socks_details');


});
#################################################
# React Fillup Section Start
#################################################



#################################################
# Direct Product Sales Section Start
#################################################
Route::group(['namespace' => 'DirectSale', 'prefix' => 'direct-sale', 'middleware' => 'auth', 'as' => 'direct_sale.'], function(){
    Route::get('/index', [DirectProductSalesController::class, 'index'])->name('index');
    Route::get('/sale', [DirectProductSalesController::class, 'saleForm'])->name('sale_form');
    Route::post('/add-new-row', [DirectProductSalesController::class, 'addNewRow'])->name('add_new_row');
    Route::post('/store', [DirectProductSalesController::class, 'store'])->name('store');
});
#################################################
# Direct Product Sales Section End
#################################################


#################################################
# Direct Product Sales Authorization Decline Section Start
#################################################
Route::group(['namespace' => 'DirectSale', 'prefix' => 'direct-sale/auth-decline', 'middleware' => 'auth', 'as' => 'direct_sale.auth_decline.'], function(){
    Route::get('/index', [DirectSaleAuthDeclineController::class, 'index'])->name('index');
    Route::get('/voucher-details/{voucher_no}', [DirectSaleAuthDeclineController::class, 'voucherDetails'])->name('voucher_detatils');
    Route::post('voucher-authorize', [DirectSaleAuthDeclineController::class, 'voucherAuthorize'])->name('voucher_authorize');
    Route::post('voucher-decline', [DirectSaleAuthDeclineController::class, 'voucherDeclined'])->name('voucher_decline');
    Route::get('voucher-download/{voucher_no}',  [DirectSaleAuthDeclineController::class, 'voucherDownload'])->name('voucher_download');
});
#################################################
# Direct Product Sales Section End
#################################################



#################################################
# Rac Bill Collection Section Start
#################################################
Route::group(['namespace' => 'Agent\Rack', 'prefix' => 'agent/rack/bill-collection/', 'middleware' => 'auth', 'as' => 'agent.rack.bill_collection.'], function(){
    Route::get('/rack-list', [RackBillCollectionController::class, 'rackList'])->name('rack_list');
    Route::get('/reack-details/{rack_id}', [RackBillCollectionController::class, 'rackDetails'])->name('rack_details');
    Route::post('/calculate-commission', [RackBillCollectionController::class, 'calculateCommission'])->name('calculate_commission');
    Route::post('/socks-bill-collection', [RackBillCollectionController::class, 'socksBillCollection'])->name('socks_bill_collection');
});
#################################################
# Rac Bill Collection Section Start
#################################################



Route::get('rack-bill/voucher-stream/{bill_no}', function($bill_no){
    $pdf = public_path("backend/assets/voucher/rack-bill/$bill_no"); 

    return response()->file(
        public_path("backend/assets/voucher/rack-bill/$bill_no")
    );
});



#################################################
# Product Status Change Section Start
#################################################
Route::group(['namespace' => 'Agent\Rack', 'prefix' => 'agent/rack/product-status-change/', 'middleware' => 'auth', 'as' => 'agent.rack.product_status_change.'], function(){
    Route::get('index', [ProductStatusChangeController::class, 'index'])->name('index');
    Route::post('find-product', [ProductStatusChangeController::class, 'findProduct'])->name('find_product');
    Route::post('status-update', [ProductStatusChangeController::class, 'statusUpdate'])->name('status_update');
});
#################################################
# Product Status Change Section Start
#################################################



#################################################
# Rack Mapping Section Start
#################################################
Route::group(['namespace' => 'Rack', 'prefix' => 'rack/mapping', 'middleware' => 'auth', 'as' => 'rack.mapping.'], function(){
    Route::get('index', [RackMappingController::class, 'index'])->name('index');
    Route::get('create', [RackMappingController::class, 'create'])->name('create');
    Route::post('store', [RackMappingController::class, 'store'])->name('store');
});
#################################################
# Rack Mapping Section End
#################################################

################################################# packet code generetor ##########################################
Route::group(['prefix' => 'report/', 'middleware' => 'auth', 'namespace' => '', 'as' => 'report.'], function(){

    //socks code generate 
    Route::get('packet-code-generate', [PacketCodeController::class, 'socks_code_generate'])->name('packet_code_generate');   
    Route::post('packet/generate-pdf', [PacketCodeController::class, 'generate_pdf'])->name('packet.generate_pdf');   
    //end socks code generate
});


#################################################
# Shop Keeper Dashboard Section Start
#################################################
Route::group(['prefix' => 'shop-keeper', 'namespace' => 'Shopkeeper', 'as' => 'shopkeeper.', 'middleware' => 'auth'], function(){
    Route::get('home', [ShopkeeperSingleRackController::class, 'home'])->name('home');
    Route::post('/calculate-commission', [ShopkeeperSingleRackController::class, 'calculateCommission'])->name('calculate_commission');
    Route::post('/socks/sold', [ShopkeeperSingleRackController::class, 'socksSold'])->name('socks.sold');
});
#################################################
# Shop Keeper Dashboard Section End
#################################################


#################################################
# Rack Product Delete Section Start
#################################################
Route::group(['prefix' => 'rack/socks-return', 'namespace' => 'Rack', 'as' => 'rack.socks_return.', 'middleware' => 'auth'], function(){
    Route::get('index', [RackProductDeleteController::class, 'index'])->name('index');
    Route::get('socks_return_voucher', [RackProductDeleteController::class, 'socks_return_voucher'])->name('socks_return_voucher');
    Route::post('generate_socks_return_voucher', [RackProductDeleteController::class, 'generate_socks_return_voucher'])->name('generate_socks_return_voucher');
    Route::post('find-socks-list', [RackProductDeleteController::class, 'findSocksList'])->name('find_socks_list');
    Route::post('find-socks', [RackProductDeleteController::class, 'findSocks'])->name('find_socks');
    Route::post('delete-socks', [RackProductDeleteController::class, 'deleteSocks'])->name('delete_socks');
});

#################################################
# Rack Product Delete Section End
#################################################





#################################################
# FInance BIll COllection Section Start
#################################################

Route::group(['prefix' => 'finance/rack/bill-collection', 'namespace' => 'Finance\Rack', 'as' => 'finance.rack.bill-collection.', 'middleware' => 'auth'],function(){

          Route::get('search', [FinaceRackBillCollectionController::class, 'search'])->name('search');
          Route::get('search-result', [FinaceRackBillCollectionController::class, 'search_result'])->name('search_result');
          Route::post('approved-amount', [FinaceRackBillCollectionController::class, 'approved_amount'])->name('approved_amount');

    });


#################################################
# FInance BIll COllection End Start
#################################################







#################################################
# Search Socks From Rack
#################################################
Route::group(['prefix' => 'shopkeeper', 'namespace' => 'Shopkeeper', 'as' => 'shopkeeper.', 'middleware' => 'auth'], function(){
    Route::post('/search-socks', [SearchRackSocksController::class, 'searchSocks'])->name('search_socks');
});

#################################################
# Search Socks From Rack
#################################################



#################################################
# Bill Collection Voucher Section Start
#################################################

Route::group(['prefix' => 'voucher', 'namespace' => 'voucher', 'as' => 'voucher.', 'middleware' => 'auth'], function(){
    Route::get('/shop/rack-bill-info/{voucher_no}', [ShopRackBillCollectionVoucherController::class, 'voucherInfo'])->name('voucher_info');
    Route::get('/shop/rack-bill-voucher/{voucher_no}', [ShopRackBillCollectionVoucherController::class, 'voucherShow'])->name('rack_bill_collection.voucher_show');
});
#################################################
# Bill Collection Voucher Section End
#################################################

#################################################
# Shopkeeper message Collection Section Start
#################################################

Route::group(['prefix' => 'shopkeeper', 'namespace' => 'Shopkeeper', 'as' => 'shopkeeper.', 'middleware' => 'auth'], function(){
    Route::post('message-send', [CustomMessageController::class, 'messageSend'])->name('message_send');
});
#################################################
# Shopkeeper message Collection Section End
#################################################


########################################### report stock summary ##############################
Route::group(['prefix'=>'report', 'namespace'=>'Report', 'as'=>'report.stock.',], function(){

    Route::get('stock/summary', [StockSumarryController::class, 'index'])->name('summary');
    Route::post('stock/details', [StockSumarryController::class, 'details'])->name('summary-details');

});
########################################### report stock summary ##############################

####################################################### Account route #########################################
###############################################################################################################


################################################## transaction route ############################################

 Route::group(['prefix' =>'account', 'namespace'=>'Accounts', 'as' => 'account.transaction.' ], function(){

    Route::get('transaction/create', [TransactionController::class, 'create'])->name('create');
    Route::post('transaction/store', [TransactionController::class, 'store'])->name('store');
    Route::get('transaction/auth', [TransactionController::class, 'transaction_auth'])->name('auth');
    Route::post('transaction/auth_process', [TransactionController::class, 'auth_process'])->name('auth_process');
    Route::post('transaction/decline', [TransactionController::class, 'decline'])->name('decline');

 });


################################################## end  transaction route #######################################

########################################################## Report ###############################################
Route::group(['prefix' =>'account/report/transfer/', 'namespace'=>'Accounts/Report', 'as' => 'account.report.transfer.' ], function(){

    Route::get('index', [TransferReport::class, 'index'])->name('index');
    Route::post('details', [TransferReport::class, 'details'])->name('details');
    

 });

 Route::group(['prefix' =>'account/report/balance/', 'namespace'=>'Accounts/Report', 'as' => 'account.report.gl.' ], function(){

    Route::get('balance', [TransferReport::class, 'balance'])->name('balance');
      Route::get('balance_2', [TransferReport::class, 'balance_2'])->name('balance_2');
   
    

 });
########################################################## Report ###############################################

####################################################### Account route #########################################
#########################################################################################################



#################################### Commission SetUp ###############################################
Route::group(['prefix'=>'parameter-setup/',  'as'=>'parameter_setup.commission.'], function(){

    Route::get('commission', [CommissionSetUpController::class, 'create'])->name('create');
    Route::post('commission', [CommissionSetUpController::class, 'store'])->name('store');
    

});
#################################### End Commission SetUp ###########################################


########################################### report Commission  ##############################
Route::group(['prefix'=>'report', 'namespace'=>'Report', 'as'=>'report.commission.',], function(){

    Route::get('commission/index', [CommissionReport::class, 'index'])->name('index');
    Route::post('commission/details', [CommissionReport::class, 'details'])->name('details');

});
########################################### report Commission  ##############################

########################################### report rack fill up  ##############################
Route::group(['prefix'=>'report', 'namespace'=>'Report', 'as'=>'report.rackfill.',], function(){

    Route::get('Rack-fill/index', [RackFillUpReport::class, 'index'])->name('index');
    Route::post('Rack-fill/details', [RackFillUpReport::class, 'details'])->name('details');

});
########################################### report rack fill up  ##############################


########################################### report Product   ##############################
Route::group(['prefix'=>'report', 'namespace'=>'Report', 'as'=>'report.Rack-product.',], function(){

    Route::get('Rack-product/index', [RackProductDetailsReport::class, 'index'])->name('index');
    Route::post('Rack-product/details', [RackProductDetailsReport::class, 'details'])->name('details');

});
########################################### report Product  ##############################



####################################################### Account route #########################################
###############################################################################################################


################################################## transaction route ############################################

 Route::group(['prefix' =>'account', 'namespace'=>'Accounts', 'as' => 'account.transaction.' ], function(){

    Route::get('transaction/create', [TransactionController::class, 'create'])->name('create');
    Route::post('transaction/store', [TransactionController::class, 'store'])->name('store');
    Route::get('transaction/auth', [TransactionController::class, 'transaction_auth'])->name('auth');
    Route::post('transaction/auth_process', [TransactionController::class, 'auth_process'])->name('auth_process');
    Route::post('transaction/decline', [TransactionController::class, 'decline'])->name('decline');

 });


################################################## end  transaction route #######################################

########################################################## Report ###############################################
Route::group(['prefix' =>'account/report/transfer/', 'namespace'=>'Accounts/Report', 'as' => 'account.report.transfer.' ], function(){

    Route::get('index', [TransferReport::class, 'index'])->name('index');
    Route::post('details', [TransferReport::class, 'details'])->name('details');
    
    

 });



 Route::group(['prefix' =>'account/report/balance/', 'namespace'=>'Accounts/Report', 'as' => 'account.report.gl.' ], function(){

    Route::get('balance', [TransferReport::class, 'balance'])->name('balance');
    Route::get('balance_2', [TransferReport::class, 'balance_2'])->name('balance_2');
    Route::get('pdf', [TransferReport::class, 'generatePdt'])->name('pdf');
   
    

 });
########################################################## Report ###############################################

####################################################### Account route #########################################
#########################################################################################################


##### Rack Wrong Sold Item Controller #######
Route::group(['prefix' => 'agent/sold-delete', 'namespace' => 'Agent\SoldDelete', 'as' => 'agent.sold_delete.', 'middleware' => 'auth'], function(){
    Route:: get('rack-list', [RackWrongSoldItemDeteController::class, 'rackList'])->name('rack_list');
    Route:: get('rack-sold-information/{rack_code}', [RackWrongSoldItemDeteController::class, 'rackSoldInformation'])->name('rack_sold_information');
    Route:: post('calculate-socks', [RackWrongSoldItemDeteController::class, 'calculateSocks'])->name('single_rack.calculateSocks');
    Route:: post('unsold-sold-items', [RackWrongSoldItemDeteController::class, 'unsoldSoldItems'])->name('single_rack.unsold_sold_items');
});
##### Rack Wrong Sold Item Controller ########



#################################################
# Account Manager Dashboard Section Start
#################################################
Route::group(['prefix' => 'account-manager', 'namespace' => 'AccountManager', 'as' => 'account_manager.', 'middleware' => 'auth'], function(){
    
    Route::get('/home', [AccountManagerController::class, 'home'])->name('account_manager.home');
   
});
#################################################
# Account Manager Dashboard Section End


#################################### Shop voucher report  ###############################################

Route::group(['prefix'=>'report/shop', 'as'=>'report.shop.voucher.', 'middleware'=>'auth'], function(){

    Route::get('/voucher', [ShopVoucherReport::class, 'index'])->name('index');

});

#################################### end  Shop voucher report  ###########################################



