@extends('layouts.frontend.app')

@section('content')
<?php /*
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="avatar" class="col-md-4 col-form-label text-md-right">{{ __('Avatar') }}</label>

                            <div class="col-md-6">
                                <input id="avatar" type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" required>

                                @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> */?>
<style type="text/css">
    .iti { 
        width: 100%;
    }

    a#signUp_button {
        float: right;
    }
</style>
@php
    use App\Category;
    $categoryLists = Category::where("status","active")->get(['id',"name"]);
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
        <section class="subscribe-sec mb-5">
            <form id="signUpForm" method="post" action="{{ url('api/signUp')}} " enctype="multipart/form-data">
                <input id="device_id" name="device_id" type="hidden" value="">
                <input id="device_token" name="device_token" type="hidden" value="">
                <input id="device_type" name="device_type" type="hidden" value="web">
            <div class="container">
                <div class="headding-cont">
                  <h2 class="">{{ __('Register') }}</h2>
                </div>
                <div class="row"> 
                    <!-- <div class="col-md-6"> -->
                        <div class="form-group col-md-6">
                            <select required class="form-control form-control-line" name="store_category" id= "store_category">
                            <option value="">Select Category</option>
                            @if(isset($categoryLists))
                                @foreach($categoryLists as $row)
                                    <option value="{{$row->id}}">{!! ucfirst($row->name) !!}</option>
                                @endforeach
                            @endif
                            </select>
                            <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                            @error('store_category')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <input id="store_name" type="text" class="form-control @error('store_name') is-invalid @enderror" name="store_name" value="{{ old('store_name') }}" required autocomplete="store_name" autofocus placeholder="Store Name">
                            <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                            @error('store_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>

                        
                        <div class="form-group col-md-6"> 
                            <input type="hidden" name="lat" id="lat" class="form-control" placeholder="Enter latitude" value="26.9124">
                            <input type="hidden" name="lng" id="lng" class="form-control" placeholder="Enter longitude" value="75.7873">
                            <input type="text" placeholder="Please enter address" id="searchInput" class="form-control" name="address" value="">
                            <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <input id="GST_number" type="text" class="form-control" name="GST_number" required autocomplete="GST_number" placeholder="GST number">
                            <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                            @error('GST_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <div class="map" id="map" style="width: 100%; height: 290px;"></div>
                        </div>
                   <!--  </div>
                    <div class="col-md-6"> -->
                        
                        <div class="col-md-6">
                            <div class="form-group ">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Contact Person {{ __('Name') }}">
                            <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                             <div class="form-group  overflow-visible mobileNumber " style="overflow: visible;">
                                    <input type="hidden" class="form-control form-control-line" id="phone_code" name="phone_code" value="91" placeholder="Please enter phone code 91" >
                                    <input type="text" class="form-control form-control-line d-block" id="mobile" name="mobile" value="" placeholder="Please enter phone number">
                                    <small class="help-block error text-danger error_fild mobile_error" style="position:unset;"></small>
                                </div>
                                  <div class="form-group  ">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('E-Mail Address') }}">
                                        <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                     <div class="form-group  ">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">
                                        <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                     <div class="form-group  ">
                                        <a href="javascript:void(0)" class="btn default-btn" id="signUp_button" style="float-right;"> Sign Up</a>
                                    </div>
                        </div>
                        
                       
                        
                      
                      
                       
                   <!--  </div> -->
               
              
                
                <?php /*<div class="form-group col-md-12">
                    <input id="store_logo" type="file" class="form-control @error('store_logo') is-invalid @enderror" name="store_logo" required>
                    <small class="help-block error text-danger error_fild" style="position:unset;"></small>
                    @error('store_logo')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> */?>
                
                </div>
            </div>
        </form>
    </section>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
<script>
    $("#mobile").intlTelInput({
        preferredCountries: ["IN"],
        separateDialCode: true,
        initialCountry: "IN"
    }).on('countrychange', function (e, countryData) {
        $("#phone_code").val(($("#mobile").intlTelInput("getSelectedCountryData").dialCode));
    });

   //email signUp
    $('#signUp_button').on('click', function() {
        $('.error').text('');
        $('.error').removeClass('has-error');
        var form = $('#signUpForm')[0];
        var formData = new FormData(form);
        $('#signUp_button').html('Processing...');
        $('#signUp_button').prop('disabled', true);
        $('.error').removeClass('has-error');
        $('.error').text('');
        $('input+small').text('');
        $('input').parent().removeClass('has-error');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: $('#signUpForm').attr('action'),
            //data: $("#signUpForm").serialize(),
            data: formData,
            dataType: "json",
            cache: false,
            mimeType: "multipart/form-data",
            processData: false,
            contentType: false,
        })
        .done(function(data) {
            if (data.status == 200) {
                $('#signUp_button').prop('disabled', false);
                $('#signUp_button').html('Sign Up');
                $('#name').val('');
                $('#store_name').val('');
                $('#email').val('');
                $('#mobile').val('');
                $('#GST_number').val('');
                $('#store_category').val(null);
                $('#password').val('');
                $('#searchInput').val('');
                toastr.success(data.message);
            } else if (data.errors) {
                $('#signUp_button').prop('disabled', false);
                $('#signUp_button').html('Sign Up');
                $.each(data.errors, function(key, value) {
                    if (key === 'mobile') {
                        $('.mobile_error').text(value);
                    }
                    var input = '#signUpForm input[name=' + key + ']';
                    $(input + '+small').text(value);
                    
                    $(input).parent().addClass('has-error');

                    var textarea = '#signUpForm textarea[name=' + key + ']';
                    $(textarea + '+small').text(value);
                    $(textarea).parent().addClass('has-error');

                    var select = '#signUpForm select[name=' + key + ']';
                    $(select + '+small').text(value);
                    $(select).parent().addClass('has-error');
                });
                //toastr.error(data.message, "Error");
            } else {
                $('#signUp_button').prop('disabled', false);
                $('#signUp_button').html('Sign Up');
                toastr.error(data.message);
            }
        })
    });
</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyCHjWiLctEWEIwycRfETV1LZOEHsEEeZA4"></script>
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key={{env('MAP_API_KEY')}}"></script> -->
<script>
/* script */
function initialize() {
   var latlng = new google.maps.LatLng(26.9124,75.7873);
    var map = new google.maps.Map(document.getElementById('map'), {
      center: latlng,
      zoom: 12
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      draggable: true,
      anchorPoint: new google.maps.Point(0, -29)
   });
    var input = document.getElementById('searchInput');
   // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var geocoder = new google.maps.Geocoder();
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);
    var infowindow = new google.maps.InfoWindow();   
    autocomplete.addListener('place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }
  
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
       
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);          
    
        bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
        infowindow.setContent(place.formatted_address);
        infowindow.open(map, marker);
       
    });
    // this function will work on marker move event into map 
    google.maps.event.addListener(marker, 'dragend', function() {
        geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {        
              bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
              infowindow.setContent(results[0].formatted_address);
              infowindow.open(map, marker);
          }
        }
        });
    });
}
function bindDataToForm(address,lat,lng){
   document.getElementById('searchInput').value = address;
   document.getElementById('lat').value = lat;
   document.getElementById('lng').value = lng;
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
@endsection
