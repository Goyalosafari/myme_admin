@extends('Layouts.master')

@section('title','Time slots-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#time-slot-form" class="btn btn-primary round">Create Time slots</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Time slots</li>
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
    <section class="section" id="time_slot-data">
        <div class="card">
            <div class="card-header">Time slots List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Time slot</th>
                            <th>Cut off</th>
                            <th>Time start</th>
                            <th>Time end</th>
                            <th>Ref</th>
                            <th>Cutoff Key</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td>{{$data->time_slot}}</td>
                            <td>{{$data->cutoff}}</td>
                            <td>{{$data->time_start}}</td>
                            <td>{{$data->time_end}}</td>
                            <td>{{$data->ref}}</td>
                            <td>{{$data->ref1}}</td>

                            <td>
                                <a href="#time-slot-form" class="btn icon btn-primary edit-time_slot-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/timeslot/delete/'. $data->id) }}" method="POST">
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
    <section id="time-slot-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Time slots</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="time_slotForm" action="{{ route('timeslot.store')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_slot">Time slot</label>
                                            <input type="text" id="time_slot" class="form-control" placeholder="Time slot"
                                                name="time_slot" value="{{old('time_slot')}}">
                                                @error('time_slot')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="cutoff">Cutoff</label>
                                            <input type="text" id="cutoff" class="form-control" placeholder="Cutoff"
                                                name="cutoff">
                                                @error('cutoff')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_start"> Time start </label>
                                            <input type="time" id="time_start" class="form-control" placeholder="District" name="time_start" value="{{old('time_start')}}">
                                            @error('time_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_end">Time end</label>
                                            <input type="time" id="time_end" class="form-control" placeholder="Time end" name="time_end" value="{{old('time_end')}}">
                                            @error('time_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ref">Ref</label>
                                            <input type="text" id="ref" class="form-control" placeholder="Ref" name="ref" value="{{old('ref')}}">
                                            @error('ref')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ref1">Cutoff Key(compulsory)</label>
                                            <input type="text" id="ref1" class="form-control" placeholder="Ref1" name="ref1" value="{{old('ref1')}}">
                                            @error('ref1')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <input type="hidden" id="time_slot_id" name="time_slot_id" value="">                                   
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
            $(document).on('click', '.edit-time_slot-btn', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#time-slot-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the time_slot ID from the data attribute
                var time_slotId = $(this).data('id');
                
                //make an AJAX request to fetch the time_slot data
                $.ajax({
                   url: '/timeslot/'+ time_slotId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#time_slot').val(data.time_slot);
                    $('#cutoff').val(data.cutoff);
                    $('#time_start').val(data.time_start);
                    $('#time_end').val(data.time_end);
                    $("#ref").val(data.ref);
                    $("#ref1").val(data.ref1);

                    // Adjust the form action, method, and submit button text for update
                    $('#time_slotForm').attr('action', '{{ url('timeslot/update') }}'+'/'+ time_slotId);
                    $('#time_slotForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#time_slotForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Time slots');
                   },
                   error: function(error){
                    console.error('Error:', error);
                   }
                });
            });
        });
    </script>
@endpush

