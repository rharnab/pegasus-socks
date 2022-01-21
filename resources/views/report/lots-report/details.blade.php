@extends('layouts.app')
@section('title','Stocks')

@push('css')

 

<link rel="stylesheet" href="{{ asset('public/backend/assets/css/datagrid/datatables/datatables.bundle.css') }}">

@endpush
@section('content')
<!-- BEGIN Page Content -->
    <main id="js-page-content" role="main" class="page-content">
        <ol class="breadcrumb page-breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item">Stock</li>
            <li class="breadcrumb-item active">  Stock Lot </li>
            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
        </ol>

<div class="subheader">
        
    </div>

        <div class="row">
            <div class="col-xl-12 col-md-12">
                <!-- data table -->
                 <div id="panel-1" class="panel">
                                    <div class="panel-hdr">
                                        <h2>
                                           Lot Details
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
                                                <thead class="bg-primary-600">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Style Code</th>
                                                        <th>Brand</th>
                                                        <th>Type</th>
                                                        <th>Size</th>
                                                        <th>Cnt</th>
                                                        <th>Quantity </th>
                                                        <th>Stock In Date</th>
                                                        <th>Stock Out Date</th>
                                                        <th>Bundle Buy Price</th>
                                                        <th>Single Buy Price</th>
                                                        <th>Bundle Sales Price</th>
                                                        <th>Single Sales Price</th>
                                                        <th>Sales Officer</th>
                                                        <th>Amount Received Date</th>
                                                        <th>Market Sales Price</th>
                                                        <th>Voucher No</th>
                                                        <th>Lot No</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                	@php
                                                	$sl=1;
                                                	@endphp
                                                	@foreach($lot_details as $single_lot)

                                                	<tr>
                                                		
                                                			<td>	{{ $sl++ }} </td>
                                                			<td> {{ $single_lot->style_code }}</td>
                                                			<td> {{ $single_lot->brand }}</td>
                                                			<td> {{ $single_lot->types_name }}</td>
                                                			<td> {{ $single_lot->size_name }}</td>
                                                			<td> </td>
                                                			<td> {{ $single_lot->per_packet_shocks_quantity }}</td>
                                                			<td> {{ $single_lot->stock_in_date }}</td>
                                                			<td> {{ $single_lot->stock_out_date }}</td>
                                                			<td> {{ $single_lot->packet_buy_price }}</td>
                                                			<td> {{ $single_lot->individual_buy_price }}</td>
                                                			<td> {{ $single_lot->packet_sale_price }}</td>
                                                			<td> {{ $single_lot->individual_sale_price }}</td>
                                                			<td> {{ $single_lot->agent_name }}</td>
                                                			<td> {{ $single_lot->amount_receive_date }}</td>
                                                			<td> {{ $single_lot->market_sales_price }}</td>
                                                			<td> {{ $single_lot->voucher_no }}</td>
                                                			<td> {{ $single_lot->lot_no }}</td>

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
    </script>




@endpush