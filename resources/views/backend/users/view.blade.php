@extends('layouts.backend.app')
@section('title', 'User - ' . ucfirst($user->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} User</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('users')}}">Users</a></li>
                    <li class="breadcrumb-item active">View User</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body">
                        @include('layouts.backend.message')
                        <center class="m-t-30"> <img class="card-title" src="@if($user->avatar != null){{$user->avatar}} @endif" width="100%" style="object-fit: cover;" />
                            <!-- <h4 class="m-t-10 m-b-0">{{ ucfirst($user->name)}}</h4> -->
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                        @if($user->id != '1')
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body p-3">
                                <div class="d-flex flex-column gap-3" style="gap: 16px;">
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">First Name</small>
                                        <div class="text-black  font-17 fw-bold db">{{ ucfirst($user->name) }}</div> 
                                    </div>
                                    
                                
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Email Address</small>
                                        <div class="text-black  font-17 fw-bold db">{{  $user->email ?? 'N/A' }}</div>
                                    </div>
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Mobile No.</small>
                                        <div class="text-black  font-17 fw-bold db"> {{ $user->phone_code." ". $user->mobile }}</div>
                                    </div>
                                    <!-- @if($user->id != '1')
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">User Total Amount</small>
                                        <div class="text-black  font-17 fw-bold db"> {{ isset($amount) ?  ucFirst($amount) : 'N/A' }}</div>
                                    </div>
                                    
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Total Purchase Box</small>
                                        <div class="text-black  font-17 fw-bold db"> {{ $totalPurchases }}</div>
                                    </div>
                                    @endif -->
                                    <!--<div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Country</small>
                                        <div class="text-black  font-17 fw-bold db"> {{ isset($user->getCountry->name) ? ucFirst($user->getCountry->name) : 'N/A' }}</div>
                                    </div> -->
                                                                       
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Registered On</small>
                                        <div class="text-black  font-17 fw-bold db">{{date('d-m-Y', strtotime($user->created_at))}}</div>
                                    </div>
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Status</small><div class="text-black  font-17 fw-bold db">{{ucfirst(config('constants.STATUS.'.$user->status))}}</div>
                                    </div>

                                    <?php /*<div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Roles</small><div class="text-black  font-17 fw-bold db">@if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $v)
                                            <label class="badge badge-success">{{ $v }}</label>
                                        @endforeach
                                    @endif</div> 
                                    </div>*/?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body ">
                                <form class="form-horizontal form-material p-t-20" method="post" action="{{route('changeStatusUsers')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$user->id}}">
                                            <input type="hidden" name="status" value="{{$user->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($user->status == 'active') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusUsers">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

            $('#statusUsers').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusUsers").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });
        });
    </script>
@endpush
