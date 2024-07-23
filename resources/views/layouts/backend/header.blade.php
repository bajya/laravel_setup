@if(Auth()->user())
<style type="text/css">
    a.nottify-btn {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #6c8bc0;
    color: #fff;
    padding: 10px;
    border-radius: 999px;
    max-width: 200px;
    margin: 0 auto 10px;
}
#notificationList {
    max-height: 300px !important;
    overflow-y: auto;
}
</style>
<input type="hidden" name="auth_user_id" id="auth_user_id" value="{{ Auth()->user()->id }}">
<input type="hidden" name="auth_user_token" id="auth_user_token" value="{{ auth()->user()->createToken('Token')->accessToken }}">
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row" id="topbar">
    <div class="navbar-menu-wrapper d-flex align-items-center ml-auto ml-lg-0">
        <ul class="navbar-nav navbar-nav-right">
            @php
                $notifications = App\Notificationuser::where('is_read', '0')->where('receiver_id', Auth::user()->id)->get();
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="mdi mdi-bell"></i>
                    @if(count($notifications)>0)
                    <span class="count" id="notCount">{{count($notifications)}}</span>
                    @endif
                </a> 
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="notificationList" aria-labelledby="notificationDropdown">
                    @if(count($notifications)>0)
                        <div class="dropdown-item d-flex justify-content-between align-items-center"  >
                            <p class="mb-0 font-weight-normal float-left">You have {{count($notifications)}} new notifications
                            </p>
                              <div>
                                <a href="javascript:void(0)" onclick="clearAllNotification()">Clear All</a>
                              </div>
                        </div>
                      
                        @foreach($notifications->sortByDesc('created_at') as $not)
                            <?php
                                $redirectUrl = '';

                                if ($not->notification_type == 'Vendor Register') {
                                    $redirectUrl = 'admin/vendors/list';

                                } else if ($not->notification_type == 'Customer Register') {
                                    $redirectUrl = 'admin/users/list';

                                } else {
                                    $redirectUrl = 'admin';
                                }
                            ?>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item" href="javascript:void(0);" data-id="{{$not->id}}" data-url="{{url('').'/'.$redirectUrl}}" onclick="readNotification(this)">
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-medium text-dark">{{Str::limit($not->title,50,'...')}}</h6>
                                    <p class="preview-subject font-weight-medium small-text text-dark">{{Str::limit($not->description,50,'...')}}</p>
                                    <p class="font-weight-light small-text">
                                        {{App\Library\Helper::get_time_ago($not->created_at)}}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="nottify-btn" href="{{route('notifications')}}">
                            <span class=" ">View all</span>
                        </a>

                    @else
                        <a class="dropdown-item">
                            <p class="mb-0 font-weight-normal float-left">Notifications 
                            </p>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item preview-item">
                            <div class="preview-item-content">
                                <h6 class="preview-subject font-weight-medium text-dark" style="padding-top: 100px;">No Notification Found</h6>
                            </div>
                        </a>
                    @endif
                </div>
            </li> 
            <li class="nav-item dropdown d-none d-lg-inline-block">
                <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    
                        @if(Auth::user()->role == 'Vendor')
                           <span class="profile-text" style="font-weight: 600; font-size: 14px;">{{ ucfirst(Auth::user()->store_name) }}</span>
                            <img class="img-xs rounded-circle" src="{{ Auth::user()->avatar }}" alt="Profile image">  
                        @else
                            <span class="profile-text" style="font-weight: 600; font-size: 14px;">{{ ucfirst(Auth::user()->name).' '.ucfirst(Auth::user()->last_name) }}</span>
                            <img class="img-xs rounded-circle" src="{{ Auth::user()->avatar }}" alt="Profile image">
                        @endif

                   
                    
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <ul class="dropdown-user">
                        <li>
                            <div class="dw-user-box">
                                <div class="u-img"><img src="{{ Auth::user()->avatar }}" alt="user" class="profile-pic" style="height: 60px; width: 70px;" /></div>
                                <div class="u-text">
                                    <h4>{{ config('app.name', 'Laravel') }}</h4>
                                    <p class="">{{ ucfirst(Auth::user()->name).' '.ucfirst(Auth::user()->last_name) }}</p>
                                    <p class="">{{ Auth::user()->email }}</p>
                                </div>
                            </div>  
                        </li>
                        <!-- <li role="separator" class="divider"></li> -->
                        @if(Auth::user()->role == 'Vendor')
                            <li><a href="{{route('editVendors', ['id' => Auth::user()->id])}}" style="padding: 4px 10px;"><i class="fa fa-pencil"></i> Manage Profile</a></li>
                        @else
                            <li><a href="{{route('editVendors', ['id' => Auth::user()->id])}}" style="padding: 4px 10px;"><i class="fa fa-pencil"></i> Manage Profile</a></li>
                        @endif
                        
                        <li><a href="{{route('changepassword')}}" style="padding: 4px 10px;"><i class="fa fa-key"></i> Change Password</a></li>
                        @if(Auth::user()->role == 'Vendor')
                            <li><a href="{{route('viewVendors', ['id' => Auth::user()->id])}}" style="padding: 4px 10px;"><i class="fa fa-eye"></i> View Profile</a></li>
                        @else
                            <li><a href="{{route('viewUsers', ['id' => Auth::user()->id])}}" style="padding: 4px 10px;"><i class="fa fa-eye"></i> View Profile</a></li>
                        @endif
                        
                        <li><a href="#" class="logoutAdmin" type="header" style="padding: 4px 10px;"><i class="fa fa-power-off"></i>
                            {{ __('Logout') }}
                        </a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

@endif
