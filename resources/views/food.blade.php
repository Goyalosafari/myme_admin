@extends('Layouts.master')

@section('title','Food-Food Delivery')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .truncate {
        display: inline-block;
        max-width: 130px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    .modal-body { max-height: 70vh; overflow-y: auto; }
</style>
@endpush

@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <button class="btn btn-primary round" data-bs-toggle="modal" data-bs-target="#foodModal" id="createFoodBtn">
                        Create Food
                    </button>
                </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Food</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">Food List</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Offer Price</th>
                                <th>GST</th>
                                <th>Category</th>
                                <th>Offer 1</th>
                                <th>Offer 2</th>
                                <th>Veg</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($foodData as $i => $data)
                            <tr>
                                <td>{{ $foodData->firstItem() + $i }}</td>
                                <td>
                                    <span class="truncate" title="{{ $data->title }}">{{ $data->title }}</span>
                                </td>
                                <td>₹{{ $data->price }}</td>
                                <td>₹{{ $data->offer_price }}</td>
                                <td>{{ $data->gst }}%</td>
                                <td>
                                    <span class="truncate">{{ implode(', ', $data->categories->pluck('title')->toArray()) }}</span>
                                </td>
                                <td>{{ $data->ref }}</td>
                                <td>{{ $data->offer }}</td>
                                <td>
                                    @if($data->veg === 'yes')
                                        <span class="badge bg-success">Veg</span>
                                    @else
                                        <span class="badge bg-danger">Non-Veg</span>
                                    @endif
                                </td>
                                <td>
                                    <img src="{{ asset('storage/' . $data->image) }}" alt="{{ $data->title }}" width="60" style="border-radius:4px;">
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-sm btn-primary edit-food-btn" data-id="{{ $data->id }}"
                                        data-bs-toggle="modal" data-bs-target="#foodModal" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                    <form style="display:inline;" action="{{ url('/food/delete/' . $data->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this food item?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete"><i data-feather="trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="11" class="text-center text-muted">No food items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $foodData->links() }}</div>
            </div>
        </div>
    </section>
</div>

<!-- Food Modal -->
<div class="modal fade" id="foodModal" tabindex="-1" aria-labelledby="foodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="foodForm" method="POST" action="{{ route('food.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="foodModalLabel">Create Food</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="m_title" class="form-control" placeholder="Food name">
                            @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Calorie</label>
                            <input type="text" name="calorie" id="m_calorie" class="form-control" placeholder="e.g. 250 kcal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cooking Time</label>
                            <input type="text" name="cooking_time" id="m_cooking_time" class="form-control" placeholder="e.g. 30 mins">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Taste <span class="text-danger">*</span></label>
                            <input type="text" name="taste" id="m_taste" class="form-control" placeholder="e.g. Spicy">
                            @error('taste')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="m_price" class="form-control" placeholder="0.00">
                            @error('price')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Offer Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="offer_price" id="m_offer_price" class="form-control" placeholder="0.00">
                            @error('offer_price')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MRP <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="mrp" id="m_mrp" class="form-control" placeholder="0.00">
                            @error('mrp')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Margin</label>
                            <input type="text" name="margin" id="m_margin" class="form-control" placeholder="Margin">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GST (%)</label>
                            <input type="number" step="0.01" name="gst" id="m_gst" class="form-control" placeholder="e.g. 18">
                            @error('gst')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Veg <span class="text-danger">*</span></label>
                            <select name="veg" id="m_veg" class="form-select">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            @error('veg')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferences</label>
                            <input type="text" name="preferences" id="m_preferences" class="form-control" placeholder="Preferences">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meal Type <span class="text-danger">*</span></label>
                            <input type="text" name="meal_type" id="m_meal_type" class="form-control" placeholder="e.g. Breakfast">
                            @error('meal_type')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Food Details <span class="text-danger">*</span></label>
                            <input type="text" name="food_details" id="m_food_details" class="form-control" placeholder="Short description">
                            @error('food_details')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control select2-multiple" name="categories[]" id="m_categories" multiple>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                            @error('categories')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Offer 1</label>
                            <select name="ref" id="m_ref" class="form-select">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Offer 2</label>
                            <select name="offer" id="m_offer" class="form-select">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" id="m_image" class="form-control" accept="image/*">
                            @error('image')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6" id="previewWrap" style="display:none;">
                            <label class="form-label">Preview</label><br>
                            <img id="imagePreview" src="" alt="Preview" style="max-height:120px; border-radius:6px; border:1px solid #dee2e6;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Food</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    // ----- JS Validation -----
    function clearFoodErrors() {
        $('#foodForm .is-invalid').removeClass('is-invalid');
        $('#foodForm .js-error').remove();
        $('#m_categories').closest('.col-md-6').find('.js-error').remove();
    }
    function showFoodError(field, msg) {
        $(field).addClass('is-invalid');
        if (!$(field).next('.js-error').length) {
            $(field).after('<span class="js-error text-danger small">' + msg + '</span>');
        }
    }
    $('#foodForm').on('submit', function (e) {
        clearFoodErrors();
        var ok = true;
        var title       = $.trim($('#m_title').val());
        var taste       = $.trim($('#m_taste').val());
        var price       = $.trim($('#m_price').val());
        var offerPrice  = $.trim($('#m_offer_price').val());
        var mrp         = $.trim($('#m_mrp').val());
        var gst         = $.trim($('#m_gst').val());
        var veg         = $('#m_veg').val();
        var mealType    = $.trim($('#m_meal_type').val());
        var foodDetails = $.trim($('#m_food_details').val());
        var cats        = $('#m_categories').val();
        var isCreate    = ($('#formMethod').val() === 'POST');
        var hasImage    = ($('#m_image')[0].files.length > 0);

        if (!title) { showFoodError('#m_title', 'Food name is required.'); ok = false; }

        if (!taste) { showFoodError('#m_taste', 'Taste is required.'); ok = false; }

        if (price === '' || isNaN(price) || parseFloat(price) < 0) {
            showFoodError('#m_price', 'Price is required and must be 0 or greater.');
            ok = false;
        }
        if (offerPrice === '' || isNaN(offerPrice) || parseFloat(offerPrice) < 0) {
            showFoodError('#m_offer_price', 'Offer price is required and must be 0 or greater.');
            ok = false;
        }
        if (mrp === '' || isNaN(mrp) || parseFloat(mrp) < 0) {
            showFoodError('#m_mrp', 'MRP is required and must be 0 or greater.');
            ok = false;
        }
        if (gst !== '' && (isNaN(gst) || parseFloat(gst) < 0 || parseFloat(gst) > 100)) {
            showFoodError('#m_gst', 'GST must be between 0 and 100.');
            ok = false;
        }
        if (!veg) { showFoodError('#m_veg', 'Please select veg or non-veg.'); ok = false; }

        if (!mealType) { showFoodError('#m_meal_type', 'Meal type is required.'); ok = false; }

        if (!foodDetails) { showFoodError('#m_food_details', 'Food details are required.'); ok = false; }

        if (!cats || cats.length === 0) {
            $('#m_categories').addClass('is-invalid');
            $('#m_categories').closest('.col-md-6').append('<span class="js-error text-danger small">Please select at least one category.</span>');
            ok = false;
        }
        if (isCreate && !hasImage) {
            showFoodError('#m_image', 'Please select an image.');
            ok = false;
        }
        if (!ok) e.preventDefault();
    });

    // Init select2
    function initSelect2() {
        $('#m_categories').select2({ dropdownParent: $('#foodModal'), placeholder: 'Select categories', allowClear: true });
    }
    $('#foodModal').on('shown.bs.modal', function () {
        initSelect2();
        if (typeof feather !== 'undefined') feather.replace();
    });

    // Reset on Create
    $('#createFoodBtn').on('click', function () {
        clearFoodErrors();
        $('#foodModalLabel').text('Create Food');
        $('#foodForm')[0].reset();
        $('#foodForm').attr('action', '{{ route('food.store') }}');
        $('#formMethod').val('POST');
        $('#m_categories').val(null).trigger('change');
        $('#imagePreview').attr('src', '');
        $('#previewWrap').hide();
        $('#modalSubmitBtn').text('Save Food');
    });

    // Fill modal on Edit via AJAX (clear errors first)
    $(document).on('click', '.edit-food-btn', function () {
        clearFoodErrors();
        var foodId = $(this).data('id');

        $.ajax({
            url: '{{ route('food.edit', '__ID__') }}'.replace('__ID__', foodId),
            method: 'GET',
            success: function (data) {
                var f = data.food;
                $('#foodModalLabel').text('Edit Food');
                $('#m_title').val(f.title);
                $('#m_calorie').val(f.calorie);
                $('#m_cooking_time').val(f.cooking_time);
                $('#m_taste').val(f.taste);
                $('#m_price').val(f.price);
                $('#m_offer_price').val(f.offer_price);
                $('#m_mrp').val(f.mrp);
                $('#m_margin').val(f.margin);
                $('#m_preferences').val(f.preferences);
                $('#m_meal_type').val(f.meal_type);
                $('#m_food_details').val(f.food_details);
                $('#m_gst').val(f.gst);
                $('#m_ref').val(f.ref);
                $('#m_offer').val(f.offer);
                $('#m_veg').val(f.veg);
                $('#m_image').val('');

                $('#m_categories').val(data.selectedCategories).trigger('change');

                if (f.image) {
                    $('#imagePreview').attr('src', '{{ asset('storage') }}/' + f.image);
                    $('#previewWrap').show();
                }

                var updateUrl = '{{ route('food.update', '__ID__') }}'.replace('__ID__', f.id);
                $('#foodForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');
                $('#modalSubmitBtn').text('Update Food');
            },
            error: function () {
                alert('Failed to load food data.');
            }
        });
    });

    // Image preview
    $('#m_image').on('change', function () {
        var file = this.files[0];
        if (file) {
            $('#imagePreview').attr('src', URL.createObjectURL(file));
            $('#previewWrap').show();
        }
    });
});
</script>
@endpush
