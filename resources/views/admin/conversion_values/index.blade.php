@extends('Layouts.master')
@section('content')
@include('admin.conversion_values.tabs')
<div class="container mt-4">
    <h2>Conversion Settings</h2>
    <a href="{{ route('admin.conversion-values.create') }}" class="btn btn-primary mb-3">Add New Conversion Value</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>₹ per Coin</th>
                <th>Referrer Reward</th>
                <th>Referee Reward</th>
                <th>Minimum Applicable Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conversionValues as $value)
                <tr @if($value->status=='active') style="background:#e6ffe6" @endif>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->coin_conversion_rate }}</td>
                    <td>{{ $value->referrer_reward_points }}</td>
                    <td>{{ $value->referee_reward_points }}</td>
                    <td>{{ $value->minimum_applicable_amount }}</td>
                    <td>
                        @if($value->status=='active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.conversion-values.edit', $value->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        @if($value->status=='inactive')
                        <form action="{{ route('admin.conversion-values.activate', $value->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Activate</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
