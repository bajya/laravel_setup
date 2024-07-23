@extends('layouts.backend.app')
@section('title', 'Dashboard')

@section('content')

<!-- <style>
  
  .rwd-table {
    margin: auto;
    min-width: 300px;
    max-width: 100%;
    border-collapse: collapse;
  }

  .rwd-table tr:first-child {
    border-top: none;
    background: #6d8cc2;
    color: #fff;
  }

  .rwd-table tr {
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    background-color: #f5f9fc;
  }

  .rwd-table tr:nth-child(odd):not(:first-child) {
    background-color: #ebf3f9;
  }

  .rwd-table th {
    display: none;
  }

  .rwd-table td {
    display: block;
  }

  .rwd-table td:first-child {
    margin-top: .5em;
  }

  .rwd-table td:last-child {
    margin-bottom: .5em;
  }

  .rwd-table td:before {
    content: attr(data-th) ": ";
    font-weight: bold;
    width: 120px;
    display: inline-block;
    color: #000;
  }

  .rwd-table th,
  .rwd-table td {
    text-align: left;
  }

  .rwd-table {
    color: #333;
    border-radius: .4em;
    overflow: hidden;
  }

  .rwd-table tr {
    border-color: #bfbfbf;
  }

  .rwd-table th,
  .rwd-table td {
    padding: .5em 1em;
  }
  @media screen and (max-width: 601px) {
    .rwd-table tr:nth-child(2) {
      border-top: none;
    }
  }
  @media screen and (min-width: 600px) {
    .rwd-table tr:hover:not(:first-child) {
      background-color: #d8e7f3;
    }
    .rwd-table td:before {
      display: none;
    }
    .rwd-table th,
    .rwd-table td {
      display: table-cell;
      padding: .25em .5em;
    }
    .rwd-table th:first-child,
    .rwd-table td:first-child {
      padding-left: 0;
    }
    .rwd-table th:last-child,
    .rwd-table td:last-child {
      padding-right: 0;
    }
    .rwd-table th,
    .rwd-table td {
      padding: 1em !important;
    }
}
</style> -->

  <div class="content-wrapper">
     <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-primary">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <?php /*<form method="GET" id="filter_form" class="fillter-inline" role="form">
      <div class="row">
          <div class="col-12">
              <div class="card-header">
                  <!-- <h4 class="card-title">Filters</h4> -->
                  <div class="row">
                      <div class="col-lg-2 col-md-4">
                          <div class="form-group mb-0">
                              <!-- <label for="status">Status</label> -->
                              <select class="form-control status" id="created_at" name="created_at" style="display:none;">
                                    <option value="">Select </option>
                                    <option value="day" {{ (isset($_GET['created_at'])) ? ($_GET['created_at']=='day') ? "selected" : "" : "" }}>Past 24 hours</option>
                                    <option value="week" {{ (isset($_GET['created_at'])) ? ($_GET['created_at']=='week') ? "selected" : "" : "" }}>Past 7 days</option>
                                    <option value="month" {{ (isset($_GET['created_at'])) ? ($_GET['created_at']=='month') ? "selected" : "" : "" }}>Past 30 days</option>
                                    <option value="year" {{ (isset($_GET['created_at'])) ? ($_GET['created_at']=='year') ? "selected" : "" : "" }}>Past 365 days</option>
                                    <option value="custom" {{ (isset($_GET['created_at'])) ? ($_GET['created_at']=='custom') ? "selected" : "" : "" }}>Custom</option>
                                </select>
                          </div>
                      </div>
                      @php
                          if((isset($_GET['created_at'])) && ($_GET['created_at']=='custom')){
                            $class_style = 'display:block';
                          }else{
                            $class_style = 'display:none';
                          }
                      @endphp
                      <div class="col-lg-2 col-md-4 custom_date" style="{{ $class_style }}">
                          <div class="form-group mb-0">
                              <input type="text" id="from_date" name="from_date" class="form-control" placeholder="Enter from date" value=''>
                          </div>
                      </div>
                      <div class="col-lg-2 col-md-4 custom_date" style="{{ $class_style }}">
                          <div class="form-group mb-0">
                              <input type="text" id="end_date" name="end_date" class="form-control" placeholder="Enter end date" value=''>
                          </div>
                      </div>
                <!--      <div class="col-lg-4 col-md-12 d-flex align-items-center">
                          <div class="form-group flex-column flex-md-row mb-0">
                              <input type="submit" class="btn btn-success" value="Filter">
                              <a href="{{ route('dashboard') }}" class="btn waves-effect waves-light btn-primary reset">Reset</a>
                          </div>
                      </div> -->
                  </div>
              </div>
          </div>
      </div>
    </form> */?>
          <div class="row mt-3">
            
          @if(Auth::user()->is_admin=='Yes')
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                  <a class="" href="{{route('users')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-users text-danger icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Users</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_user}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-alert-octagon mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                        
                    </div>
                    </a>
                </div>
            </div>
          @endif 
             <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-users text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Vendors</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_vendor ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-list-alt text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Category</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_category ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-dollar text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Brand</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_brand ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-certificate text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Attribute</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_attribute ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-product-hunt text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Products</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_item ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-tag text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Offer</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_offer ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('categories')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-gift text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Voucher</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_voucher ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>

            
             <!-- <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('countries')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-globe text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Countries</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_country}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('states')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-globe text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total State</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_state}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('cities')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-globe text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Cities</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_city}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
             <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('regions')}}">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-globe text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Villages/Region</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_region}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div> -->
           <!--  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('users')}}?type=Male">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-users text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Males</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_male ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{route('users')}}?type=Female">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <i class="fa fa-users text-warning icon-lg"></i>
                            </div>
                            <div class="float-right">
                                <p class="mb-0 text-right">Total Females</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0">{{$total_female ?? 0}}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="text-muted mt-3 mb-0">
                            <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                        </p> --}}
                    </div>
                    </a>
                </div>
            </div> -->


          
           <?php /* <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
                <div class="card card-statistics">
                    <a href="{{ route('cms') }}">
                        <div class="card-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <i class="fa fa-desktop text-warning icon-lg"></i>
                                </div>
                                <div class="float-right" style="width:70%">
                                    <p class="mb-0 text-right">Total Splash Screen</p>
                                    <div class="fluid-container">
                                        <h3 class="font-weight-medium text-right mb-0">{{ $total_splash_screen }}</h3>
                                    </div>
                                </div>
                            </div>
                            {{-- <p class="text-muted mt-3 mb-0">
                                <i class="mdi mdi-bookmark-outline mr-1" aria-hidden="true"></i> 65% lower growth
                            </p> --}}
                        </div>
                    </a>
                </div>
            </div> */?>
            
        </div>
        <div class="row">
          <?php /*  <div class="col-md-6 col-sm-6 mt-sm-0 mt-6">
              <div class="card overflow-hidden">
                <div class="card-header p-3 pb-0">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Income</p>
                  <h5 class="font-weight-bolder mb-0">
                         {{$total_transaction}}
                    <!-- <span class="text-success text-sm font-weight-bolder">+100%</span> -->
                  </h5>
                </div>
                <div class="card-body p-0">
                  <div class="chart chart-area">
                    <!-- <canvas id="chart-line-2" class="chart-canvas" height="100"></canvas> -->
                    <canvas id="myAreaChart" class="chart-canvas" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div> */?>
            @if(Auth::user()->is_admin=='Yes')
                <div class="col-md-6 col-sm-6 mt-sm-0 mt-6">
                  <div class="card overflow-hidden">
                      <div class="card-header p-3 pb-0">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Customers</p>
                        <h5 class="font-weight-bolder mb-0">
                          {{ $total_user }}
                          <!-- <span class="text-success text-sm font-weight-bolder">+100%</span> -->
                        </h5>
                      </div>
                      <div class="card-body p-0">
                        <div class="chart">
                          <!-- <canvas id="chart-line-1" class="chart-canvas" height="100"></canvas> -->
                          <div id="pie_chart" height="100" class="chart-canvas">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6 mt-sm-0 mt-6">
                  <div class="card overflow-hidden">
                      <div class="card-header p-3 pb-0">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Vendors</p>
                        <h5 class="font-weight-bolder mb-0">
                          {{ $total_vendor }}
                          <!-- <span class="text-success text-sm font-weight-bolder">+100%</span> -->
                        </h5>
                      </div>
                      <div class="card-body p-0">
                        <div class="chart">
                          <!-- <canvas id="chart-line-1" class="chart-canvas" height="100"></canvas> -->
                          <div id="pie_chart_vendor" height="100" class="chart-canvas">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
             @endif
        </div> 
       
      
        @if(Auth::user()->is_admin=='Yes')                    
          @if(!empty($total_vendor_query))
            <div class="row">
                <div class="col-md-12 col-sm-12 mt-sm-0 mt-12">
                  <div class="card overflow-hidden">
                      <div class="card-header p-3 pb-0">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Recent Vendors Details</p>
                        <h5 class="font-weight-bolder mb-0">
                          <a class="btn btn-info" href="{{route('vendors')}}"> View All
                                  </a>
                        </h5> 
                      </div>
                      <div class="card-body p-0">
                         <div class="portlet light">
                         <div class="portlet-body">
                            <div class="tabbable-line">
                               <div class="table-responsive">
                                  <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                          <th>Full Name</th>
                                          <th>Mobile No.</th>
                                          <th>Email</th>
                                          <th>Registration Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                           @foreach ($total_vendor_query  as $latest)
                                            <tr>
                                              <td>{!! $latest->name ?? '-' !!}</td>
                                              <td>{{ $latest->mobile  }} </td>
                                              <td>{{ $latest->email }}</td>
                                              <td>{{ date('d-m-Y', strtotime($latest->created_at)) }}</td>
                                            </tr>
                                          @endforeach          
                                    </tbody>
                                  </table>
                               </div>
                            </div>
                         </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
          @endif
        @endif


    </div>
@endsection
@push('scripts')

<script>
    $(document).ready(function(){
        $("#created_at").change(function(){
            $(this).find("option:selected").each(function(){
                $("#from_date").val('');
                $("#end_date").val('');
                var optionValue = $(this).attr("value");
                if(optionValue == 'custom'){
                    $(".custom_date").css('display', 'block');
                } else{
                    $(".custom_date").css('display', 'none');
                }
            });
        }).change();
    });
</script>
<script>
    $(document).ready(function(){
      var from_date = '<?php echo (isset($_GET['from_date'])) && !empty($_GET['from_date']) ? $_GET['from_date'] : ""; ?>';
      var end_date = '<?php echo (isset($_GET['end_date'])) && !empty($_GET['end_date']) ? date('Y-m-d', strtotime($_GET['end_date'])) : ""; ?>';
      if (from_date !== '') {
          $('#from_date').bootstrapMaterialDatePicker('setDate', from_date);
      }
      if (end_date !== '') {
          $('#end_date').bootstrapMaterialDatePicker('setDate', end_date);
      }
    });
</script>
      <!-- Page level plugins -->
<script src="{{URL::asset('backend/plugins/chart.js/Chart.min.js')}}"></script>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
{{-- pie chart --}}
<script type="text/javascript">
  var analytics = <?php echo $users; ?>

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart()
  {
      var data = google.visualization.arrayToDataTable(analytics);
      var options = {
          title : 'Last 7 Days registered customers'
      };
      var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
      // alert(chart);
      chart.draw(data, options);
  }
</script>

<script type="text/javascript">
  var last_message = '{{ $message}}';
  var analytic_vendors = <?php echo $vendors; ?>
  

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart_vendors);

  function drawChart_vendors()
  {
      var data_vendors = google.visualization.arrayToDataTable(analytic_vendors);
      var options_vendors = {
          title : last_message
      };
      var chart_vendors = new google.visualization.PieChart(document.getElementById('pie_chart_vendor'));
      chart_vendors.draw(data_vendors, options_vendors);
  }
</script>
<script type="text/javascript">
  var last_message = '{{ $message}}';
  var analytic_articles = <?php echo $articles; ?>
  

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart_articles);

  function drawChart_articles()
  {
      var data_article = google.visualization.arrayToDataTable(analytic_articles);
      var options_article = {
          title : last_message
      };
      var chart_article = new google.visualization.PieChart(document.getElementById('pie_chart_article'));
      chart_article.draw(data_article, options_article);
  }
</script>
  {{-- line chart --}}
  <script type="text/javascript">
    const url = "{{route('Income')}}";
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

      var ctx = document.getElementById("myAreaChart");

        axios.get(url)
              .then(function (response) {
                const data_keys = Object.keys(response.data);
                const data_values = Object.values(response.data);
                var myLineChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                    labels: data_keys, 
                    datasets: [{
                      label: "Earnings",
                      lineTension: 0.3,
                      backgroundColor: "rgba(78, 115, 223, 0.05)",
                      borderColor: "rgba(78, 115, 223, 1)",
                      pointRadius: 3,
                      pointBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointBorderColor: "rgba(78, 115, 223, 1)",
                      pointHoverRadius: 3,
                      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                      pointHitRadius: 10,
                      pointBorderWidth: 2,
                      data:data_values,
                    }],
                  },
                  options: {
                    maintainAspectRatio: false,
                    layout: {
                      padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                      }
                    },
                    scales: {
                      xAxes: [{
                        time: {
                          unit: 'date'
                        },
                        gridLines: {
                          display: false,
                          drawBorder: false
                        },
                        ticks: {
                          maxTicksLimit: 7
                        }
                      }],
                      yAxes: [{
                        ticks: {
                          maxTicksLimit: 5,
                          padding: 10,
                          callback: function(value, index, values) {
                            return 'Rs' + number_format(value);
                          }
                        },
                        gridLines: {
                          color: "rgb(234, 236, 244)",
                          zeroLineColor: "rgb(234, 236, 244)",
                          drawBorder: false,
                          borderDash: [2],
                          zeroLineBorderDash: [2]
                        }
                      }],
                    },
                    legend: {
                      display: false
                    },
                    tooltips: {
                      backgroundColor: "rgb(255,255,255)",
                      bodyFontColor: "#858796",
                      titleMarginBottom: 10,
                      titleFontColor: '#6e707e',
                      titleFontSize: 14,
                      borderColor: '#dddfeb',
                      borderWidth: 1,
                      xPadding: 15,
                      yPadding: 15,
                      displayColors: false,
                      intersect: false,
                      mode: 'index',
                      caretPadding: 10,
                      callbacks: {
                        label: function(tooltipItem, chart) {
                          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                          return datasetLabel + ': Rs' + number_format(tooltipItem.yLabel);
                        }
                      }
                    }
                  }
                });
              })
              .catch(function (error) {
              console.log(error)
              });

  </script>


              
@endpush