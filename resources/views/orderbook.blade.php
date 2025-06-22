@extends('Layouts.master')

@section('title','Food-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order Book</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Display success message if available in the session -->
    <section class="section" id="food-data">
        <div class="card">
            <div class="card-header">Order Book List </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filter-date" class="form-label">Filter by Delivery Date</label>
                        <input type="text" id="filter-date" class="form-control datepicker">
                    </div>
                    <div class="col-md-4">
                        <label for="filter-time-slot" class="form-label">Filter by Time Slot</label>
                        <select id="filter-time-slot" class="form-control">
                            <option value="">Select Time Slot</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{$slot->time_slot}}">{{$slot->time_slot}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Order Date</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Invoice date</th>
                            <th>Coupen</th>
                            <th>Delivery Date</th>
                            <th>Time slot</th>
                            <th>Total</th>
                             <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderBook as $data)
                        <tr>
                            <td>{{date( 'd/m/Y , h:i A' , strtotime($data->created_at))}}</td>
                            <td>{{$data->id}}</td>
                            <td>{{$data->userDetail->name}}</td>
            
                            <td>{{$data->invoice_dt}}</td>
                            <td>{{$data->coupen}}</td>
                            <td>{{$data->del_dt}}</td>
                            <td>{{$data->ref1}}</td>
                            <td>{{number_format($data->payment_amount, 2)}}</td>
                            <td class="orderStatus-{{$data->id}} {{$data->status == 'delivered' ? 'text-success' : ''}}">{{($data->status == 'order' ? 'Not Deliver' : 'Delivered')}}</td>
                            <td style="white-space: nowrap;"> 
                                <a class="btn icon btn-primary view-order-detail" data-bs-toggle="modal" data-bs-target="#orderModal" data-id="{{$data->id}}">
                                    <i data-feather="eye"></i>
                                </a>
                                <a class="btn icon btn-primary change-order-status"  data-id="{{$data->id}}">
                                    <i data-feather="check-square"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!--order view  Modal -->
<div class="modal fade text-left" id="orderModal" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel130" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-info">
        <h5 class="modal-title white" id="myModalLabel130">Order Details</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <i data-feather="x"></i>
        </button>
        </div>
        <div class="modal-body">
            <div class="row mt-2" style="font-weight:500; color:black">
                <h5>Delivery Details:</h5>
                <hr>
                <div class="col-md-3">
                    Delivery Date: 
                </div>
                <div class="col-md-3 delivery-date">
                </div>
                <div class="col-md-3">
                    Timeslot: 
                </div>
                <div class="col-md-3 time-slot">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table mb-0" id="orderDetailsTable">
                <thead>
                <tr>
                    <th> Item </th>
                    <th> Quantity </th>
                    <th> Price </th>
                </tr>
                </thead>
                <tbody>
               
                </tbody>
            </table>
            </div>
            <div class="row mt-2" style="font-weight:500; color:black">
                <h5>User Details:</h5>
                <hr>
                <div class="col-md-4">
                    Name: 
                </div>
                <div class="col-md-8 name">
                </div>
                <div class="col-md-4">
                    Email: 
                </div>
                <div class="col-md-8 email">
                </div>
                <div class="col-md-4">
                    Mobile: 
                </div>
                <div class="col-md-8 mobile">
                </div>
                <div class="col-md-4">
                    Address: 
                </div>
                <div class="col-md-8 address">
                </div>
            </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
            <i class="bx bx-x d-block d-sm-none"></i>
            <span class="d-none d-sm-block">Close</span>
        </button>
        </div>
    </div>
    </div>
</div>
<!--order view  Modal ends-->

@endsection

@push('scripts')
    <script src="{{asset('vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{asset('js/vendors.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(document).ready(function($){
            $(document).on('click', '.view-order-detail', function(e){
                e.preventDefault();
                
                var orderId = $(this).data('id');
                
                //make an ajax request to fetch order details
                $.ajax({
                    url : '/orderDetails/'+orderId,
                    method : 'GET',
                    success: function(data){
                        var orders = data.data;
                        
                        $('.delivery-date').text(data.deliveryDate);
                        $('.time-slot').text(data.time_slot);

                        //Update the table with recieved data
                        var tableBody = $('#orderDetailsTable tbody');
                        tableBody.empty();

                        //Loop through each order and create a row in the table
                        orders.forEach(function(order){
                            var newRow = '<tr>' +
                                '<td style="font-weight:500">' + order.food.title + '</td>' + 
                                '<td class="text-bold-500 text-red" style="font-weight:500">' + order.qty + '</td>'+
                                '<td style="font-weight:500">'+ order.price +'</td>' +
                            '</tr>';

                            //append new raw
                            tableBody.append(newRow);
                            $('.name').text(order.user.name);
                            $('.email').text(order.user.email);
                            $('.mobile').text(order.user.mobile);
                            $('.address').text(order.user.address1 + ', pincode:' + order.user.pincode1 + ', landmark:' + order.user.landmark1);
                        });

                       

                    },
                    error: function(error){
                    console.error('Error:', error);
                   }
                });
            });

            $(document).on('click', '.change-order-status', function(e){
                e.preventDefault();

                var orderBookId = $(this).data('id');
                if (confirm('Are you sure you want to change the order status?')) {
                    //get the CSRF from header 
                    var csrfToken = $('meta[name=csrf-token]').attr('content');

                    $.ajax({
                        url : '/changeOrderStatus/'+ orderBookId,
                        method : 'POST',
                        headers: {
                            'X-CSRF-TOKEN' : csrfToken,
                        },
                        success: function(response){
                            $('.orderStatus-'+orderBookId).html('<span class="text-success">Delivered</span>');
                        },
                        error: function(xhr, textStatus, error){
                            console.error('Error:', error);
                        }
                    });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            const dataTable = new simpleDatatables.DataTable("#table1");

            function filterByDate() {
                const date = $('#filter-date').val();
                $('#table1 tbody tr').each(function() {
                    const cellData = $(this).find('td:eq(5)').text(); // assuming 'Delivery Date' is the 6th column (index 5)
                    if (date === "" || cellData.includes(date)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterByTimeSlot() {
                const slot = $('#filter-time-slot').val();
                $('#table1 tbody tr').each(function() {
                    const cellData = $(this).find('td:eq(6)').text(); // assuming 'Time Slot' is the 7th column (index 6)
                    if (slot === "" || cellData.includes(slot)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Date filter event listener
            $('#filter-date').on('change', function() {
                filterByDate();
            });

            // Time Slot filter event listener
            $('#filter-time-slot').on('change', function() {
                filterByTimeSlot();
            });
        });
    </script>
@endpush

