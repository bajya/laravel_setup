@extends('layouts.backend.app')
@section('title', 'Tutorial Screen - ' . ucfirst($tutorialscreen->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Tutorial Screen</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('tutorialscreens')}}">Tutorial Screens</a></li>
                    <li class="breadcrumb-item active">View Tutorial Screen</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.backend.message')
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="main_view_img">
                            <div class="view_main_img_title">
                                <span class="mr-5">Image</span>
                            </div>
                            <div class="main-inner-img">
                                <img class="card-title mt-0 mb-0" src="@if($tutorialscreen->image != null){{$tutorialscreen->image}} @endif" width="40%" />
                            </div>
                            <h4 class="m-t-10 m-b-0 text-center">{{ ucfirst($tutorialscreen->name) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li> -->
                        
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile" role="tabpanel">
                            <div class="card-body p-3">
                                <div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Title</h5><small class="text-success db">{{ ucfirst($tutorialscreen->name) }}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Created On</h5><small class="text-success db">{{date('Y, M d', strtotime($tutorialscreen->created_at))}}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Status</h5><small class="text-success db">{{ucfirst(config('constants.STATUS.'.$tutorialscreen->status))}}</small>
                                    </div>
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Description</h5><small class="text-success db">{!! $tutorialscreen->description !!}</small>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body p-3">
                                <form class="form-horizontal form-material" method="post" action="{{route('changeStatusTutorialScreen')}}">
                                    {{csrf_field()}}
                                    <div class="form-group bt-switch">
                                        <div class="col-md-6">
                                            <label class="col-md-6" for="val-block">Status</label>
                                            <input type="hidden" name="statusid" value="{{$tutorialscreen->id}}">
                                            <input type="hidden" name="status" value="{{$tutorialscreen->status}}">
                                            <div class="col-md-2" style="float: right;">
                                                <input type="checkbox" @if($tutorialscreen->status == 'active') checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" id="statusTutorialScreen">
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
            
            $('#statusTutorialScreen').on('switchChange.bootstrapSwitch', function (event, state) {

                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusTutorialScreen").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });
        });
    </script>
@endpush
