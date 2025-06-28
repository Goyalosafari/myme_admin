@extends('Layouts.master')
@section('content')
@include('admin.conversion_values.tabs')
<div class="container mt-4">
    <h2>Coin to Reward Conversion Settings</h2>
    <a href="{{ route('admin.coin-reward-conversions.create') }}" class="btn btn-primary mb-3">Add New Conversion Value</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Coin to Reward Rate</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coinRewardConversions as $value)
                <tr @if($value->status=='active') style="background:#e6ffe6" @endif>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->coin_to_reward_rate }}</td>
                    <td>
                        @if($value->status=='active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.coin-reward-conversions.edit', $value->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        @if($value->status=='inactive')
                        <form action="{{ route('admin.coin-reward-conversions.activate', $value->id) }}" method="POST" style="display:inline-block;">
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
