@extends('Layouts.master')

@section('title','Coupon-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#coupen-form" class="btn btn-primary round">Create Coupon</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Coupon</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Display success message if available in the session -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: absolute; top: 20px; right: 20px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <section class="section" id="coupen-data">
        <div class="card">
            <div class="card-header">Coupon List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Coupon Code</th>
                            <th>No of Usage</th>
                            <th>Discount type</th>
                            <th>Discount</th>
                            <th>Maximum Discount</th>
                            <th>Min amount</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupenData as $data)
                        <tr>
                            <td>{{$data->title}}</td>
                            <td>{{$data->coupen_code}}</td>
                            <td>{{$data->no_of_usage}}</td>
                            <td>{{$data->discount_type}}</td>
                            <td>{{$data->discount}}</td>
                            <td>{{$data->max_discount}}</td>
                            <td>{{$data->min_amount}}</td>
                            <td>{{$data->from_date}}</td>
                            <td>{{$data->to_date}}</td>
                            <td>
                                <a href="#coupen-form" class="btn icon btn-primary edit-coupen-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/coupen/delete/'. $data->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn icon btn-danger" onclick="return confirm('Are you sure you want to delete?')">
                                        <i data-feather="delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- //Form for coupen -->
    <section id="coupen-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Coupon</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="coupenForm" action="{{ route('coupen.store')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" id="title" class="form-control" placeholder="Title"
                                                name="title" value="{{old('title')}}">
                                                @error('title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="coupen_code">Coupon code</label>
                                            <input type="text" id="coupen_code" class="form-control" placeholder="Coupon code"
                                                name="coupen_code">
                                                @error('coupen_code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="no_of_usage">Number of usage</label>
                                            <input type="text" id="no_of_usage" class="form-control" placeholder="Number of usage" name="no_of_usage" value="{{old('no_of_usage')}}">
                                            @error('no_of_usage')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount_type">Discount Type</label>
                                            <select id="discount_type" class="form-control" name="discount_type">
                                                <option value="">Select Discount Type</option>
                                                <option value="flat"> Flat </option>
                                                <option value="percentage"> Percentage </option>
                                            </select> 
                                            @error('discount_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror  
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount">Discount value</label>
                                            <input type="text" id="discount" class="form-control" placeholder="Discount" name="discount" value="{{old('discount')}}">
                                            @error('discount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount">Maximum Discount</label>
                                            <input type="text" id="max_discount" class="form-control" placeholder="Maximum Discount" name="max_discount" value="{{old('max_discount')}}">
                                            @error('max_discount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="min_amount">Minimum amount</label>
                                            <input type="text" id="min_amount" class="form-control" placeholder="Minimum amount" name="min_amount" value="{{old('min_amount')}}">
                                            @error('min_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="from_date">From Date</label>
                                            <input type="date" id="from_date" class="form-control" name="from_date" value="{{old('from_date')}}">
                                            @error('from_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="to_date">To date</label>
                                            <input type="date" id="to_date" class="form-control" name="to_date" value="{{old('to_date')}}">
                                            @error('to_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <input type="hidden" id="coupen_id" name="coupen_id" value="">                                   
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" id="submitBtn" class="btn btn-primary me-1 mb-1">Submit</button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- // Basic multiple Column Form section end -->
</div>

@endsection

@push('scripts')
    <script src="{{asset('vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{asset('js/vendors.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            $('.edit-coupen-btn').on('click', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#coupen-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the coupen ID from the data attribute
                var coupenId = $(this).data('id');
                
                //make an AJAX request to fetch the coupen data
                $.ajax({
                   url: '/coupen/'+ coupenId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#title').val(data.title);
                    $('#coupen_code').val(data.coupen_code);
                    $('#no_of_usage').val(data.no_of_usage);
                    $('#discount_type').val(data.discount_type);
                    $('#discount').val(data.discount);
                    $('#max_discount').val(data.max_discount);
                    $('#min_amount').val(data.min_amount);
                    $('#from_date').val(data.from_date);
                    $('#to_date').val(data.to_date);

                   
                    // Adjust the form action, method, and submit button text for update
                    $('#coupenForm').attr('action', '{{ url('coupen/update') }}'+'/'+ coupenId);
                    $('#coupenForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#coupenForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Coupon');
                   },
                   error: function(error){
                    console.error('Error:', error);
                   }
                });
            });
        });
    </script>
@endpush

