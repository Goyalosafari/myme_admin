<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.conversion-values.*') ? 'active' : '' }}" href="{{ route('admin.conversion-values.index') }}">Rupee to Coin</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.coin-reward-conversions.*') ? 'active' : '' }}" href="{{ route('admin.coin-reward-conversions.index') }}">Coin to Reward</a>
    </li>
</ul>
