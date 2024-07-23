@extends('layouts.backend.app')
@section('title', ucfirst($type).' Subscription')
@section('content')

<style type="text/css">
.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group #image_preview {
    margin-top: 0px !important;
    border: 1px solid #ddd;
    border-right: 0;
    width: 40px;
    height: 40px;
    border-radius: 0.25rem 0 0 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.input-group #image_preview img {
    width: auto;
    max-height: 100%;
    max-width: 100%;
}
/*.form-group .text-muted {
    position: absolute;
    padding-top: 1px;
    width: 100%;
    left: 0;
    bottom: -15px;
}*/
.text-muted {
    color: #adb5bd !important;
    font-size: 10px;
    line-height: 16px;
    display: block;
    margin-top: 3px;
}
</style>
    <div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Subscription</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Subscription</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                       <?php /* @if($type == 'add')
                            <h4>Fill In User Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit User Details</h4>
                        @endif
                        <hr> */?>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-material row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">
                            {{csrf_field()}}
                                
                                    <div class="form-group col-xxl-3 col-xl-4 col-sm-4">
                                        <label>Subscription Name</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter subscription name" class="form-control form-control-line" name="name" value="{{ isset($subscription->name) && !empty($subscription->name) ? $subscription->name : ''}}" readonly maxlength="100">
                                    </div>

                                    <div class="form-group col-xxl-3 col-xl-4 col-sm-4">
                                        <label>Subscription Type</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter subscription type" class="form-control form-control-line" name="type" value="{{ isset($subscription->type) && !empty($subscription->type) ? $subscription->type : ''}}" readonly maxlength="100">
                                    </div>

                                     <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Subscription Price(INR)</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter subscription price" class="form-control form-control-line" name="price" value="{{ isset($subscription->price) && !empty($subscription->price) ? $subscription->price : ''}}" maxlength="100">
                                    </div>

                                    <div class="form-group col-md-12 ">
                                        <label>Description</label><sup class="text-reddit"></sup>
                                        <textarea class="form-control form-control-line" name="description" rows="">{{ isset($subscription->description) && !empty($subscription->description) ? $subscription->description : ''}}</textarea>
                                    </div>

                                    <input type="hidden" name="roles[]" value="2">
                                   
                        
                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('subscriptions')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>

<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>

    <script type="text/javascript">

        $(function(){
            $('#statusVenders').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusVenders").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });
             $('#livingstatusVenders').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#livingstatusVenders").is(':checked'))
                    $('input[name=living_status]').val('Yes');
                else
                    $('input[name=living_status]').val('No');
            });

        



            $('#changeImage1').click(function(){
                $('#userImage1').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"><i class="fa fa-upload"></i></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="user_image" accept=".jpg, .jpeg, .png"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#userImage1').remove();
                $('#image_exists').val(0);
            });
          
        });

    </script>

    <script type="text/javascript">
    $(function(){
        $('#country_id').select2({
            placeholder: 'Select country', // Your placeholder text here
        });
        $('#state_id').select2({
            placeholder: 'Select state', // Your placeholder text here
        });
        $('#city_id').select2({
            placeholder: 'Select city', // Your placeholder text here
        });
        //Country 
         $("#country_id").change(function() {
            selectedValues = [];
            selectedValues.push($(this).val());
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getStatelistByCountryId')}}";
            id = "#state_id";
            SelectChangeValue(data,url,id,null);


            selectedValues = [];
            selectedValues.push(0);
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getCitylistByStateId')}}";
            id = "#city_id";
            SelectChangeValue(data,url,id,null);


            selectedValues = [];
            selectedValues.push(0);
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getRegionlistByCityId')}}";
            id = "#region_id";
            SelectChangeValue(data,url,id,null);
        });
        //State 
         $("#state_id").change(function() {
            selectedValues = [];
            selectedValues.push($(this).val());
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getCitylistByStateId')}}";
            id = "#city_id";
            SelectChangeValue(data,url,id,null);

            selectedValues = [];
            selectedValues.push(0);
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getRegionlistByCityId')}}";
            id = "#region_id";
            SelectChangeValue(data,url,id,null);
        });
        //City 
         $("#city_id").change(function() {
            selectedValues = [];
            selectedValues.push($(this).val());
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('getRegionlistByCityId')}}";
            id = "#region_id";
            SelectChangeValue(data,url,id,null);
        });
        
        function SelectChangeValue(data,url,id,selectedId){
            valuesArray = null;
            if(selectedId!=null)
            {
                valuesArray = selectedId.split(",");
            }
            options="";
              $.ajax({
                type: 'post',
                url: url,
                data: data,
                dataType: "json",
                cache: false,
              //  mimeType: "multipart/form-data",
                //processData: false,
                //contentType: false,
            })
            .done(function(data) {
                if (data.status == true) {
                  
                  var result = data.data;
                  var select_option = ''; 
                  if (id === '#country_id') {
                    select_option = 'Select Country'; 
                  }else if(id === '#state_id'){
                    select_option = 'Select State'; 
                  }else if(id === '#city_id'){
                    select_option = 'Select City'; 
                  }else{
                    select_option = 'Select Region'; 
                  }
                  options="<option selected  value=''>"+select_option+"</option>";
                  
                  $.each(result, function(key,val) {
                    /*if($.inArray(val.id, valuesArray) !== -1)
                    {
                        options+="<option value='"+val.id+"'>"+val.name+"</option>";
                    }
                    else{*/
                     options+="<option  value='"+val.id+"'>"+val.name+"</option>";   
                    /*}*/
                  });
                }
                  $(id).html(options);

            });
        }
  $("#phone_number").intlTelInput({
                    preferredCountries: ["IL"],
                    separateDialCode: true,
                    initialCountry: "{{$subscription->country_flag?? 'AE'}}"
                }).on('countrychange', function (e, countryData) {
                //  $("#country_code_flags").val($("#phone_number").intlTelInput("getSelectedCountryData").iso2);
                    $("#phone_code").val(($("#phone_number").intlTelInput("getSelectedCountryData").dialCode));
                });

            var input = document.querySelector("#phone_number");
                $("#phone_number").intlTelInput({
                    // options here
                    initialCountry: "{{$subscription->country_flag?? 'AE'}}",
                geoIpLookup: function(success, failure) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "AE";
                    success(countryCode);
                    });
                }
        });
    //     $('.iti__country-list li').click(function(){
    //         $("#phone_code").val($(this).data('dial-code'));
        
    // })


    });

 function imageIsLoaded(e){
    $("#file").css("color","green");
    $('#previewing').attr('src',e.target.result);
}   
$("#file").on("change", function(){
    var fileObj = this.files[0];
    var imageFileType = fileObj.type;
    var imageSize = fileObj.size;

    var match = ["image/jpeg","image/png","image/jpg"];
    if(!((imageFileType == match[0]) || (imageFileType == match[1]) || (imageFileType == match[2]))){
    $('#previewing').attr('src','images/image.png');
        toastr.error('Please Select A valid Image File <br> Note: Only jpeg, jpg and png Images Type Allowed!!');
        return false;
    }else{
        if(imageSize < 1000000){
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }else{
            toastr.error('Images Size Too large Please Select 1MB File!!');
            return false;
        } 
    }
});
   
</script>


@endpush