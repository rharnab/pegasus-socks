@extends('layouts.app')
@section('title','Shopkeeper')

@push('css')

<style>
    .p-3 {
        padding: 0.5rem !important;
    }
</style>

@endpush
@section('content')
<!-- BEGIN Page Content -->
       
        <div class="row">
            <div class="col-md-12">
                <h4 style="text-transform: uppercase; font-weight:bold; text-align:center">
                    <i class="fal fa-shopping-bag"></i> &nbsp; {{ $rack_info[0]->shop_name }} ( {{ $rack_info[0]->rack_code   }} )
                </h4>
            </div>
        </div>

        <br>

        <div class="row" id="camission_calcualate_container">
            <div class="col-4 ">
                <div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            <span id="selected_shocks" style="font-size: 18px;">0 Pair</span>
                            <small class="m-0 l-h-n">Sold Pair</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-4 ">
                <div class="p-3 bg-warning-400 rounded overflow-hidden position-relative text-white mb-g">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            <span id="selected_shocks" style="font-size: 18px;">0 TK</span>
                            <small class="m-0 l-h-n">Total Bill</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-4 ">
                <div class="p-3 bg-success-20@extends('layouts.app')
                @section('title','Agent-Dashboard')
                
                @push('css')
                
                <style>
                    .p-3 {
                        padding: 0.5rem !important;
                    }
                </style>
                
                @endpush
                @section('content')
                <!-- BEGIN Page Content -->       
                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="text-transform: uppercase; font-weight:bold; text-align:center">
                                    <i class="fal fa-shopping-bag"></i> &nbsp; {{ $rack_info[0]->shop_name }} ( {{ $rack_info[0]->rack_code   }} )
                                </h4>
                            </div>
                        </div>
                
                        <br>
                        <div class="row">
                            <div class="col-4 ">
                                <div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            <span style="font-size: 18px;">{{ $total_sell_due }} Pair</span>
                                            <small class="m-0 l-h-n">Total Sold</small>
                                        </h3>
                                    </div>
                                    <i class="fal fa-socks position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="p-3 bg-danger-400 rounded overflow-hidden position-relative text-white mb-g">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            <span style="font-size: 18px;">{{ number_format($total_due_bill,2) }} TK</span>
                                            <small class="m-0 l-h-n">Total Due</small>
                                        </h3>
                                        <i class="fal fa-usd-circle position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4" style="font-size: 6rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="p-3 bg-danger-200 rounded overflow-hidden position-relative text-white mb-g">
                                    <div class="">
                                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                                            <span style="font-size: 18px;">{{ number_format($shop_commission,2) }} TK</span>
                                            <small class="m-0 l-h-n">Comission</small>
                                        </h3>
                                        <i class="fal fa-usd-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
                                    </div>
                                </div>
                            </div>   
                        </div>
                
                        <div class="row" id="camission_calcualate_container">  
                        </div>
                        <br>
                            
                        <div class="row">
                            <div class="col-md-12">
                                <div id="panel-2" class="panel">
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            <div class="accordion accordion-outline" id="m">  
                    
                                                @foreach($rack_shocks_array as $rack_shocks_size)
                                                
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#1" onclick="collapaseShow({{ $rack_shocks_size['type_id'] }})" aria-expanded="true">
                                                                
                                                                <i class="fal fa-socks width-2 fs-xl"></i>
                                                                {{ $rack_shocks_size['name'] }} - ( {{ $rack_shocks_size['total'] }} Pair)
                                                                <span class="ml-auto">
                                                                    <span class="collapsed-reveal">
                                                                        <i class="fal fa-minus fs-xl"></i>
                                                                    </span>
                                                                    <span class="collapsed-hidden">
                                                                        <i class="fal fa-plus fs-xl"></i>
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <div id="m" class="collapse shocks_collapse_{{ $rack_shocks_size['type_id'] }}" data-parent="#1">
                                                            <div class="card-body">
                                                                <div class="frame-wrap">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input" id="{{ $rack_shocks_size['type_id'] }}">
                                                                        <label class="custom-control-label" onclick="selectAllSocks({{ $rack_shocks_size['type_id'] }})" for="{{ $rack_shocks_size['type_id'] }}">Select All {{ $rack_shocks_size['name'] }} Socks</label>
                                                                    </div>
                
                                                                    <hr>
                
                                                                    <div class="demo">
                                                                        @foreach($rack_shocks_array[$rack_shocks_size['type_id']]['shocks'] as $size_single_shocks)
                                                                            <div class="custom-control custom-checkbox single_checkbox">
                                                                                <input type="checkbox" onclick="countTotalSoldoutSocks('{{ $size_single_shocks['shocks_code'] }}')" class="select_items custom-control-input rack_single_shocks_{{  $size_single_shocks['shocks_type_id'] }}" value="{{ $size_single_shocks['shocks_code'] }}" id="{{ $size_single_shocks['shocks_code'] }}">
                                                                                <label  class="custom-control-label" for="{{ $size_single_shocks['shocks_code'] }}">{{ $size_single_shocks['print_shocks_code'] }} ({{  $size_single_shocks['brand_name'] }} - {{  $size_single_shocks['brand_size_name'] }})</label>
                                                                            </div>
                                                                        @endforeach                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                              
                                                @endforeach              
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                    
                
                        <input type="hidden" id="calculate_shocks_bill_route" value="{{ route('shopkeeper.calculate_commission') }}">
                        <input type="hidden" id="shopkeeper_socks_sold" value="{{ route('shopkeeper.socks.sold') }}">
                        <input type="hidden" id="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="rack_code" value="{{ $rack_info[0]->rack_code }}">
                        <input type="hidden" id="shop_id" value="{{ $rack_info[0]->shop_id }}">
                
                @endsection
                
                @push('js')
                
                <script>
                    function selectAllSocks(socks_size_id){
                        var classname = "rack_single_shocks_"+socks_size_id;
                        var select_all = $("#"+socks_size_id).is(':checked');
                        if(select_all === false){
                            $("input."+classname+":checkbox").prop('checked',true);
                        }else{
                            $("input."+classname+":checkbox").prop('checked',false);
                        }
                
                        // show shocks list
                        getSelectedBoxCount();  
                
                        amountCalculation();    
                        
                    }
                
                    function  getSelectedBoxCount(){
                        var numberOfChecked = $('.select_items:checked').length;
                        $("#selected_shocks").html(numberOfChecked);
                    }
                
                    function countTotalSoldoutSocks(shock_code){
                        amountCalculation(); 
                        var numberOfChecked = $('.select_items:checked').length;
                        $("#selected_shocks").html(numberOfChecked); 
                              
                    }
                     
                    
                </script>
                
                
                <script>
                    function amountCalculation(){
                        var socks = []
                        var checkboxes = document.querySelectorAll('.single_checkbox input[type=checkbox]:checked')
                
                        for (var i = 0; i < checkboxes.length; i++) {
                            socks.push(checkboxes[i].value)
                        }
                           
                      
                        var calculate_shocks_bill_route = $("#calculate_shocks_bill_route").val();
                        var _token = $("#_token").val();
                
                    
                        $.ajax({
                            type: 'POST',
                            url: calculate_shocks_bill_route,
                            data: {
                                "shocks" : socks,
                                "_token" : _token,
                            },
                            beforeSend: function() {
                                //  loaderStart();
                            },
                            success: (data) => {
                                $("#camission_calcualate_container").html(data);               
                            },
                            error: function(data) {
                                console.log(data);
                            },
                            complete: function() {
                                // loaderEnd();
                            }
                        });
                    }
                </script>
                
                
                <script>
                    function shopKeeperSold(){
                
                        var socks = []
                        var checkboxes = document.querySelectorAll('.single_checkbox input[type=checkbox]:checked')
                
                        for (var i = 0; i < checkboxes.length; i++) {
                            socks.push(checkboxes[i].value)
                        }
                
                        if( socks.length > 0 ){ 
                            
                            var shopkeeper_socks_sold = $("#shopkeeper_socks_sold").val();
                            var rack_code                = $("#rack_code").val();
                            var shop_id                  = $("#shop_id").val();
                            var _token                   = $("#_token").val();
                
                        cuteAlert({
                                type       : "question",
                                title      : "Confirmation",
                                message    : "Are your sure ? You want to sales mark selected socks",
                                confirmText: "Yes",
                                cancelText : "No"
                            }).then((e)=>{
                                if (e == ("confirm")){
                                    $.ajax({
                                        type: 'POST',
                                        url : shopkeeper_socks_sold,
                                        data: {
                                            "shocks"   : socks,
                                            "rack_code": rack_code,
                                            "shop_id": shop_id,
                                            "_token"   : _token,
                                        },
                                        beforeSend: function() {
                                            loaderStart();
                                        },
                                        success: (data) => {
                                            if(data.status === 200){
                                                cuteAlert({
                                                    type      : "success",
                                                    title     : "Success",
                                                    message   : data.message,
                                                    buttonText: "ok"
                                                }).then((e)=>{
                                                    //window.location.replace("voucher-download/" + data.bill_no);
                                                    location.reload(true);
                                                });
                                            }else{
                                                cuteAlert({
                                                    type: "error",
                                                    title: "Error",
                                                    message: data.message,
                                                    buttonText: "Try Again"
                                                });                                 
                                            }          
                                        },
                                        error: function(data) {
                                            console.log(data);
                                        },
                                        complete: function() {
                                            loaderEnd();
                                        }
                                    });
                                }
                            })
                        }
                       
                    }
                </script>
                
                
                <script>
                    function collapaseShow(id){
                        $(".shocks_collapse_"+id).toggleClass("show");
                    }
                </script>
                
                
                
                
                @endpush
                0 rounded overflow-hidden position-relative text-white mb-g">
                    <div class="">
                        <h3 class="display-4 d-block l-h-n m-0 fw-500">
                            <span id="selected_shocks" style="font-size: 18px;">0 TK</span>
                            <small class="m-0 l-h-n">S.Comission</small>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button type="button" class="btn  btn-sm btn-danger waves-effect waves-themed w-100" disabled >  
                    SELECT SOCKS PAIR
                </button>
            </div>    
        </div>
        <br>
            
        <div class="row">
            <div class="col-md-12">
                <div id="panel-2" class="panel">
                    <div class="panel-container show">
                        <div class="panel-content">
                            <div class="accordion accordion-outline" id="m">  
    
                                @foreach($rack_shocks_array as $rack_shocks_size)
                                
                                    <div class="card">
                                        <div class="card-header">
                                            <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#1" onclick="collapaseShow({{ $rack_shocks_size['type_id'] }})" aria-expanded="true">
                                                
                                                <i class="fal fa-socks width-2 fs-xl"></i>
                                                {{ $rack_shocks_size['name'] }} - ( {{ $rack_shocks_size['total'] }} Pair)
                                                <span class="ml-auto">
                                                    <span class="collapsed-reveal">
                                                        <i class="fal fa-minus fs-xl"></i>
                                                    </span>
                                                    <span class="collapsed-hidden">
                                                        <i class="fal fa-plus fs-xl"></i>
                                                    </span>
                                                </span>
                                            </a>
                                        </div>
                                        <div id="m" class="collapse shocks_collapse_{{ $rack_shocks_size['type_id'] }}" data-parent="#1">
                                            <div class="card-body">
                                                <div class="frame-wrap">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="{{ $rack_shocks_size['type_id'] }}">
                                                        <label class="custom-control-label" onclick="selectAllSocks({{ $rack_shocks_size['type_id'] }})" for="{{ $rack_shocks_size['type_id'] }}">Select All {{ $rack_shocks_size['name'] }} Socks</label>
                                                    </div>

                                                    <hr>

                                                    <div class="demo">
                                                        @foreach($rack_shocks_array[$rack_shocks_size['type_id']]['shocks'] as $size_single_shocks)
                                                            <div class="custom-control custom-checkbox single_checkbox">
                                                                <input type="checkbox" onclick="countTotalSoldoutSocks('{{ $size_single_shocks['shocks_code'] }}')" class="select_items custom-control-input rack_single_shocks_{{  $size_single_shocks['shocks_type_id'] }}" value="{{ $size_single_shocks['shocks_code'] }}" id="{{ $size_single_shocks['shocks_code'] }}">
                                                                <label  class="custom-control-label" for="{{ $size_single_shocks['shocks_code'] }}">{{ $size_single_shocks['print_shocks_code'] }} ({{  $size_single_shocks['brand_name'] }} - {{  $size_single_shocks['brand_size_name'] }})</label>
                                                            </div>
                                                        @endforeach                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                              
                                @endforeach              
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    

        <input type="hidden" id="calculate_shocks_bill_route" value="{{ route('agent.rack.calculate_shocks_bill') }}">
        <input type="hidden" id="scoks_bill_collect_route" value="{{ route('agent.rack.socks.bill_collect') }}">
        <input type="hidden" id="_token" value="{{ csrf_token() }}">
        <input type="hidden" id="rack_code" value="{{ $rack_info[0]->rack_code }}">
        <input type="hidden" id="shop_id" value="{{ $rack_info[0]->shop_id }}">

@endsection

@push('js')

<script>
    function selectAllSocks(socks_size_id){
        var classname = "rack_single_shocks_"+socks_size_id;
        var select_all = $("#"+socks_size_id).is(':checked');
        if(select_all === false){
            $("input."+classname+":checkbox").prop('checked',true);
        }else{
            $("input."+classname+":checkbox").prop('checked',false);
        }

        // show shocks list
        getSelectedBoxCount();  

        amountCalculation();    
        
    }

    function  getSelectedBoxCount(){
        var numberOfChecked = $('.select_items:checked').length;
        $("#selected_shocks").html(numberOfChecked);
    }

    function countTotalSoldoutSocks(shock_code){
        amountCalculation(); 
        var numberOfChecked = $('.select_items:checked').length;
        $("#selected_shocks").html(numberOfChecked); 
              
    }
     
    
</script>


<script>
    function amountCalculation(){
        var socks = []
        var checkboxes = document.querySelectorAll('.single_checkbox input[type=checkbox]:checked')

        for (var i = 0; i < checkboxes.length; i++) {
            socks.push(checkboxes[i].value)
        }
           
      
        var calculate_shocks_bill_route = $("#calculate_shocks_bill_route").val();
        var _token = $("#_token").val();

    
        $.ajax({
            type: 'POST',
            url: calculate_shocks_bill_route,
            data: {
                "shocks" : socks,
                "_token" : _token,
            },
            beforeSend: function() {
                //  loaderStart();
            },
            success: (data) => {
                $("#camission_calcualate_container").html(data);               
            },
            error: function(data) {
                console.log(data);
            },
            complete: function() {
                // loaderEnd();
            }
        });
    }
</script>


<script>
    function voucherGenerateAndSoldOutConfirm(){

        var socks = []
        var checkboxes = document.querySelectorAll('.single_checkbox input[type=checkbox]:checked')

        for (var i = 0; i < checkboxes.length; i++) {
            socks.push(checkboxes[i].value)
        }

        if( socks.length > 0 ){ 
            
            var scoks_bill_collect_route = $("#scoks_bill_collect_route").val();
            var rack_code                = $("#rack_code").val();
            var shop_id                  = $("#shop_id").val();
            var _token                   = $("#_token").val();

        cuteAlert({
                type       : "question",
                title      : "Confirmation",
                message    : "Are your sure ? You want to sales mark selected socks",
                confirmText: "Yes",
                cancelText : "No"
            }).then((e)=>{
                if (e == ("confirm")){
                    $.ajax({
                        type: 'POST',
                        url : scoks_bill_collect_route,
                        data: {
                            "shocks"   : socks,
                            "rack_code": rack_code,
                            "shop_id": shop_id,
                            "_token"   : _token,
                        },
                        beforeSend: function() {
                            loaderStart();
                        },
                        success: (data) => {
                            if(data.status === 200){
                                cuteAlert({
                                    type      : "success",
                                    title     : "Success",
                                    message   : data.message,
                                    buttonText: "ok"
                                }).then((e)=>{
                                    //window.location.replace("voucher-download/" + data.bill_no);
                                    location.reload(true);
                                });
                            }else{
                                cuteAlert({
                                    type: "error",
                                    title: "Error",
                                    message: data.message,
                                    buttonText: "Try Again"
                                });                                 
                            }          
                        },
                        error: function(data) {
                            console.log(data);
                        },
                        complete: function() {
                            loaderEnd();
                        }
                    });
                }
            })
        }
       
    }
</script>


<script>
    function collapaseShow(id){
        $(".shocks_collapse_"+id).toggleClass("show");
    }
</script>




@endpush
