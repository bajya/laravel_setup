@extends('layouts.backend.app')
@section('title', $type.' CMS - ')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Country</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('countries')}}">Country</a></li>
                    <li class="breadcrumb-item active">{{$type}} Country</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <h4>{{$type}} {{$countries->name ?? ''}} Country</h4>
                        <hr>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-material  row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            <div class="form-group col-md-12 ">
                                <label>Short Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" required name="country_shortname" value="{{$countries->shortname ?? ''}}" />
                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Code</label><sup class="text-reddit"> *</sup>
                                <input type="number" class="form-control form-control-line numberInput" required name="country_code" value="{{$countries->phonecode ?? ''}}" />
                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="country_name" required value="{{$countries->name ?? ''}}" />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('countries')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

 
@endsection

@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            
              $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

          @if($type == 'Edit')
                $('input[name=country_name]').rules('add', {remote: APP_NAME + "/admin/countries/checkCountryName/{{$countries->id}}"});
            @endif
        });
        
    </script>
@endpush