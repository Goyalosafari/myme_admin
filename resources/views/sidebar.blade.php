<div id="sidebar" class='active'>
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <img src="{{asset('images/logo.svg')}}" alt="" srcset="">
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class='sidebar-title'>Main menus</li>
                <li class="sidebar-item {{ request()->is('/dashboard') ? 'active' : '' }}">
                    <a href="{{url('/dashboard')}}" class='sidebar-link'>
                        <i data-feather="home" width="20"></i> 
                        <span>Dashboard</span>
                    </a>        
                </li>
                <li class="sidebar-item {{ request()->routeIs('banner.index') ? 'active' : '' }}">
                    <a href="{{route('banner.index')}}" class='sidebar-link'>
                        <i data-feather="image" width="20"></i> 
                        <span>Banner</span>
                    </a>        
                </li>
                <li class="sidebar-item  has-sub {{ request()->is('category*') || request()->is('food*') || request()->is('recipe') ? 'active open' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i data-feather="triangle" width="20"></i> 
                        <span>Menus</span>
                    </a>
                    <ul class="submenu ">                       
                        <li>
                            <a href="{{ route('category.index') }}"> Food Category </a>
                        </li>                        
                        <li>
                            <a href="{{ route('food.index') }}">Food</a>
                        </li>                       
                        <li>
                            <a href="{{ route('recipe.index') }}">Recipe</a>
                        </li>    
                        <li>
                            <a href="{{ route('grocery_category.index') }}"> Grocery Category </a>
                        </li>                        
                        <li>
                            <a href="{{ route('grocery_product.index') }}">Grocery Items</a>
                        </li>                    
                    </ul>   
                </li>
                <li class="sidebar-item {{request()->routeIs('orderbook.index') ? 'active' : ''}}">
                    <a href="{{route('orderbook.index')}}" class="sidebar-link">
                        <i data-feather="list" width="20"></i> 
                        <span> Orders </span>
                    </a>        
                </li>
                <li class="sidebar-item {{request()->routeIs('coupen.index') ? 'active' : ''}}">
                    <a href="{{route('coupen.index')}}" class='sidebar-link'>
                        <i data-feather="percent" width="20"></i> 
                        <span> Coupon </span>
                    </a>        
                </li>
                <li class="sidebar-item {{request()->routeIs('timeslot.index') ? 'active' : ''}}">
                    <a href="{{route('timeslot.index')}}" class='sidebar-link'>
                        <i data-feather="clock" width="20"></i> 
                        <span> Timeslots </span>
                    </a>        
                </li>
                <li class="sidebar-item {{request()->routeIs('notification.index') ? 'active' : ''}}">
                    <a href="{{route('notification.index')}}" class='sidebar-link'>
                        <i data-feather="bell" width="20"></i> 
                        <span> Notification </span>
                    </a>        
                </li>
                <li class="sidebar-item {{request()->routeIs('pincode.index') ? 'active' : ''}}">
                    <a href="{{route('pincode.index')}}" class='sidebar-link'>
                        <i data-feather="map-pin" width="20"></i> 
                        <span>Pincode</span>
                    </a>        
                </li>
                <li class="sidebar-item {{request()->routeIs('users.index') ? 'active' : ''}}">
                    <a href="{{route('users.index')}}" class='sidebar-link'>
                        <i data-feather="users" width="20"></i> 
                        <span> Users </span>
                    </a>        
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>