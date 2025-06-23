@extends('Layouts.master')

@section('title', 'Time slots-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}">
<!-- Include Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .status-text {
        margin-left: 10px;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><a href="#time-slot-form" class="btn btn-primary round">Create Time slots</a></h3>
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
            <div class="card-header">Time slots List</div>
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
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td>{{ $data->time_slot }}</td>
                            <td>{{ $data->cutoff }}</td>
                            <td>{{ $data->time_start }}</td>
                            <td>{{ $data->time_end }}</td>
                            <td>{{ $data->ref }}</td>
                            <td>{{ $data->ref1 }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-status" data-id="{{ $data->id }}" {{ $data->status === 'y' ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <span class="status-text">{{ $data->status === 'y' ? 'Yes' : 'No' }}</span>
                            </td>
                            <td>
                                <a href="#time-slot-form" class="btn icon btn-primary edit-time_slot-btn" data-id="{{ $data->id }}"><i data-feather="edit"></i></a>
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
    <!-- Form for time slot -->
    <section id="time-slot-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create Time slots</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" method="POST" id="time_slotForm" action="{{ route('timeslot.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_slot">Time slot</label>
                                            <input type="text" id="time_slot" class="form-control" placeholder="Time slot" name="time_slot" value="{{ old('time_slot') }}">
                                            @error('time_slot')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="cutoff">Cutoff</label>
                                            <input type="text" id="cutoff" class="form-control" placeholder="Cutoff" name="cutoff">
                                            @error('cutoff')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_start">Time start</label>
                                            <input type="time" id="time_start" class="form-control" placeholder="District" name="time_start" value="{{ old('time_start') }}">
                                            @error('time_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_end">Time end</label>
                                            <input type="time" id="time_end" class="form-control" placeholder="Time end" name="time_end" value="{{ old('time_end') }}">
                                            @error('time_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ref">Ref</label>
                                            <input type="text" id="ref" class="form-control" placeholder="Ref" name="ref" value="{{ old('ref') }}">
                                            @error('ref')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="ref1">Cutoff Key (compulsory)</label>
                                            <input type="text" id="ref1" class="form-control" placeholder="Ref1" name="ref1" value="{{ old('ref1') }}">
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
</div>
@endsection

@push('scripts')
<script src="{{asset('vendors/simple-datatables/simple-datatables.js')}}"></script>
<script src="{{asset('js/vendors.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Include Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $(document).on('click', '.edit-time_slot-btn', function (e) {
            e.preventDefault();

            var formSectionOffset = $('#time-slot-form').offset().top;
            $("html, body").animate({
                scrollTop: formSectionOffset
            }, 800);

            var time_slotId = $(this).data('id');
            
            $.ajax({
                url: '/timeslot/' + time_slotId + '/edit',
                method: 'GET',
                success: function(data) {
                    $('#time_slot').val(data.time_slot);
                    $('#cutoff').val(data.cutoff);
                    $('#time_start').val(data.time_start);
                    $('#time_end').val(data.time_end);
                    $("#ref").val(data.ref);
                    $("#ref1").val(data.ref1);

                    $('#time_slotForm').attr('action', '{{ url('timeslot/update') }}' + '/' + time_slotId);
                    $('#time_slotForm').append('<input type="hidden" name="_method" value="PUT">');
                    $('#time_slotForm').append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                    $('#submitBtn').text('Update');
                    $('.card-title').text('Update Time slots');
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });

        // Toggle status functionality with Toastr
        $('.toggle-status').on('change', function() {
            var timeSlotId = $(this).data('id');
            var isChecked = $(this).is(':checked');
            var newStatus = isChecked ? 'y' : 'n';
            var statusText = $(this).closest('td').find('.status-text');

            $.ajax({
                url: '/timeslot/toggle-status/' + timeSlotId,
                method: 'POST',
                data: {
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    statusText.text(response.status === 'y' ? 'Yes' : 'No');
                    toastr.success('Status updated successfully!');
                },
                error: function(error) {
                    console.error('Error updating status:', error);
                    $(this).prop('checked', !isChecked);
                    statusText.text(isChecked ? 'No' : 'Yes');
                    toastr.error('Failed to update status!');
                }.bind(this)
            });
        });

        // Initialize Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 3000
        };
    });
</script>
@endpush