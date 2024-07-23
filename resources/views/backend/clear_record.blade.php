@extends('layouts.backend.app')
@section('title', 'Clear Record')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Clear Record</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('clears')}}">Clear Record</a></li>
                    <li class="breadcrumb-item active">Clear Record</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            @include('layouts.backend.message')
            <div class="col-6">
                <div class="card">
                    <div class="card-body p-3">
                        <h4 class="card-title">All Data Remove</h4>

                        <form class="form-material row form-valide" method="post" action="{{ route('clearRecord') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="sampleSheet col-12">
                                <div class="variantRow">
                                    <h6>Points to Remember:</h6>
                                    <p>
                                        <ol>
                                            <li>Church & Article Management Data Clear.</li>
                                            <li>Language & Splash Management Data Clear.</li>
                                        </ol>
                                    </p>
                                </div>
                            </div>
                            <input type="hidden" name="type" value="all">
                            <div class="col-12 ">
                                <br>
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    
                    <div class="card-body p-3">
                        <h4 class="card-title">Church & Article Related Data Remove</h4>

                        <form class="form-material row form-valide" method="post" action="{{ route('clearRecord') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="sampleSheet col-12">
                                <div class="variantRow">
                                    <h6>Points to Remember:</h6>
                                    <p>
                                        <ol>
                                            <li>Church Management Data Clear.</li>
                                            <li>Article Management Data Clear.</li>
                                        </ol>
                                    </p>
                                </div>
                            </div>
                            <input type="hidden" name="type" value="article">
                            <div class="col-12 ">
                                <br>
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection