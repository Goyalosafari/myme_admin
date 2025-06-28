@extends('Layouts.master')
@section('content')
@include('admin.conversion_values.tabs')
<div class="container mt-4">
    <h2>Add Coin to Reward Conversion Value</h2>
    <form action="{{ route('admin.coin-reward-conversions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Coin to Reward Rate</label>
            <input type="number" step="0.01" name="coin_to_reward_rate" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.coin-reward-conversions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
