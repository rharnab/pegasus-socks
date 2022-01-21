@extends('layouts.app')
@section('title','Product Types')

@push('css')

 

<link rel="stylesheet" href="{{ asset('public/backend/assets/css/datagrid/datatables/datatables.bundle.css') }}">
 <link rel="stylesheet" href="{{ asset('public/backend/assets/css/notifications/toastr/toastr.css') }}">


@endpush
@section('content')
<!-- BEGIN Page Content -->
    <main id="js-page-content" role="main" class="page-content">
        <ol class="breadcrumb page-breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item">Parameter Setup</li>
            <li class="breadcrumb-item active"> Product </li>
            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
        </ol>

<div class="subheader">
        <h1 class="subheader-title">            
           

            <span style="float:right">
                <a href="{{route('parameter_setup.products.create')}}" class="btn btn-sm btn-primary">
                    <span class="fal fa-plus mr-1"></span> Add New
                </a>
            </span>
            
        </h1>
    </div>

        <div class="row">
            <div class="col-xl-12">
                <!-- data table -->
                 <div id="panel-1" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
                                            Product list
                                        </h2>

                                        <div class="panel-toolbar">
                                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                                            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                                        </div>
                                    </div>
                                    <div class="panel-container show">
                                        <div class="panel-content">
                                            
                                            <!-- datatable start -->
                                            <table id="dt-basic-example" class="table table-bordered table-hover table-striped table-sm w-100">
                                                <thead class="bg-primary-600 text-uppercase">
                                                    <tr>

                                                        <th>#SL</th>
                                                        <th>Brand Name</th>
                                                        <th>Brand Size Id</th>
                                                        <th>Type Id</th>
                                                        <th>Per Packet Socks Pair Quantity</th>
                                                        <th>Packet Buying Price</th>
                                                        <th>Packet Selling Price</th>
                                                        <th>Individual Buying Price</th>
                                                        <th>Individual Selling Price</th>
                                                        <th>Sale Type</th>
                                                        <th>Entry Date & Time</th>
                     
                                                    </tr>


                                                </thead>
                                                <tbody>
                                                	

                                                     @php $sl=1; @endphp
                                                     
                                                    @foreach($get_data as $single_data)

                                                        <tr>
                                                            <td>{{$sl++}}</td>
                                                            <td>{{$single_data->brand_name}}</td>
                                                            <td>{{$single_data->brand_size_name}}</td>
                                                            <td>{{$single_data->types_name}}</td>
                                                            <td>{{$single_data->packet_socks_pair_quantity}}</td>
                                                            <td>{{$single_data->packet_buying_price}}</td>
                                                            <td>{{$single_data->packet_selling_price}}</td>
                                                            <td>{{$single_data->individual_buying_price}}</td>
                                                            <td>{{$single_data->individual_selling_price}}</td>
                                                            <td><?php 

                                                            $sale_type = $single_data->sale_type;

                                                            if ($sale_type==1) {
                                                                
                                                                echo "Single";

                                                            }elseif($sale_type==2){

                                                                 echo "Rak";
                                                            }

                                                        ?></td>
                                                            <td>{{$single_data->entry_date}} - {{$single_data->entry_time}}</td>
                                                        </tr>

                                                     @endforeach

                                                </tbody>
                                            </table>
                                            <!-- datatable end -->
                                        </div>
                                    </div>
                                </div>

                <!-- data table -->
            </div>

        </div>


        




    </main>
@endsection

@push('js')

 <script src="{{ asset('public/backend/assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
 <script src="{{ asset('public/backend/assets/js/datagrid/datatables/datatables.export.js') }}"></script>
  <script src="{{ asset('public/backend/assets/js/notifications/toastr/toastr.js') }}"></script>

  
    <script>

    /*data table script*/

     $(document).ready(function()
            {

                // initialize datatable
                $('#dt-basic-example').dataTable(
                {
                    responsive: true,
                    lengthChange: false,
                    dom:
                        "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        /*{
                            extend:    'colvis',
                            text:      'Column Visibility',
                            titleAttr: 'Col visibility',
                            className: 'mr-sm-3'
                        },*/
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1'
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            titleAttr: 'Generate Excel',
                            className: 'btn-outline-success btn-sm mr-1'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV',
                            titleAttr: 'Generate CSV',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'copyHtml5',
                            text: 'Copy',
                            titleAttr: 'Copy to clipboard',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm'
                        }
                    ]
                });

            });

    /*data table script*/




        //tostr message 
         @if(Session::has('message'))
		  toastr.success("{{ session('message') }}");
		  @endif
    </script>




@endpush
