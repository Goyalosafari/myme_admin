@extends('Layouts.master')

@section('title','Category-Food Delivery')

@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    <button class="btn btn-primary round" data-bs-toggle="modal" data-bs-target="#categoryModal" id="createCategoryBtn">
                        Create Category
                    </button>
                </h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Food Category</li>
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
            <div class="card-header">Category List</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Reference</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryData as $i => $data)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $data->title }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $data->image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $data->image) }}" alt="{{ $data->title }}" width="70" style="border-radius:4px;">
                                    </a>
                                </td>
                                <td>{{ $data->ref }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-category-btn"
                                        data-id="{{ $data->id }}"
                                        data-title="{{ $data->title }}"
                                        data-image="{{ asset('storage/' . $data->image) }}"
                                        data-bs-toggle="modal" data-bs-target="#categoryModal" title="Edit">
                                        <i data-feather="edit"></i>
                                    </button>
                                    <a href="{{ route('category.download', $data->id) }}" class="btn btn-sm btn-success" title="Download">
                                        <i data-feather="download"></i>
                                    </a>
                                    <form style="display:inline;" action="{{ route('category.destroy', $data->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete"><i data-feather="trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                <div class="mt-3">{{ $categoryData->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm" method="POST" action="{{ route('category.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="modal_title" class="form-control" placeholder="Category title">
                            @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-12">
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
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Category</button>
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
    function clearCatErrors() {
        $('#categoryForm .is-invalid').removeClass('is-invalid');
        $('#categoryForm .js-error').remove();
    }
    function showCatError(field, msg) {
        $(field).addClass('is-invalid');
        if (!$(field).next('.js-error').length) {
            $(field).after('<span class="js-error text-danger small">' + msg + '</span>');
        }
    }
    $('#categoryForm').on('submit', function (e) {
        clearCatErrors();
        var ok = true;
        var title    = $.trim($('#modal_title').val());
        var isCreate = ($('#formMethod').val() === 'POST');
        var hasImage = ($('#modal_image')[0].files.length > 0);

        if (!title) {
            showCatError('#modal_title', 'Title is required.');
            ok = false;
        }
        if (isCreate && !hasImage) {
            showCatError('#modal_image', 'Please select an image.');
            ok = false;
        }
        if (!ok) e.preventDefault();
    });

    // Reset modal on Create
    $('#createCategoryBtn').on('click', function () {
        clearCatErrors();
        $('#categoryModalLabel').text('Create Category');
        $('#categoryForm')[0].reset();
        $('#categoryForm').attr('action', '{{ route('category.store') }}');
        $('#formMethod').val('POST');
        $('#imagePreview').attr('src', '');
        $('#previewWrap').hide();
        $('#modalSubmitBtn').text('Save Category');
    });

    // Fill modal on Edit (clear errors first)
    $(document).on('click', '.edit-category-btn', function () {
        clearCatErrors();
        var id    = $(this).data('id');
        var title = $(this).data('title');
        var image = $(this).data('image');

        $('#categoryModalLabel').text('Edit Category');
        $('#modal_title').val(title);
        $('#modal_image').val('');
        $('#imagePreview').attr('src', image);
        $('#previewWrap').show();

        var updateUrl = '{{ route('category.update', '__ID__') }}'.replace('__ID__', id);
        $('#categoryForm').attr('action', updateUrl);
        $('#formMethod').val('PUT');
        $('#modalSubmitBtn').text('Update Category');
    });

    // Image preview
    $('#modal_image').on('change', function () {
        var file = this.files[0];
        if (file) {
            $('#imagePreview').attr('src', URL.createObjectURL(file));
            $('#previewWrap').show();
        }
    });
});
</script>
@endpush
