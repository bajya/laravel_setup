@extends('layouts.backend.app')
@section('title', $type.' City - ')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} City</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cities')}}">City</a></li>
                    <li class="breadcrumb-item active">{{$type}} City</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <h4>{{$type}} {{$cities->name ?? ''}} City</h4>
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
                                         @if(isset($countrylist))
                                            @foreach($countrylist as $row)
                                                <option value="{{$row->id}}"
                                                        @if(isset($cities->country_id) && $row->id  == $cities->country_id) 
                                                        selected
                                                         @endif >{{$row->name}}</option>
                                            @endforeach
                                        @endif


                                         
                                       </select>
                            </div>
                            <div class="form-group col-md-12 ">
                                <label>Select State</label><sup class="text-reddit"> *</sup>
                                <select required class="form-control form-control-line" name="state_id" id="state_id">
                                        <option value="">Select State</option>
                                        @if(isset($statelist))
                                            @foreach($statelist as $row)
                                                <option value="{{$row->id}}"
                                                        @if(isset($cities) && ($row->id  == $cities->state_id))
                                                        selected 
                                                        @endif
                                                    >{{$row->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                            </div>

                            <div class="form-group col-md-12 ">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="city_name" required value="{{$cities->name ?? ''}}" />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('cities')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
                $('input[name=city_name]').rules('add', {remote: APP_NAME + "/admin/cities/checkCitiesName/{{$cities->id}}"});
            countryList({{$cities->country_id}});
           // stateList({{$cities->country_id,$cities->state_id}});
            @else 
                countryList(country_id);
            @endif
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
/*
    function stateList1(state_id,country_id){

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
          //  mimeType: "multipart/form-data",
            //processData: false,
            //contentType: false,
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
                    options+="<option value='"+val.id+"'>"+val.name+"</option>";
                }
              });
            }
              $("#state_id").html(options);

        });
    }
*/
    $("#country_id").on("change",function(e){
            var country_id =$(this).val(); 
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
          //  mimeType: "multipart/form-data",
            //processData: false,
            //contentType: false,
        }).done(function(data) {
            if (data.status == 200) {
              var result = data.data;
              $.each(result, function(key,val) {
                options+="<option value='"+val.id+"'>"+val.name+"</option>";
              });
            }
              $("#state_id").html(options);

        });
        });
        });
        
    </script>
@endpush