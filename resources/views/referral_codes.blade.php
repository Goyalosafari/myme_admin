@extends('Layouts.master')

@section('title','Referral Codes - Food Delivery')

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Referral Codes</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Referral Codes</li>
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

    <section class="section">
        <div class="card">
            <div class="card-header">Referral Reward Settings</div>
            <div class="card-body">
                <form method="POST" action="{{ route('referral.settings.update') }}" class="d-flex align-items-end gap-2 flex-wrap">
                    @csrf
                    <div>
                        <label class="form-label">Reward credited to referrer's wallet (₹) per successful referral</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" min="0" step="0.01" name="reward_amount" class="form-control"
                                value="{{ old('reward_amount', $settings->reward_amount) }}" required>
                        </div>
                        @error('reward_amount')<div class="text-danger small">{{ $message }}</div>@enderror
                        <div class="form-text">Paid to the referrer's wallet once the referred user's first order is delivered.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span>Users &amp; Referral Codes</span>
                <form method="GET" action="{{ route('referral.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search name / mobile / code..." value="{{ request('search') }}" style="min-width:240px;">
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                    @if(request('search'))
                        <a href="{{ route('referral.index') }}" class="btn btn-sm btn-light-secondary">Clear</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>My Referral Code</th>
                                <th>Referred By (Code Used)</th>
                                <th>Referrer Name</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            @php
                                $referrer = $user->referred_by
                                    ? \App\Models\User::where('referral_code', $user->referred_by)->first()
                                    : null;
                            @endphp
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->mobile ?? '-' }}</td>
                                <td>
                                    @if($user->referral_code)
                                        <span class="badge bg-success fs-6 px-3 py-1" style="font-family:monospace;letter-spacing:1px;">
                                            {{ $user->referral_code }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->referred_by)
                                        <span class="badge bg-info text-dark" style="font-family:monospace;">
                                            {{ $user->referred_by }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($referrer)
                                        <span class="text-primary">{{ $referrer->name }}</span>
                                        <small class="text-muted d-block">{{ $referrer->mobile }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    @if(!$user->referral_code)
                                        <form method="POST" action="{{ route('referral.generate', $user->id) }}"
                                            onsubmit="return confirm('Generate a referral code for {{ $user->name }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-primary">Generate Code</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $users->links() }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header">Referral Summary</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 border rounded">
                            <div class="fs-3 fw-bold text-primary">
                                {{ \App\Models\User::whereNotNull('referral_code')->count() }}
                            </div>
                            <div class="text-muted small">Users with Referral Code</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 border rounded">
                            <div class="fs-3 fw-bold text-success">
                                {{ \App\Models\User::whereNotNull('referred_by')->count() }}
                            </div>
                            <div class="text-muted small">Users Who Were Referred</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 border rounded">
                            <div class="fs-3 fw-bold text-warning">
                                {{ \App\Models\User::whereNull('referral_code')->count() }}
                            </div>
                            <div class="text-muted small">Users Without a Code</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 border rounded">
                            <div class="fs-3 fw-bold text-info">
                                {{ \App\Models\User::count() }}
                            </div>
                            <div class="text-muted small">Total Users</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
