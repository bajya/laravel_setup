@extends('layouts.backend.app')
@section('title', $type.' CMS - ')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} State</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cms')}}">State</a></li>
                    <li class="breadcrumb-item active">{{$type}} State</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <h4>{{$type}} {{$state->name ?? ''}} State</h4>
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
                                <label>Select Country</label><sup class="text-reddit"> *</sup>
                                <select required class="form-control form-control-line" name="country_id">
                                        <option value="">Select Country</option>
                                        @if(isset($countrylist))
                                            @foreach($countrylist as $row)
                                                <option value="{{$row->id}}" @if(isset($states->country_id) && $row->id == $states->country_id) selected  @endif >{{$row->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>

                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="state_name" required value="{{$states->name ?? ''}}" />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('states')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
                $('input[name=state_name]').rules('add', {remote: APP_NAME + "/admin/states/checkStatesName/{{$states->id}}"});
            @endif
        });
        
    </script>
@endpush