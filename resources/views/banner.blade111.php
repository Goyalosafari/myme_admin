@extends('Layouts.master')

@section('title','Banner-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a class="btn btn-primary round create-banner-btn">Create Banner</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Banner</li>
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
    <section class="section" id="banner-data">
        <div class="card">
            <div class="card-header">Banner List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Display location</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bannerData as $data)
                        <tr>
                            <td>{{$data->title}}</td>
                            <td>{{$data->display_location}}</td>
                            <td><img src="{{ asset('storage/' .$data->image)}}" alt="{{$data->image}}" srcset="" width="70px"></td>
                            <td>{{($data->category != null ? $data->category->title : '')}}</td>
                            <td>
                                <a class="btn icon btn-primary edit-banner-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/banner/delete/'. $data->id) }}" method="POST">
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
    <!-- //Form for banner -->
    <section id="banner-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Banner</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="bannerForm" action="{{ route('banner.store')}}" enctype="multipart/form-data">
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
                                            <label for="image">Image</label>
                                            <input type="file" id="image" class="form-control"
                                                name="image">
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="category_id">Category</label>
                                            <select id="category_id" class="form-control" name="category_id">
                                                <option value="">Select a category</option>
                                                @foreach($categories as $cat)
                                                <option value="{{$cat->id}}">{{$cat->title}}</option>
                                                @endforeach
                                            </select>                                              
                                            @error('category_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                    <div class="form-group">
                                            <label for="display_location">Display Location</label>
                                            <select id="display_location" class="form-control" name="display_location">
                                                    <option value="">Select a Location</option>
                                                    <option value="home">Home Page Slider</option>
                                                    <option value="page_1">Category Section 1</option>
                                                     <option value="ad_1">Advertisment 1</option>
                                                    <option value="page_2">Category Section 2</option>
                                                     <option value="ad_1">Advertisment 2</option>
                                                </select>   
                                                @error('display_location')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror                     
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <img id="imagePreview" src="" alt="" srcset="" width="170px">
                                        </div>
                                    </div>
                                    <input type="hidden" id="banner_id" name="banner_id" value="">                                   
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
            $('.edit-banner-btn').on('click', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#banner-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the banner ID from the data attribute
                var bannerId = $(this).data('id');
                
                //make an AJAX request to fetch the banner data
                $.ajax({
                   url: '/banner/'+ bannerId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#title').val(data.title);
                    $('#category_id').val(data.category_id);
                    $('#display_location').val(data.display_location);

                    var Image = data.image;
                    var imgPath= "{{asset( 'storage/' )}}"+ '/' + Image;
                    $('#imagePreview').attr('src',imgPath);

                    // Adjust the form action, method, and submit button text for update
                    $('#bannerForm').attr('action', '{{ url('banner/update') }}'+'/'+ bannerId);
                    $('#bannerForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#bannerForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Banner');
                   },
                   error: function(error){
                    console.error('Error:', error);
                   }
                });
            });

            // Handle change event of the file input
            $('#image').change(function() {
                $('#imagePreview').attr('src', '');
                // Get the selected file
                var file = this.files[0];
                if (file) {
                    // Create a URL for the selected file
                    var imageUrl = URL.createObjectURL(file);

                    // Update the src attribute of the image preview
                    $('#imagePreview').attr('src', imageUrl);
                } else {
                    // Clear the src attribute if no file is selected 
                    $('#imagePreview').attr('src', '');
                }
            });

            $('.create-banner-btn').on('click', function(e){
                e.preventDefault();

                var formSectionOffset = $('#banner-form').offset().top;
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);
            });
        });
    </script>
@endpush

