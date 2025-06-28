@extends('Layouts.master')
@section('content')
<div class="container mt-4">
    <h2>Add Conversion Value</h2>
    <form action="{{ route('admin.conversion-values.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>₹ per Coin</label>
            <input type="number" step="0.01" name="coin_conversion_rate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Referrer Reward Points</label>
            <input type="number" name="referrer_reward_points" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Referee Reward Points</label>
            <input type="number" name="referee_reward_points" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Minimum Applicable Amount</label>
            <input type="number" step="0.01" name="minimum_applicable_amount" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.conversion-values.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
