@extends('Layouts.master')

@section('title','Pincode-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendors/simple-datatables/style.css') }}">
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#pincode-form" class="btn btn-primary round">Create Pincode</a></h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pincode</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="position:absolute;top:20px;right:20px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="section" id="pincode-data">
        <div class="card">
            <div class="card-header">Pincode List</div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Pincode</th>
                            <th>Place Name</th>
                            <th>District</th>
                            <th>State</th>
                            <th>Delivery Fee</th>
                            <th>Other Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pincodeData as $data)
                        <tr>
                            <td>{{ $data->pincode }}</td>
                            <td>{{ $data->place_name }}</td>
                            <td>{{ $data->district }}</td>
                            <td>{{ $data->state }}</td>
                            <td>{{ $data->delivery_fee }}</td>
                            <td>{{ $data->other_fee }}</td>
                            <td>
                                <a href="#pincode-form" class="btn icon btn-primary edit-pincode-btn" data-id="{{ $data->id }}">
                                    <i data-feather="edit"></i>
                                </a>
                                <form style="display:inline;" action="{{ route('pincode.destroy', $data->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn icon btn-danger">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $pincodeData->links() }}</div>
            </div>
        </div>
    </section>

    <section id="pincode-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="formTitle">Create Pincode</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="pincodeForm" action="{{ route('pincode.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="formMethod" value="POST">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="pincode">Pincode <span class="text-danger">*</span></label>
                                            <input type="text" id="pincode" class="form-control" placeholder="e.g. 682001"
                                                name="pincode" value="{{ old('pincode') }}">
                                            @error('pincode')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="place_name">Place Name <span class="text-danger">*</span></label>
                                            <input type="text" id="place_name" class="form-control" placeholder="Place name"
                                                name="place_name" value="{{ old('place_name') }}">
                                            @error('place_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="district">District <span class="text-danger">*</span></label>
                                            <input type="text" id="district" class="form-control" placeholder="District"
                                                name="district" value="{{ old('district') }}">
                                            @error('district')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="state">State <span class="text-danger">*</span></label>
                                            <input type="text" id="state" class="form-control" placeholder="State"
                                                name="state" value="{{ old('state') }}">
                                            @error('state')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="delivery_fee">Delivery Fee <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="delivery_fee" class="form-control" placeholder="0.00"
                                                name="delivery_fee" value="{{ old('delivery_fee') }}" min="0">
                                            @error('delivery_fee')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="other_fee">Other Fee <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="other_fee" class="form-control" placeholder="0.00"
                                                name="other_fee" value="{{ old('other_fee') }}" min="0">
                                            @error('other_fee')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" id="submitBtn" class="btn btn-primary me-1 mb-1">Submit</button>
                                        <button type="button" id="resetBtn" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendors/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('js/vendors.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(function () {

    function clearErrors() {
        $('#pincodeForm .is-invalid').removeClass('is-invalid');
        $('#pincodeForm .js-error').remove();
    }

    function showError(field, msg) {
        $(field).addClass('is-invalid');
        if (!$(field).next('.js-error').length) {
            $(field).after('<span class="js-error text-danger small">' + msg + '</span>');
        }
    }

    $('#pincodeForm').on('submit', function (e) {
        clearErrors();
        var ok = true;

        var pincode     = $.trim($('#pincode').val());
        var placeName   = $.trim($('#place_name').val());
        var district    = $.trim($('#district').val());
        var state       = $.trim($('#state').val());
        var deliveryFee = $.trim($('#delivery_fee').val());
        var otherFee    = $.trim($('#other_fee').val());

        if (!pincode) { showError('#pincode', 'Pincode is required.'); ok = false; }

        if (!placeName) { showError('#place_name', 'Place name is required.'); ok = false; }

        if (!district) { showError('#district', 'District is required.'); ok = false; }

        if (!state) { showError('#state', 'State is required.'); ok = false; }

        if (deliveryFee === '') {
            showError('#delivery_fee', 'Delivery fee is required.');
            ok = false;
        } else if (isNaN(deliveryFee) || parseFloat(deliveryFee) < 0) {
            showError('#delivery_fee', 'Delivery fee must be a valid number (0 or greater).');
            ok = false;
        }

        if (otherFee === '') {
            showError('#other_fee', 'Other fee is required.');
            ok = false;
        } else if (isNaN(otherFee) || parseFloat(otherFee) < 0) {
            showError('#other_fee', 'Other fee must be a valid number (0 or greater).');
            ok = false;
        }

        if (!ok) e.preventDefault();
    });

    $('#resetBtn').on('click', function () {
        clearErrors();
        $('#pincodeForm')[0].reset();
        $('#pincodeForm').attr('action', '{{ route('pincode.store') }}');
        $('#formMethod').val('POST');
        $('#submitBtn').text('Submit');
        $('#formTitle').text('Create Pincode');
    });

    $(document).on('click', '.edit-pincode-btn', function (e) {
        e.preventDefault();
        clearErrors();

        $('html, body').animate({ scrollTop: $('#pincode-form').offset().top }, 800);

        var pincodeId = $(this).data('id');

        $.ajax({
            url: '{{ route('pincode.edit', '__ID__') }}'.replace('__ID__', pincodeId),
            method: 'GET',
            success: function (data) {
                $('#pincode').val(data.pincode);
                $('#place_name').val(data.place_name);
                $('#district').val(data.district);
                $('#state').val(data.state);
                $('#delivery_fee').val(data.delivery_fee);
                $('#other_fee').val(data.other_fee);

                var updateUrl = '{{ route('pincode.update', '__ID__') }}'.replace('__ID__', pincodeId);
                $('#pincodeForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');
                $('#submitBtn').text('Update');
                $('#formTitle').text('Update Pincode');
            },
            error: function () {
                alert('Failed to load pincode data.');
            }
        });
    });
});
</script>
@endpush
