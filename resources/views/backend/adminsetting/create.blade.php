@extends('layouts.backend.app')
@section('title', 'Admin Setting')

@section('content')
    <div class="main-body">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">
                        <h4>Fill In Admin Setting Details</h4>
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
                        <form class="form-material row form-valide" method="post" action="{{ route('updateAdminSetting') }}" enctype="multipart/form-data">
                            {{csrf_field()}}
                           

                            <div class="form-group col-xxl-3 col-xl-3 col-sm-6">
                                        <label>Total User</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter Total User" class="form-control form-control-line" name="total_user" value="{{ old('total_user', isset($adminsetting->total_user) ? $adminsetting->total_user : '') }}" maxlength="20">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-3 col-sm-6">
                                        <label>Total Happy Client</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter Total Happy Client" class="form-control form-control-line" name="total_happy_client" value="{{ isset($adminsetting->total_happy_client) && !empty($adminsetting->total_happy_client) ? $adminsetting->total_happy_client : ''}}" maxlength="20">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-3 col-sm-6">
                                        <label>Total Review</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter Total Review" class="form-control form-control-line" name="total_review" value="{{ isset($adminsetting->total_review) && !empty($adminsetting->total_review) ? $adminsetting->total_review : ''}}" maxlength="20">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-3 col-sm-6">
                                        <label>Total App Download</label><sup class="text-reddit"> *</sup>
                                        <input type="text" placeholder="Please enter Total App Download" class="form-control form-control-line" name="total_app_download" value="{{ isset($adminsetting->total_app_download) && !empty($adminsetting->total_app_download) ? $adminsetting->total_app_download : ''}}" maxlength="20">
                            </div>

                            <div class="form-group col-xxl-3 col-sm-12">
                                        <label>About App Description</label><sup class="text-reddit"> *</sup>
                                        <textarea class="form-control form-control-line" name="about_app_description" rows="3">{{ isset($adminsetting->about_app_description) && !empty($adminsetting->about_app_description) ? $adminsetting->about_app_description : ''}}</textarea>
                            </div>

                            <div class="form-group col-xxl-3 col-sm-12">
                                        <label>Subscription Description </label><sup class="text-reddit"> *</sup>
                                        <textarea class="form-control form-control-line" name="subscription_description" rows="3">{{ isset($adminsetting->subscription_description) && !empty($adminsetting->subscription_description) ? $adminsetting->subscription_description : ''}}</textarea>
                            </div>

                            <div class="form-group col-xxl-3 col-sm-12">
                                        <label>Testimonial Description </label><sup class="text-reddit"> *</sup>
                                        <textarea class="form-control form-control-line" name="testimonial_description" rows="3">{{ isset($adminsetting->testimonial_description) && !empty($adminsetting->testimonial_description) ? $adminsetting->testimonial_description : ''}}</textarea>
                            </div>

                            <div class="form-group col-xxl-3 col-sm-12">
                                        <label>Footer Description </label><sup class="text-reddit"> *</sup>
                                        <textarea class="form-control form-control-line" name="footer_description" rows="3">{{ isset($adminsetting->footer_description) && !empty($adminsetting->footer_description) ? $adminsetting->footer_description : ''}}</textarea>
                            </div>

                            <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Facebook URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Facebook Url" class="form-control form-control-line" name="facebook_url" value="{{ isset($adminsetting->facebook_url) && !empty($adminsetting->facebook_url) ? $adminsetting->facebook_url : ''}}">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Twitter URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Twitter Url" class="form-control form-control-line" name="twitter_url" value="{{ isset($adminsetting->twitter_url) && !empty($adminsetting->twitter_url) ? $adminsetting->twitter_url : ''}}">
                            </div>
                            
                             <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Instagram URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Instagram Url" class="form-control form-control-line" name="instagram_url" value="{{ isset($adminsetting->instagram_url) && !empty($adminsetting->instagram_url) ? $adminsetting->instagram_url : ''}}">
                            </div>

                             <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Linkedin URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Linkedin Url" class="form-control form-control-line" name="linkedin_url" value="{{ isset($adminsetting->linkedin_url) && !empty($adminsetting->linkedin_url) ? $adminsetting->linkedin_url : ''}}">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Whatsapp URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Whatsapp Url" class="form-control form-control-line" name="whatsup_url" value="{{ isset($adminsetting->whatsup_url) && !empty($adminsetting->whatsup_url) ? $adminsetting->whatsup_url : ''}}">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>Play Store URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter Play Store Url" class="form-control form-control-line" name="play_store_url" value="{{ isset($adminsetting->play_store_url) && !empty($adminsetting->play_store_url) ? $adminsetting->play_store_url : ''}}">
                            </div>

                            <div class="form-group col-xxl-3 col-xl-4 col-sm-6">
                                        <label>App Store URL</label><sup class="text-reddit"> *</sup>
                                        <input type="url" placeholder="Please enter App Store Url" class="form-control form-control-line" name="app_store_url" value="{{ isset($adminsetting->app_store_url) && !empty($adminsetting->app_store_url) ? $adminsetting->app_store_url : ''}}">
                            </div>

                            <div class="form-group col-md-12">
                                    <label>Address</label><sup class="text-reddit"> *</sup>
                                    <input type="hidden" name="latitude" id="lat" class="form-control" placeholder="Enter latitude" value="<?php echo isset($adminsetting) && !empty($adminsetting->latitude) ? $adminsetting->latitude : "26.9124";?>">
                    
                                    <input type="hidden" name="longitude" id="lng" class="form-control" placeholder="Enter longitude" value="<?php echo isset($adminsetting) && !empty($adminsetting->longitude) ? $adminsetting->longitude : "75.7873";?>">
                                    <input type="text" placeholder="Please enter address" id="searchInput" class="form-control" name="address" value="{{ isset($adminsetting->address) && !empty($adminsetting->address) ? $adminsetting->address : ''}}">
                            </div>    
                                    <div class="form-group col-md-12">
                                        <div class="map" id="map" style="width: 100%; height: 500px;"></div>
                                    </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
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

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key="></script>
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
@endpush