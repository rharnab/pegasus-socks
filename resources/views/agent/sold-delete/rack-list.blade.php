@extends('layouts.app')
@section('title','Agent-Dashboard')

@push('css')

 


@endpush
@section('content')
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
       <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
       <li class="breadcrumb-item">Agent</li>
       <li class="breadcrumb-item active"> Dashboard </li>
       <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
    </ol>
    <div class="row">
       @foreach($shop_racks as $shop_rack)
       <div class="col-sm-6 col-xl-3">
           <a href="{{ route('agent.sold_delete.rack_sold_information', [Crypt::encrypt( $shop_rack->rack_code )]) }}">
            <div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white">
                <div class="">
                   <h3 class="display-4 d-block l-h-n m-0 fw-500">
                       {{ $shop_rack->rack_code }}
                      <small class="m-0 l-h-n">
                           {{ $shop_rack->shop_name }}
                      </small>
                   </h3>
                </div>
                <i class="fal fa-gem position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4" style="font-size: 6rem;"></i>
             </div>
           </a>
          
       </div>
       @endforeach
    </div>
 </main>  
@endsection

@push('js')






@endpush
