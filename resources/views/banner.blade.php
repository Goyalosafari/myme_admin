@extends('Layouts.master')

@section('title','Banner-Food Delivery')

@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <button class="btn btn-primary round" data-bs-toggle="modal" data-bs-target="#bannerModal" id="createBannerBtn">
                        Create Banner
                    </button>
                </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Banner</li>
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
            <div class="card-header">Banner List</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Display Location</th>
                                <th>Image</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bannerData as $i => $data)
                            @php
                                $locations = [
                                    'home'  => 'Home Page Slider',
                                    'ad_1'  => 'Food Page Slider 1',
                                    'ad_2'  => 'Food Page Slider 2',
                                    'ad_3'  => 'Grocery Page Slider 1',
                                    'ad_4'  => 'Grocery Page Slider 2',
                                ];
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $data->title }}</td>
                                <td>{{ $locations[$data->display_location] ?? $data->display_location }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $data->image) }}" alt="{{ $data->title }}" width="70" style="border-radius:4px;">
                                </td>
                                <td>{{ $data->category?->title ?? '—' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-banner-btn"
                                        data-id="{{ $data->id }}"
                                        data-title="{{ $data->title }}"
                                        data-category="{{ $data->category_id }}"
                                        data-location="{{ $data->display_location }}"
                                        data-image="{{ asset('storage/' . $data->image) }}"
                                        data-bs-toggle="modal" data-bs-target="#bannerModal" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                    <form style="display:inline;" action="{{ route('banner.destroy', $data->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this banner?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete"><i data-feather="trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">No banners found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                <div class="mt-3">{{ $bannerData->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="bannerForm" method="POST" action="{{ route('banner.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Create Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="modal_title" class="form-control" placeholder="Banner title">
                            @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Display Location <span class="text-danger">*</span></label>
                            <select name="display_location" id="modal_location" class="form-select">
                                <option value="">Select location</option>
                                <option value="home">Home Page Slider</option>
                                <option value="ad_1">Food Page Slider 1</option>
                                <option value="ad_2">Food Page Slider 2</option>
                                <option value="ad_3">Grocery Page Slider 1</option>
                                <option value="ad_4">Grocery Page Slider 2</option>
                            </select>
                            @error('display_location')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="modal_category" class="form-select">
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" id="modal_image" class="form-control" accept="image/*">
                            @error('image')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-12" id="previewWrap" style="display:none;">
                            <img id="imagePreview" src="" alt="Preview" style="max-height:150px; border-radius:6px; border:1px solid #dee2e6;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(function () {
    // ----- JS Validation -----
    function clearBannerErrors() {
        $('#bannerForm .is-invalid').removeClass('is-invalid');
        $('#bannerForm .js-error').remove();
    }
    function showBannerError(field, msg) {
        $(field).addClass('is-invalid');
        if (!$(field).next('.js-error').length) {
            $(field).after('<span class="js-error text-danger small">' + msg + '</span>');
        }
    }
    $('#bannerForm').on('submit', function (e) {
        clearBannerErrors();
        var ok = true;
        var title    = $.trim($('#modal_title').val());
        var location = $('#modal_location').val();
        var isCreate = ($('#formMethod').val() === 'POST');
        var hasImage = ($('#modal_image')[0].files.length > 0);

        if (!title) {
            showBannerError('#modal_title', 'Title is required.');
            ok = false;
        }
        if (!location) {
            showBannerError('#modal_location', 'Please select a display location.');
            ok = false;
        }
        if (isCreate && !hasImage) {
            showBannerError('#modal_image', 'Please select an image.');
            ok = false;
        }
        if (!ok) e.preventDefault();
    });

    // Reset modal on Create
    $('#createBannerBtn').on('click', function () {
        clearBannerErrors();
        $('#bannerModalLabel').text('Create Banner');
        $('#bannerForm')[0].reset();
        $('#bannerForm').attr('action', '{{ route('banner.store') }}');
        $('#formMethod').val('POST');
        $('#imagePreview').attr('src', '');
        $('#previewWrap').hide();
        $('#modalSubmitBtn').text('Save Banner');
    });

    // Fill modal on Edit (clear errors first)
    $(document).on('click', '.edit-banner-btn', function () {
        clearBannerErrors();
        var id       = $(this).data('id');
        var title    = $(this).data('title');
        var category = $(this).data('category');
        var location = $(this).data('location');
        var image    = $(this).data('image');

        $('#bannerModalLabel').text('Edit Banner');
        $('#modal_title').val(title);
        $('#modal_category').val(category);
        $('#modal_location').val(location);
        $('#modal_image').val('');
        $('#imagePreview').attr('src', image);
        $('#previewWrap').show();

        var updateUrl = '{{ route('banner.update', '__ID__') }}'.replace('__ID__', id);
        $('#bannerForm').attr('action', updateUrl);
        $('#formMethod').val('PUT');
        $('#modalSubmitBtn').text('Update Banner');
    });

    // Image preview
    $('#modal_image').on('change', function () {
        var file = this.files[0];
        if (file) {
            $('#imagePreview').attr('src', URL.createObjectURL(file));
            $('#previewWrap').show();
        }
    });

    // Re-init feather icons after modal opens
    $('#bannerModal').on('shown.bs.modal', function () {
        if (typeof feather !== 'undefined') feather.replace();
    });
});
</script>
@endpush
