@extends('layouts.backend.app')
@section('title', 'Subscription - ' . ucfirst($subscription->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Subscription</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('subscriptions')}}">Subscriptions</a></li>
                    <li class="breadcrumb-item active">View Subscription</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            
            <div class="col-lg-12 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                        @if($subscription->id != '1')
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                        @endif

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body p-3">
                                <div class="d-flex flex-column gap-3" style="gap: 16px;">
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Subscription Name</small>
                                        <div class="text-black  font-17 fw-bold db">{{ ucfirst($subscription->name) }}</div> 
                                    </div>
                                    
                                
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Subscription Price</small>
                                        <div class="text-black  font-17 fw-bold db">{{  $subscription->price ?? 'N/A' }}</div>
                                    </div>
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Description</small>
                                        <div class="text-black  font-17 fw-bold db"> {{ $subscription->description." ". $subscription->description }}</div>
                                    </div>
                                   
                                                                       
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Registered On</small>
                                        <div class="text-black  font-17 fw-bold db">{{date('d-m-Y', strtotime($subscription->created_at))}}</div>
                                    </div>
                                    <div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Status</small><div class="text-black  font-17 fw-bold db">{{ucfirst(config('constants.STATUS.'.$subscription->status))}}</div>
                                    </div>

                                    <?php /*<div class="d-sm-flex align-items-center justify-content-between">
                                        <small class=" text-dark  font-14">Roles</small><div class="text-black  font-17 fw-bold db">@if(!empty($subscription->getRoleNames()))
                                        @foreach($subscription->getRoleNames() as $v)
                                            <label class="badge badge-success">{{ $v }}</label>
                                        @endforeach
                                    @endif</div> 
                                    </div>*/?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body ">
                                <form class="form-horizontal form-material p-t-20" method="post" action="{{route('changeStatusSubscriptions')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$subscription->id}}">
                                            <input type="hidden" name="status" value="{{$subscription->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($subscription->status == 'active') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusSubscriptions">
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

            $('#statusSubscriptions').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusSubscriptions").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });
        });
    </script>
@endpush
