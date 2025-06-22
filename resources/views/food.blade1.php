@extends('Layouts.master')

@section('title','Food-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#food-form" class="btn btn-primary round">Create Food</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Food</li>
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
    <section class="section" id="food-data">
        <div class="card">
            <div class="card-header">Food List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>calorie</th>
                            <th>Cooking time</th>
                            <th>Taste</th>
                            <th>Price</th>
                            <th>Preferences</th>
                            <th>Meal type</th>
                            <th>Details</th>
                            <th>Category</th>
                            <th>Offer</th>
                            <th>GST</th>
                            <th>image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($foodData as $data)
                        <tr>
                            <td>{{$data->title}}</td>
                            <td>{{$data->calorie}}</td>
                            <td>{{$data->cooking_time}}</td>
                            <td>{{$data->taste}}</td>
                            <td>{{$data->price}}</td>
                            <td>{{$data->preferences }}</td>
                            <td>{{$data->meal_type }}</td>
                            <td>{{$data->food_details }}</td>
                            <td>{{($data->category != null ? $data->category->title : '')}}</td>
                            <td>{{$data->offer}}</td>
                            <td>{{$data->gst}}</td>
                            <td><img src="{{ asset('storage/' .$data->image)}}" alt="{{$data->image}}" srcset="" width="70px"></td>
                            <td>
                                <a href="#food-form" class="btn icon btn-primary edit-food-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/food/delete/'. $data->id) }}" method="POST">
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
    <!-- //Form for food -->
    <section id="food-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Food</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="foodForm" action="{{ route('food.store')}}" enctype="multipart/form-data">
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
                                            <label for="calorie">Calorie</label>
                                            <input type="text" id="calorie" class="form-control" placeholder="calorie"
                                                name="calorie">
                                                @error('calorie')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="cooking_time">Cooking Time</label>
                                            <input type="text" id="cooking_time" class="form-control" placeholder="Cooking time" name="cooking_time" value="{{old('cooking_time')}}">
                                            @error('cooking_time')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="taste">Taste</label>
                                            <input type="text" id="taste" class="form-control" placeholder="Taste" name="taste" value="{{old('taste')}}">
                                            @error('taste')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="text" id="price" class="form-control" placeholder="Price" name="price" value="{{old('price')}}">
                                            @error('price')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="preferences">Preferences</label>
                                            <input type="text" id="preferences" class="form-control" placeholder="Preferences" name="preferences" value="{{old('preferences')}}">
                                            @error('preferences')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="meal_type">Meal type</label>
                                            <input type="text" id="meal_type" class="form-control" name="meal_type" placeholder="Meal Type" value="{{old('meal_type')}}">
                                            @error('meal_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="food_details">Food details</label>
                                            <input type="text" id="food_details" class="form-control" name="food_details" placeholder="Details" value="{{old('food_details')}}">
                                            @error('food_details')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
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
                                            <label for="image">Image</label>
                                            <input type="file" id="image" class="form-control"
                                                name="image">
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ref">Offer 1</label>
                                               <select id="ref" class="form-control" name="ref">
                                                    <option value="">Select a ref</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>   
                                                @error('ref')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror                     
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="offer">Offer 2 </label>
                                               <select id="offer" class="form-control" name="offer">
                                                    <option value="">Select</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>   
                                                @error('offer')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror                     
                                        </div>
                                    </div>  
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="gst"> GST </label>
                                            <input type="text" id="gst" class="form-control" name="gst" placeholder="GST" value="{{old('gst')}}">
                                            @error('gst')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>                                     
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <img id="imagePreview" src="" alt="" srcset="" width="170px">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <input type="hidden" id="food_id" name="food_id" value="">                                   
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
            $('.edit-food-btn').on('click', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#food-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the food ID from the data attribute
                var foodId = $(this).data('id');
                
                //make an AJAX request to fetch the food data
                $.ajax({
                   url: '/food/'+ foodId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#title').val(data.title);
                    $('#calorie').val(data.calorie);
                    $('#cooking_time').val(data.cooking_time);
                    $('#taste').val(data.taste);
                    $('#price').val(data.price);
                    $('#preferences').val(data.preferences);
                    $('#meal_type').val(data.meal_type);
                    $('#food_details').val(data.food_details);
                    $('#category_id').val(data.category_id);
                    $('#ref').val(data.ref);
                    $('#offer').val(data.offer);

                    var Image = data.image;
                    var imgPath= "{{asset( 'storage/' )}}"+ '/' + Image;
                    $('#imagePreview').attr('src',imgPath);
                   
                    // Adjust the form action, method, and submit button text for update
                    $('#foodForm').attr('action', '{{ url('food/update') }}'+'/'+ foodId);
                    $('#foodForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#foodForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Food');
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
        });
    </script>
@endpush

