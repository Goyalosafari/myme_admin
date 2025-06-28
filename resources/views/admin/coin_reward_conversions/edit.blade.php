@extends('Layouts.master')
@section('content')
@include('admin.conversion_values.tabs')
<div class="container mt-4">
    <h2>Edit Coin to Reward Conversion Value</h2>
    <form action="{{ route('admin.coin-reward-conversions.update', $coinRewardConversion->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Coin to Reward Rate</label>
            <input type="number" step="0.01" name="coin_to_reward_rate" class="form-control" value="{{ $coinRewardConversion->coin_to_reward_rate }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.coin-reward-conversions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
