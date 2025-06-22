@extends('Layouts.master')

@section('title','Pincode-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#pincode-form" class="btn btn-primary round">Create Pincode</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pincode</li>
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
    <section class="section" id="pincode-data">
        <div class="card">
            <div class="card-header">Pincode List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Pincode</th>
                            <th>Place name</th>
                            <th>District</th>
                            <th>state</th>
                            <th>Delivery fee</th>
                            <th>Other fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pincodeData as $data)
                        <tr>
                            <td>{{$data->pincode}}</td>
                            <td>{{$data->place_name}}</td>
                            <td>{{$data->district}}</td>
                            <td>{{$data->state}}</td>
                            <td>{{$data->delivery_fee}}</td>
                            <td>{{$data->other_fee}}</td>
                            <td>
                                <a href="#pincode-form" class="btn icon btn-primary edit-pincode-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/pincode/delete/'. $data->id) }}" method="POST">
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
    <!-- //Form for pincode -->
    <section id="pincode-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Pincode</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="pincodeForm" action="{{ route('pincode.store')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="pincode">Pincode</label>
                                            <input type="text" id="pincode" class="form-control" placeholder="Pincode"
                                                name="pincode" value="{{old('pincode')}}">
                                                @error('pincode')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="place_name">Place</label>
                                            <input type="text" id="place_name" class="form-control" placeholder="Place Name"
                                                name="place_name">
                                                @error('place_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="district"> District </label>
                                            <input type="text" id="district" class="form-control" placeholder="District" name="district" value="{{old('district')}}">
                                            @error('district')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" id="state" class="form-control" placeholder="State" name="state" value="{{old('state')}}">
                                            @error('state')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="delivery_fee"> Delivery fee </label>
                                            <input type="text" id="delivery_fee" class="form-control" placeholder="delivery fee" name="delivery_fee" value="{{old('delivery fee')}}">
                                            @error('delivery_fee')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="other_fee">Other fee</label>
                                            <input type="text" id="other_fee" class="form-control" placeholder="Other fee" name="other_fee" value="{{old('other_fee')}}">
                                            @error('other_fee')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <input type="hidden" id="pincode_id" name="pincode_id" value="">                                   
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
            $('.edit-pincode-btn').on('click', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#pincode-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the pincode ID from the data attribute
                var pincodeId = $(this).data('id');
                
                //make an AJAX request to fetch the pincode data
                $.ajax({
                   url: '/pincode/'+ pincodeId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#pincode').val(data.pincode);
                    $('#place_name').val(data.place_name);
                    $('#district').val(data.district);
                    $('#state').val(data.state);
                    $('#delivery_fee').val(data.delivery_fee);
                    $('#other_fee').val(data.other_fee);
                    
                    // Adjust the form action, method, and submit button text for update
                    $('#pincodeForm').attr('action', '{{ url('pincode/update') }}'+'/'+ pincodeId);
                    $('#pincodeForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#pincodeForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Pincode');
                   },
                   error: function(error){
                    console.error('Error:', error);
                   }
                });
            });
        });
    </script>
@endpush

