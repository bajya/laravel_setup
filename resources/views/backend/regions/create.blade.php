@extends('layouts.backend.app')
@section('title', $type.' Region - ')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} City</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('regions')}}">Region</a></li>
                    <li class="breadcrumb-item active">{{$type}} Region</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <h4>{{$type}} {{$cities->name ?? ''}} Region</h4>
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
                                <select required class="form-control form-control-line" name="country_id" id= "country_id">
                                        <option value="">Select Country</option>
                                    </select>

                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Select State</label><sup class="text-reddit"> *</sup>
                                <select required class="form-control form-control-line" name="state_id" id="state_id">
                                        <option value="">Select State</option>
                                    </select>
                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Select City</label><sup class="text-reddit"> *</sup>
                                <select required class="form-control form-control-line" name="city_id" id="city_id">
                                        <option value="">Select City</option>
                                    </select>

                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="region_name" required value="{{$regions->name ?? ''}}" />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('regions')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
                $('input[name=region_name]').rules('add', {remote: APP_NAME + "/admin/regions/checkRegionsName/{{$regions->id}}"});
                 countryList({{$regions->country_id}});
                 stateList({{$regions->country_id}},{{$regions->state_id}});
                 cityList({{$regions->state_id}},{{$regions->city_id}});
            @else 
            countryList(country_id);
            @endif
        });
        
          function countryList(country_id= null) {
               
            var options = "<option value=''>Select Country</option>";
              data={
                device_type:'admin',
                device_token:'',
                device_id:'',
              };
             
             $.ajax({
            type: 'post',
            url: "{{ url('api/countryList')}}",
            data: data,
            dataType: "json",
            cache: false,
          //  mimeType: "multipart/form-data",
            //processData: false,
            //contentType: false,
        })
        .done(function(data) {
            if (data.status == 200) {
              var result = data.data;
              $.each(result, function(key,val) {
                if(val.id == country_id)
                {
                     options+="<option selected value='"+val.id+"'>"+val.name+"</option>";
                }
                else{
                    options+="<option  value='"+val.id+"'>"+val.name+"</option>";
                }
               
              });
            }
              $("#country_id").html(options);

        });
    }
    function stateList(country_id = null,state_id=null) {
        var options = "<option value=''>Select State</option>";
          data={
            country_id:country_id,
            device_type:'admin',
            device_token:'',
            device_id:'',
          };
             $.ajax({
            type: 'post',
            url: "{{ url('api/stateList')}}",
            data: data,
            dataType: "json",
            cache: false,
        })
        .done(function(data) {
            if (data.status == 200) {
              var result = data.data;
              $.each(result, function(key,val) {
                if(val.id == state_id)
                {
                    options+="<option selected value='"+val.id+"'>"+val.name+"</option>";
                }
                else{
                    options+="<option  value='"+val.id+"'>"+val.name+"</option>";
                }
              });
            }
              $("#state_id").html(options);

        });
        }

        $("#country_id").on("change",function(e){
           stateList($(this).val(),'');
        });
        
        function cityList(state_id =null,city_id=null){

              data={
                state_id:state_id,
                device_type:'admin',
                device_token:'',
                device_id:'',
              };
             
             $.ajax({
            type: 'post',
            url: "{{ url('api/cityList')}}",
            data: data,
            dataType: "json",
            cache: false,
          //  mimeType: "multipart/form-data",
            //processData: false,
            //contentType: false,
        })
        .done(function(data) {
            var options = "<option value=''>Select City</option>";
            if (data.status == 200) {
              var result = data.data;
              $.each(result, function(key,val) {
                if(val.id == city_id)
                {
                    options+="<option selected value='"+val.id+"'>"+val.name+"</option>";
                }
                else{
                options+="<option value='"+val.id+"'>"+val.name+"</option>";

                }
              });
              $("#city_id").html(options);
            }
        });
        }
        $("#state_id").on("change",function(e){
          cityList($(this).val());
    });

    </script>
@endpush