@extends('Layouts.master')

@section('title','Notifications-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a class="btn btn-primary round create-btn">Create Notifications</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
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
    <section class="section" id="data">
        <div class="card">
            <div class="card-header">Notifications List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Message</th>
                            <th>Time stamp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->message}}</td>
                            <td>{{$data->created_at}}</td>
                            <td>
                                <a class="btn icon btn-primary edit-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/notification/delete/'. $data->id) }}" method="POST">
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
    <section id="notification-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Notification</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="notificationForm" action="{{ route('notification.store')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="message">Message</label>
                                            <input type="text" id="message" class="form-control" placeholder="message"
                                                name="message" value="{{old('message')}}">
                                                @error('message')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <input type="hidden" id="notification_id" name="notification_id" value="">                                   
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
            $(document).on('click', '.edit-btn', function (event) {
                event.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#notification-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the notification ID from the data attribute
                var notificationId = $(this).data('id');
                
                $.ajax({
                   url: '/notification/'+ notificationId + '/edit',
                   method : 'GET',
                   success: function(data){
                    $('#message').val(data.message);
                  
                    $('#notificationForm').attr('action', '{{ url('notification/update') }}'+'/'+ notificationId);
                    $('#notificationForm').append('<input type="hidden" name="_method" value="PUT">'); 
                    $('#notificationForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); 
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Notification');
                   },
                   error: function(error){
                    console.error('Error:', error);
                   }
                });
            });

            $('.create-btn').on('click', function(e){
                e.preventDefault();

                var formSectionOffset = $('#notification-form').offset().top;
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);
            });
        });
    </script>
@endpush

