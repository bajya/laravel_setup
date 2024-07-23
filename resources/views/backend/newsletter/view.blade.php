@extends('layouts.backend.app')
@section('title', 'Newsletter - ' . ucfirst($newsletter->name))

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Newsletter</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('newsletter')}}">Newsletter</a></li>
                    <li class="breadcrumb-item active">View Newsletter</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.backend.message')
            
            <div class="col-lg-12 col-xlg-12 col-md-12">
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
                                        <h5 class="p-t-20 db">Email</h5><small class="text-success db"> {!! $newsletter->email !!}</small>
                                    </div>
                                     
                                    <div class="view_inner_cls">
                                        <h5 class="p-t-20 db">Created On</h5><small class="text-success db">{{date('Y, M d', strtotime($newsletter->created_at))}}</small>
                                    </div>
                                </div>
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
    
@endpush
