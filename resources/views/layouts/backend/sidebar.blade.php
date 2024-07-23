@if(Auth()->user())
<style type="text/css">
    .dawn-angle {
    font-size: 16px;
    margin-left: 15px;
}
a.nav-link.active.collapsed .dawn-angle {
    transform: rotate(270deg);
}
ul.nav.sub-menu.sidebar-submenu {
    padding: 0px;
}
li.submenu-inn::before {
    display: none;
}
li.nav-item.active {
    background: #32388E;
    position: relative;
}
.submenu-inn a.nav-link i {
    margin-right: 1.25rem;
    width: 16px;
    line-height: 1;
    font-size: 18px;
    color: #33398c !important;
}
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center" style="height:100%"> 
                <a class="navbar-brand brand-logo d-flex align-items-center" href="{{ route('dashboard') }}" style="gap:10px">
                    <!-- <b><img src="{{URL::asset('/images/logo.jpg')}}" alt="homepage" class="dark-logo" style="width: 10%;"/></b> -->
                     <img src="{{ asset('images/offerlogo.gif') }}" alt="homepage" class="dark-logo" style="width: 100px;">
                    <!-- <h3>{{ config('app.name', 'Laravel') }}</h3> -->
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                   <b><img src="{{ asset('images/offerlogo.gif') }}" alt="homepage" class="dark-logo" style="width: 60%;"></b>
                     <!-- <h3>{{ config('app.name', 'Laravel') }}</h3>  -->
                </a>
            </div>
            <div class="nav-link d-flex d-lg-none">
                <div class="user-wrapper">
                    <div class="text-wrapper">
                        <p class="profile-name">{{ config('app.name', 'Laravel') }}</p>
                        <div>
                            <small class="designation text-muted">{{ Auth::user()->name }}</small>
                            <span class="status-indicator online"></span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <ul class="nav sidebarLinks">

        <li class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
            <a class="nav-link first-link" href="{{route('dashboard')}}"><i class="menu-icon mdi mdi-gauge"></i><span class="menu-title">Dashboard</a>
        </li>
        
       <!--  <li class="nav-item {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('roles')}}"><i class="menu-icon fa fa-unlock-alt"></i><span class="menu-title">Role Management</span></a>
        </li>  -->
      <!--   <li class="nav-item {{ request()->is('admin/admins') || request()->is('admin/admins/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('admins')}}"><i class="menu-icon fa fa-user"></i><span class="menu-title">Admins</span></a>
        </li> --> 
        @if(Auth::user()->role == 'Vendor')

            <li class="nav-item {{ request()->is('admin/vendor_addcustomers') || request()->is('admin/vendor_addcustomers/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('vendoraddcustomers')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Customer Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/vendor_salespersons') || request()->is('admin/vendor_salespersons/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('vendorsalepersons')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Sales Person Manager</span></a>
            </li>
            
            @if(Auth::user()->store_category == '44')
                @if(Auth::user()->table_toggle == 'Yes')
                <li class="nav-item {{ request()->is('admin/kitchens') || request()->is('admin/kitchens/*') ? 'active' : '' }}">
                    <a class="nav-link first-link" href="{{route('kitchens')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Kitchen Manager</span></a>
                </li>
                <!-- <li class="nav-item {{ request()->is('admin/tablets') || request()->is('admin/tablets/*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{route('tablets')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Tablet Manager</span></a>
                </li> -->

                <li onclick="toggleSubMenu(this)" class="nav-item mainmenu {{ request()->is('admin/restaurant_categories/list') || request()->is('admin/restaurant_subcategories/list') || request()->is('admin/restaurant_items/list') || request()->is('admin/restaurant_ingredients/list') || request()->is('admin/restaurant_orders/list') ? 'active' : '' }}">
                    <a class="nav-link first-link" data-toggle="collapse" href="#orderMenuTest" aria-expanded="false" aria-controls="orderMenuTest">
                        <i class="menu-icon fa fa-envelope"></i><span class="menu-title">Table Manager</span><div class="dawn-angle"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
                    </a>
                    <div class="collapse {{ request()->is('admin/restaurant_categories/list') || request()->is('admin/restaurant_subcategories/list') || request()->is('admin/restaurant_items/list') || request()->is('admin/restaurant_ingredients/list') || request()->is('admin/restaurant_orders/list') ? 'show' : '' }}" id="orderMenuTest">
                        <ul class="nav sub-menu sidebar-submenu">
                            <li class="submenu-inn {{ request()->is('admin/restaurant_categories/list') ? 'sidebar_active' : '' }}">
                                <a class="nav-link" href="{{ route('restaurantcategories') }}"><i class="fa-solid fa-layer-group"></i>Category Manager</a>
                            </li>
                            
                            <li class="submenu-inn {{ request()->is('admin/restaurant_subcategories/list') ? 'sidebar_active' : '' }}">
                                <a class="nav-link" href="{{ route('restaurantsubcategories') }}"><i class="fa-solid fa-layer-group"></i>Sub Category Manager</a>
                            </li>

                            <li class="submenu-inn {{ request()->is('admin/restaurant_items/list') ? 'sidebar_active' : '' }}">
                                <a class="nav-link" href="{{ route('restaurantitems') }}"><i class="fa-solid fa-sitemap"></i>Item Manager</a>
                            </li>

                            <li class="submenu-inn {{ request()->is('admin/restaurant_ingredients/list') ? 'sidebar_active' : '' }}">
                                <a class="nav-link" href="{{ route('restaurantingredients') }}"><i class="fa-solid fa-cart-shopping"></i>Ingredient Manager</a>
                            </li>

                            <li class="submenu-inn {{ request()->is('admin/restaurant_orders/list') ? 'sidebar_active' : '' }}">
                                <a class="nav-link" href="{{ route('orders') }}"><i class="fa-solid fa-list"></i>Order Manager</a>
                            </li>
                        </ul>
                    </div>
                </li>


                
                @endif
            @else
                <li class="nav-item {{ request()->is('admin/vendorattributes') || request()->is('admin/vendorattributes/*') ? 'active' : '' }}">
                    <a class="nav-link first-link" href="{{route('vendorattributes')}}"><i class="menu-icon fa fa-certificate"></i><span class="menu-title">Attribute Manager</span></a>
                </li>
                <li class="nav-item {{ request()->is('admin/vendor_products') || request()->is('admin/vendor_products/*') ? 'active' : '' }}">
                    <a class="nav-link first-link" href="{{route('vendorproducts')}}"><i class="menu-icon fa fa-product-hunt"></i><span class="menu-title">Product Manager</span></a>
                </li>
                <li class="nav-item {{ request()->is('admin/vendorinvoices') || request()->is('admin/vendorinvoices/*') ? 'active' : '' }}">
                    <a class="nav-link first-link" href="{{route('vendorinvoices')}}"><i class="menu-icon fa fa-file"></i><span class="menu-title">Invoice Manager</span></a>
                </li>
            @endif
           
            
            <!-- <li class="nav-item {{ request()->is('admin/vendorsubscriptions') || request()->is('admin/vendorsubscriptions/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('vendorsubscriptions')}}"><i class="menu-icon fa fa-trophy"></i><span class="menu-title">Buy Subscription</span></a>
            </li> -->
        
            <!-- <li class="nav-item {{ request()->is('admin/vendortransactions') || request()->is('admin/vendortransactions/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('vendortransactions')}}"><i class="menu-icon fa fa-exchange"></i><span class="menu-title">Transaction Manager</span></a>
            </li> -->

            <li class="nav-item {{ request()->is('admin/vendor_order_transactions') || request()->is('admin/vendor_order_transactions/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('vendorordertransactions')}}"><i class="menu-icon fa fa-exchange"></i><span class="menu-title">Transaction Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/vendor_postoffers') || request()->is('admin/vendor_postoffers/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('vendorpostoffer')}}"><i class="menu-icon fa fa-tag"></i><span class="menu-title">Offer Request Manager</span></a>
            </li>

             
            
    <!--     @if(Auth::user()->store_category == '44')
                <li class="nav-item active dropdown">

                    <a class="nav-link " href="javascript:void" data-toggle="collapse" data-target="#demo">
                        Menu Manager    
                    </a>
                    <ul  id="demo" class="collapse">
                        <li class="nav-item"><a class="nav-link" href="">Item Manager</a></li>
                          <li class="nav-item active"><a class="nav-link" href="">Ingredient Manager</a></li>
                    </ul>
                </li>
            @endif  -->
        @else
            <li class="nav-item {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('users')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Customer Manager</span></a>
            </li>
              <li class="nav-item {{ request()->is('admin/vendors') || request()->is('admin/vendors/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('vendors')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Vendor Manager</span></a>
            </li>

             <li class="nav-item {{ request()->is('admin/invoices') || request()->is('admin/invoices/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('invoices')}}"><i class="menu-icon fa fa-file"></i><span class="menu-title">Invoice Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('products')}}"><i class="menu-icon fa fa-product-hunt"></i><span class="menu-title">Product Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/salespersons') || request()->is('admin/salespersons/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('salepersons')}}"><i class="menu-icon fa fa-users"></i><span class="menu-title">Sales Person Manager</span></a>
            </li>

            <li class="nav-item {{ request()->is('admin/attributes') || request()->is('admin/attributes/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('attributes')}}"><i class="menu-icon fa fa-certificate"></i><span class="menu-title">Attribute Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/brands') || request()->is('admin/brands/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('brands')}}"><i class="menu-icon fa fa-dollar"></i><span class="menu-title">Brand Manager</span></a>
            </li> 
            <li class="nav-item {{ request()->is('admin/categories') || request()->is('admin/categories/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('categories')}}"><i class="menu-icon fa fa-list-alt"></i><span class="menu-title">Category Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/postofferreq') || request()->is('admin/postofferreq/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('postoffers')}}"><i class="menu-icon fa fa-tag"></i><span class="menu-title">Post Offer Request Manager</span></a>
            </li>

            <li class="nav-item {{ request()->is('admin/subscriptions') || request()->is('admin/subscriptions/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('subscriptions')}}"><i class="menu-icon fa fa-trophy"></i><span class="menu-title">Subscription Manager</span></a>
            </li> 
            <li class="nav-item {{ request()->is('admin/pushs/list') || request()->is('admin/pushs/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('pushs') }}"><i class="menu-icon fa fa-bell"></i><span class="menu-title">Push Notifications</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/vouchers') || request()->is('admin/vouchers/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('vouchars')}}"><i class="menu-icon fa fa-trophy"></i><span class="menu-title">Voucher Manager</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/testimonials') || request()->is('admin/testimonials/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('testimonials')}}"><i class="menu-icon fa fa-quote-left"></i><span class="menu-title">Testimonial Manager</span></a>
            </li>

            <li class="nav-item {{ request()->is('admin/transactions') || request()->is('admin/transactions/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{route('transactions')}}"><i class="menu-icon fa fa-exchange"></i><span class="menu-title">Transaction Manager</span></a>
            </li> 
           
            <li class="nav-item {{ request()->is('admin/cms/list') || request()->is('admin/cms/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('cms') }}"><i class="menu-icon fa fa-file"></i><span class="menu-title">CMS Managment</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/newsletter/list') || request()->is('admin/newsletter/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('newsletter') }}"><i class="menu-icon fa fa-newspaper-o"></i><span class="menu-title">Newsletter Managment</span></a>
            </li>
            <li class="nav-item {{ request()->is('admin/contactus/list') || request()->is('admin/contactus/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('contactus') }}"><i class="menu-icon fa fa-address-book"></i><span class="menu-title">Contact Us Managment</span></a>
            </li>
             <li class="nav-item {{ request()->is('admin/emailtemplate/list') || request()->is('admin/emailtemplate/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('emailtemplate') }}"><i class="menu-icon fa fa-envelope"></i><span class="menu-title">Email Template</span></a>
            </li> 
             
             <li class="nav-item {{ request()->is('admin/adminsetting/list') || request()->is('admin/adminsetting/list/*') ? 'active' : '' }}">
                <a class="nav-link first-link" href="{{ route('adminsettings') }}"><i class="menu-icon fa fa-cog"></i><span class="menu-title">Setting Managment</span></a>
            </li>

        <!-- <li class="nav-item {{ request()->is('admin/notifications') || request()->is('admin/notifications/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('notifications')}}"><i class="menu-icon fa fa-product-hunt"></i><span class="menu-title">Notification Manager</span></a>
            </li> -->
             <!-- <li class="nav-item {{ request()->is('admin/offers') || request()->is('admin/offers/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('offers')}}"><i class="menu-icon fa fa-tag"></i><span class="menu-title">Offer Manager</span></a>
            </li> -->
             <!-- <li class="nav-item {{ request()->is('admin/settings/list') || request()->is('admin/settings/list/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('settings') }}"><i class="menu-icon fa fa-cog"></i><span class="menu-title">Setting Managment</span></a>
            </li> -->
        
        @endif
    </ul>
</nav>
@endif