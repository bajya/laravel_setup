@extends('layouts.backend.app')
@section('title', 'Email Template - ' . ucfirst($emailtemplate->name))

@section('content')
    <div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ ucfirst($emailtemplate->name) }}</h3></div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('emailtemplate')}}">Email Template</a></li>
                    <li class="breadcrumb-item active">View Email Template</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
            @include('layouts.backend.message')
                <div class="card web-email-view">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column gap-3 email-view" style="gap: 16px;">
                            <div class="d-sm-flex align-items-center justify-content-between">
                                <small class=" text-dark  font-14">Name</small>
                                <div class="text-black  font-17 fw-bold db">{{ ucfirst($emailtemplate->name) }}</div> 
                            </div>
                             <div class="d-sm-flex align-items-center justify-content-between">
                                <small class=" text-dark  font-14">Subject</small>
                                <div class="text-black  font-17 fw-bold db">{{ ucfirst($emailtemplate->subject) }}</div> 
                            </div>
                            <?php /* <div class="d-sm-flex align-items-center justify-content-between">
                                <small class=" text-dark  font-14">Footer </small>
                                <div class="text-black  font-17 fw-bold db">{!! $emailtemplate->footer !!}</div>
                            </div> */?>
                             <div class="email-desc d-sm-block align-items-center justify-content-between">
                                <small class=" text-dark  font-14">Description </small>
                                <div class="text-black  font-17 fw-bold db"> {!! $emailtemplate->description !!}</div>
                            </div>    
                            <!-- <div class="d-sm-flex align-items-center justify-content-between">
                                <small class=" text-dark  font-14">Date Of Brth</small>
                                <div class="text-black  font-17 fw-bold db"> {{date('Y, M d', strtotime($emailtemplate->created_at))}}</div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        <!-- End PAge Content -->

@endsection

@push('scripts')
    
@endpush

