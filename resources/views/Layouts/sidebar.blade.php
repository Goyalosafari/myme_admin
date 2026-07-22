<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo" style="height:32px;">
                <span class="brand-name">Myme Admin</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Main</li>

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <i data-feather="home"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('banner.index') ? 'active' : '' }}">
                    <a href="{{ route('banner.index') }}" class="sidebar-link">
                        <i data-feather="image"></i><span>Banners</span>
                    </a>
                </li>

                <li class="sidebar-item has-sub {{ request()->is('category*') || request()->is('food*') || request()->is('recipe*') || request()->is('grocery*') ? 'active open' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i data-feather="grid"></i><span>Menus</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('category.index') }}">Food Category</a></li>
                        <li><a href="{{ route('food.index') }}">Food Items</a></li>
                        <li><a href="{{ route('recipe.index') }}">Recipes</a></li>
                        <li><a href="{{ route('grocery_category.index') }}">Grocery Category</a></li>
                        <li><a href="{{ route('grocery_product.index') }}">Grocery Items</a></li>
                    </ul>
                </li>

                <li class="sidebar-title">Management</li>

                <li class="sidebar-item {{ request()->routeIs('orderbook.index') ? 'active' : '' }}">
                    <a href="{{ route('orderbook.index') }}" class="sidebar-link">
                        <i data-feather="shopping-bag"></i><span>Orders</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('coupen.index') ? 'active' : '' }}">
                    <a href="{{ route('coupen.index') }}" class="sidebar-link">
                        <i data-feather="tag"></i><span>Coupons</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('timeslot.index') ? 'active' : '' }}">
                    <a href="{{ route('timeslot.index') }}" class="sidebar-link">
                        <i data-feather="clock"></i><span>Timeslots</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('pincode.index') ? 'active' : '' }}">
                    <a href="{{ route('pincode.index') }}" class="sidebar-link">
                        <i data-feather="map-pin"></i><span>Pincodes</span>
                    </a>
                </li>

                <li class="sidebar-title">Users</li>

                <li class="sidebar-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="sidebar-link">
                        <i data-feather="users"></i><span>Customers</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('referral.index') ? 'active' : '' }}">
                    <a href="{{ route('referral.index') }}" class="sidebar-link">
                        <i data-feather="share-2"></i><span>Referral Codes</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('loyalty.settings.index') ? 'active' : '' }}">
                    <a href="{{ route('loyalty.settings.index') }}" class="sidebar-link">
                        <i data-feather="gift"></i><span>Loyalty Points</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('notification.index') ? 'active' : '' }}">
                    <a href="{{ route('notification.index') }}" class="sidebar-link">
                        <i data-feather="bell"></i><span>Notifications</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('feedback.index') ? 'active' : '' }}">
                    <a href="{{ route('feedback.index') }}" class="sidebar-link">
                        <i data-feather="message-square"></i><span>Feedback</span>
                    </a>
                </li>
            </ul>
        </div>

        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
