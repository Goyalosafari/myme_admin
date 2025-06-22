@extends('Layouts.master')

@section('title','Recipe-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#recipe-form" class="btn btn-primary round">Create Recipe</a> </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Recipe</li>
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
    <section class="section" id="recipe-data">
        <div class="card">
            <div class="card-header">Recipe List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th> Category </th>
                            <th> Food </th>
                            <th> Num of serving</th>
                            <th> Description</th>
                            <th> Ingredients</th>
                            <th> Nutritional facts</th>
                            <th> Utensils</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recipeData as $data)
                        <tr>
                            <td>{{$data->title}}</td>
                            <td> {{ $data->category->title }}</td>
                            <td> {{ $data->food->title }}</td>
                            <td> {{ $data->num_of_serving }}</td>
                            <td> {{ $data->description}} </td>
                            <td> {{ $data->ingredients }}</td>
                            <td> {{ $data->nutritional_facts }}</td>
                            <td> {{ $data->utensils }}</td>
                            <td><img src="{{ asset('storage/' .$data->image)}}" alt="{{$data->image}}" srcset="" width="70px"></td>
                            <td>
                                <a href="#recipe-form" class="btn icon btn-primary edit-recipe-btn" data-id="{{$data->id}}"><i data-feather="edit"></i></a>
                                <form id="deleteForm" style="display: inline;" action="{{ url('/recipe/delete/'. $data->id) }}" method="POST">
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
    <!-- //Form for recipe -->
    <section id="recipe-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Recipe</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="recipeForm" action="{{ route('recipe.store')}}" enctype="multipart/form-data">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <img id="imagePreview" src="" alt="" srcset="" width="170px">
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
                                            <label for="food_id"> Food</label>
                                               <select id="food_id" class="form-control" name="food_id">
                                                    <option value="">Select a food</option>
                                                    @foreach($foods as $food)
                                                    <option value="{{$food->id}}">{{$food->title}}</option>
                                                    @endforeach
                                                </select>   
                                                @error('food_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror                     
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="num_of_serving">Num of serving</label>
                                            <input type="text" id="num_of_serving" class="form-control" placeholder="Num of serving" name="num_of_serving" value="{{old('num_of_serving')}}">
                                            @error('num_of_serving')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ingredients">Ingredients</label>
                                            <input type="text" id="ingredients" class="form-control" placeholder="Ingredients" name="ingredients" value="{{old('ingredients')}}">
                                            @error('ingredients')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                <div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="nutritional_facts">Nutritional facts</label>
                                            <input type="text" id="nutritional_facts" class="form-control" placeholder="Nutritional facts" name="nutritional_facts" value="{{old('nutritional_facts')}}">
                                            @error('nutritional_facts')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="utensils">Utensils</label>
                                            <input type="text" id="utensils" class="form-control" placeholder="Utensils" name="utensils" value="{{old('utensils')}}">
                                            @error('utensils')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="description"> Description</label>
                                            <input type="text" id="description" class="form-control" placeholder="Description" name="description" value="{{old('description')}}">
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> 
                                <div>
                                <div class="row">
                                    <div class="col-md-6 col-12"></div>
                                    <input type="hidden" id="recipe_id" name="recipe_id" value="">                                   
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
            $('.edit-recipe-btn').on('click', function (e) {
                e.preventDefault();

                //Get the offset top of the form section 
                var formSectionOffset = $('#recipe-form').offset().top;

                //Animate the scrolling to the top section
                $("html, body").animate({
                    scrollTop: formSectionOffset
                }, 800);

                // Get the recipe ID from the data attribute
                var recipeId = $(this).data('id');
                
                //make an AJAX request to fetch the recipe data
                $.ajax({
                   url: '/recipe/'+ recipeId + '/edit',
                   method : 'GET',
                   success: function(data){
                    // Populate the form fields with the fetched data
                    $('#title').val(data.title);
                    $('#category_id').val(data.category_id);
                    $('#food_id').val(data.food_id);
                    $('#num_of_serving').val(data.num_of_serving);
                    $('#description').val(data.description);
                    $('#ingredients').val(data.ingredients);
                    $('#nutritional_facts').val(data.nutritional_facts);
                    $('#utensils').val(data.utensils);

                    var Image = data.image;
                    var imgPath= "{{asset( 'storage/' )}}"+ '/' + Image;
                    $('#imagePreview').attr('src',imgPath);

                    // Adjust the form action, method, and submit button text for update
                    $('#recipeForm').attr('action', '{{ url('recipe/update') }}'+'/'+ recipeId);
                    $('#recipeForm').append('<input type="hidden" name="_method" value="PUT">'); // Add the _method field
                    $('#recipeForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">'); // Add the CSRF token field
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Recipe');
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

