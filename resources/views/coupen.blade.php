@extends('Layouts.master')

@section('title','Coupon-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#coupen-form" class="btn btn-primary round">Create Coupon</a></h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Coupon</li>
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

    <section class="section" id="coupen-data">
        <div class="card">
            <div class="card-header">Coupon List</div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Coupon Code</th>
                            <th>No of Usage</th>
                            <th>Discount Type</th>
                            <th>Discount</th>
                            <th>Max Discount</th>
                            <th>Min Amount</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupenData as $data)
                        <tr>
                            <td>{{ $data->title }}</td>
                            <td>{{ $data->coupen_code }}</td>
                            <td>{{ $data->no_of_usage }}</td>
                            <td>{{ $data->discount_type }}</td>
                            <td>{{ $data->discount }}</td>
                            <td>{{ $data->max_discount }}</td>
                            <td>{{ $data->min_amount }}</td>
                            <td>{{ $data->from_date }}</td>
                            <td>{{ $data->to_date }}</td>
                            <td>
                                <a href="#coupen-form" class="btn icon btn-primary edit-coupen-btn" data-id="{{ $data->id }}">
                                    <i data-feather="edit"></i>
                                </a>
                                <form style="display:inline;" action="{{ route('coupen.destroy', $data->id) }}" method="POST"
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
                <div class="mt-3">{{ $coupenData->links() }}</div>
            </div>
        </div>
    </section>

    <section id="coupen-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="formTitle">Create Coupon</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="coupenForm" action="{{ route('coupen.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="formMethod" value="POST">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" class="form-control" placeholder="Title"
                                                name="title" value="{{ old('title') }}">
                                            @error('title')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="coupen_code">Coupon Code <span class="text-danger">*</span></label>
                                            <input type="text" id="coupen_code" class="form-control" placeholder="Coupon code"
                                                name="coupen_code" value="{{ old('coupen_code') }}">
                                            @error('coupen_code')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="no_of_usage">Number of Usage <span class="text-danger">*</span></label>
                                            <input type="number" id="no_of_usage" class="form-control" placeholder="Number of usage"
                                                name="no_of_usage" value="{{ old('no_of_usage') }}" min="1">
                                            @error('no_of_usage')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="discount_type">Discount Type <span class="text-danger">*</span></label>
                                            <select id="discount_type" class="form-control" name="discount_type">
                                                <option value="">Select Discount Type</option>
                                                <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                            </select>
                                            @error('discount_type')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="discount">Discount Value <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="discount" class="form-control" placeholder="Discount"
                                                name="discount" value="{{ old('discount') }}" min="0">
                                            @error('discount')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="max_discount">Maximum Discount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="max_discount" class="form-control" placeholder="Maximum Discount"
                                                name="max_discount" value="{{ old('max_discount') }}" min="0">
                                            @error('max_discount')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="min_amount">Minimum Amount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="min_amount" class="form-control" placeholder="Minimum amount"
                                                name="min_amount" value="{{ old('min_amount') }}" min="0">
                                            @error('min_amount')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="from_date">From Date <span class="text-danger">*</span></label>
                                            <input type="date" id="from_date" class="form-control" name="from_date"
                                                value="{{ old('from_date') }}">
                                            @error('from_date')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="to_date">To Date <span class="text-danger">*</span></label>
                                            <input type="date" id="to_date" class="form-control" name="to_date"
                                                value="{{ old('to_date') }}">
                                            @error('to_date')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" id="submitBtn" class="btn btn-primary me-1 mb-1">Submit</button>
                                        <button type="button" class="btn btn-light-secondary me-1 mb-1" id="resetBtn">Reset</button>
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

    // ----- JS Validation -----
    function clearErrors() {
        $('#coupenForm .is-invalid').removeClass('is-invalid');
        $('#coupenForm .js-error').remove();
    }

    function showError(field, msg) {
        $(field).addClass('is-invalid');
        if (!$(field).next('.js-error').length) {
            $(field).after('<span class="js-error text-danger small">' + msg + '</span>');
        }
    }

    $('#coupenForm').on('submit', function (e) {
        clearErrors();
        var ok = true;

        var title        = $.trim($('#title').val());
        var code         = $.trim($('#coupen_code').val());
        var usage        = $.trim($('#no_of_usage').val());
        var dtype        = $('#discount_type').val();
        var discount     = $.trim($('#discount').val());
        var maxDiscount  = $.trim($('#max_discount').val());
        var minAmount    = $.trim($('#min_amount').val());
        var fromDate     = $('#from_date').val();
        var toDate       = $('#to_date').val();

        if (!title) { showError('#title', 'Coupon title is required.'); ok = false; }
        if (!code)  { showError('#coupen_code', 'Coupon code is required.'); ok = false; }

        if (!usage) {
            showError('#no_of_usage', 'Number of usages is required.');
            ok = false;
        } else if (isNaN(usage) || parseInt(usage) < 1 || !Number.isInteger(parseFloat(usage))) {
            showError('#no_of_usage', 'Number of usages must be a whole number of at least 1.');
            ok = false;
        }

        if (!dtype) { showError('#discount_type', 'Please select a discount type.'); ok = false; }

        if (discount === '') {
            showError('#discount', 'Discount value is required.');
            ok = false;
        } else if (isNaN(discount) || parseFloat(discount) < 0) {
            showError('#discount', 'Discount must be a valid number (0 or greater).');
            ok = false;
        }

        if (maxDiscount === '') {
            showError('#max_discount', 'Maximum discount is required.');
            ok = false;
        } else if (isNaN(maxDiscount) || parseFloat(maxDiscount) < 0) {
            showError('#max_discount', 'Max discount must be a valid number (0 or greater).');
            ok = false;
        }

        if (minAmount === '') {
            showError('#min_amount', 'Minimum amount is required.');
            ok = false;
        } else if (isNaN(minAmount) || parseFloat(minAmount) < 0) {
            showError('#min_amount', 'Minimum amount must be a valid number (0 or greater).');
            ok = false;
        }

        if (!fromDate) { showError('#from_date', 'From date is required.'); ok = false; }
        if (!toDate)   { showError('#to_date', 'To date is required.'); ok = false; }

        if (fromDate && toDate && toDate < fromDate) {
            showError('#to_date', 'To date must be on or after the from date.');
            ok = false;
        }

        if (!ok) e.preventDefault();
    });

    // ----- Reset button -----
    $('#resetBtn').on('click', function () {
        clearErrors();
        $('#coupenForm')[0].reset();
        $('#coupenForm').attr('action', '{{ route('coupen.store') }}');
        $('#formMethod').val('POST');
        $('#submitBtn').text('Submit');
        $('#formTitle').text('Create Coupon');
    });

    // ----- Edit button -----
    $(document).on('click', '.edit-coupen-btn', function (e) {
        e.preventDefault();
        clearErrors();

        $('html, body').animate({ scrollTop: $('#coupen-form').offset().top }, 800);

        var coupenId = $(this).data('id');

        $.ajax({
            url: '{{ route('coupen.edit', '__ID__') }}'.replace('__ID__', coupenId),
            method: 'GET',
            success: function (data) {
                $('#title').val(data.title);
                $('#coupen_code').val(data.coupen_code);
                $('#no_of_usage').val(data.no_of_usage);
                $('#discount_type').val(data.discount_type);
                $('#discount').val(data.discount);
                $('#max_discount').val(data.max_discount);
                $('#min_amount').val(data.min_amount);
                $('#from_date').val(data.from_date);
                $('#to_date').val(data.to_date);

                var updateUrl = '{{ route('coupen.update', '__ID__') }}'.replace('__ID__', coupenId);
                $('#coupenForm').attr('action', updateUrl);
                $('#formMethod').val('PUT');
                $('#submitBtn').text('Update');
                $('#formTitle').text('Update Coupon');
            },
            error: function () {
                alert('Failed to load coupon data.');
            }
        });
    });
});
</script>
@endpush
