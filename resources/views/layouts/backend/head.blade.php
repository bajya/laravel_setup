<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<meta name="csrf-token" content="{{csrf_token()}}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/dealfavicon.png')}}">
<link rel="icon" type="image/png" href="{{ asset('img/dealfavicon.png')}}">
<title>{{ config('app.name', 'Laravel') }}</title>

<meta property="og:site_name" content="Discover the best deals and discounts on {{ config('app.name', 'Laravel') }}. Explore a wide range of products/services at unbeatable prices. Save money with our exclusive offers and promotions. Shop smart, shop {{ config('app.name', 'Laravel') }}.">
<meta property="og:title" content="Discover the best deals and discounts on {{ config('app.name', 'Laravel') }}. Explore a wide range of products/services at unbeatable prices. Save money with our exclusive offers and promotions. Shop smart, shop {{ config('app.name', 'Laravel') }}.">
<meta property="og:type" content="website">
<meta property="og:locale" content="en">
<meta property="og:url" content="{{url('/')}}">
<meta property="og:image" content="{{ asset('img/Laravel.gif') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Discover the best deals and discounts on {{ config('app.name', 'Laravel') }}. Explore a wide range of products/services at unbeatable prices. Save money with our exclusive offers and promotions. Shop smart, shop {{ config('app.name', 'Laravel') }}.">
<meta name="description" content="Discover the best deals and discounts on {{ config('app.name', 'Laravel') }}. Explore a wide range of products/services at unbeatable prices. Save money with our exclusive offers and promotions. Shop smart, shop {{ config('app.name', 'Laravel') }}." />
<meta property="og:description" content="Discover the best deals and discounts on {{ config('app.name', 'Laravel') }}. Explore a wide range of products/services at unbeatable prices. Save money with our exclusive offers and promotions. Shop smart, shop {{ config('app.name', 'Laravel') }}."> 
<link rel="canonical" href="{{url('/')}}"> 
<link rel="stylesheet" href="{{URL::asset('/plugins/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/mdi/css/materialdesignicons.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/toastr/toastr.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.base.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/vendor.bundle.addons.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/styles.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/style.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/animate.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/custom.css')}}">
<link rel="stylesheet" href="{{URL::asset('/css/pages/file-upload.css')}}">
<!-- <link href="{{URL::asset('/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet"> -->
<link href="{{URL::asset('/plugins/bootstrap-switch/bootstrap-switch.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('/css/pages/bootstrap-switch.css')}}" rel="stylesheet">
<link href="{{URL::asset('/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
<link href="{{URL::asset('/plugins/nestable/nestable.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
<link href="{{URL::asset('/plugins/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet">
<!-- <script src="{{URL::asset('/plugins/jquery/jquery.min.js')}}"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">


<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
<!-- <link href="{{ asset('frontend/css/nucleo-icons.css')}}" rel="stylesheet" />   -->
<!-- <link href="{{ asset('frontend/css/nucleo-svg.css')}}" rel="stylesheet" />  -->
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style type="text/css">
    /*.preloader {
        background-color: transparent !important;
    }*/

    .loading {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1;
}

#customProcessingMessage {
    font-size: 24px;
    color: #fff;
    font-weight: bold;
}
</style>

<link href="{{URL::asset('/plugins/summernote/summernote.css') }}" rel="stylesheet">
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/css/intlTelInput.css" rel="stylesheet" media="screen"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
<script type="text/javascript">
    //var APP_NAME ='{{ env('APP_URL') }}';
    var APP_NAME ="{{ url('/') }}";
</script>