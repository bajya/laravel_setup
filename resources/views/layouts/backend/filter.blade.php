<?php 
use App\Country;
use App\State;
use App\City;
use App\Region;
use App\Family;
use App\Category;
?>
<form method="POST" id="filter_form" class="fillter-inline" role="form">
    <div class="row">
        <div class="col-12">
            
                <div class="card-header">
                    <!-- <h4 class="card-title">Filters</h4> -->
                    <div class="row">

                        <div class="col-xl-2 col-md-3 col-sm-6">
                            <div class="form-group mb-0">
                                <!-- <label for="from_date">From Date</label> -->
                                <input type="date" id="from_date" name="from_date" class="form-control" placeholder="Enter From Date" value="">
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-sm-0">
                            <div class="form-group mb-0">
                                <!-- <label for="end_date">End Date</label> -->
                                <input type="date" id="end_date" name="end_date" class="form-control" placeholder="Enter End Date" value="">
                            </div>
                        </div>
                        @if(in_array(Request::segment(2), ["roles","users","admins", "states", "countries","cities", "splashscreens","emailtemplate","gifts","vendors","vendorinvoices","subscriptionlist","restaurant_categories","restaurant_ingredients","restaurant_items","vendor_salespersons","kitchens"]))
                            
                            <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                <div class="form-group mb-0">
                                    <input type="text" name="name" id="name" class="form-control name" placeholder="Please Enter Search Keyword">
                                </div>
                            </div>
                            @if(in_array(Request::segment(2), ["emailtemplate","vendor_invoices"]))

                            @else
                                <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                    <div class="form-group mb-0">
                                       
                                        <select class="form-control status" name="status">
                                            <option value="">Select Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(in_array(Request::segment(2), ["transactions"]))

                             <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                <div class="form-group mb-0">
                                    <input type="text" name="title" id="title" class="form-control title" placeholder="Please Enter Search Keyword">
                                </div>
                            </div>

                             <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                    <div class="form-group mb-0">
                                       
                                        <select class="form-control status" name="status">
                                            <option value="">Select Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>  
                        @else
                            
                        @endif

                        @if(in_array(Request::segment(2), ["customerlist"]))

                            <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                <div class="form-group mb-0">
                                    <input type="text" name="name" id="name" class="form-control name" placeholder="Please Enter Search Keyword">
                                </div>
                            </div>
                                <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                    <div class="form-group mb-0">
                                        <select class="form-control type" name="type">
                                            <option value="">Select Type</option>
                                            <option value="Visitor">Visitor</option>
                                            <option value="Purchase">Purchase</option>
                                        </select>
                                    </div>
                                </div>
                        @endif
                         @if(in_array(Request::segment(2), ["vendor_addcustomers"]))

                            <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                <div class="form-group mb-0">
                                    <input type="text" name="name" id="name" class="form-control name" placeholder="Please Enter Search Keyword">
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-3 col-sm-6 mt-2 mt-lg-0">
                                <div class="form-group mb-0">
                                    <select class="form-control type" name="type">
                                        <option value="">Select Type</option>
                                        <option value="Visitor" {{ isset($_GET['type']) && ($_GET['type'] == 'Visitor') ? 'selected' : '' }}>Visitor</option>
                                        <option value="Purchase" {{ isset($_GET['type']) && ($_GET['type'] == 'Purchase') ? 'selected' : '' }}>Purchase</option>
                                        <option value="Repeat">Repeat</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        
                    
                         
                            <div class="col mt-2 d-flex align-items-center mt-lg-0">
                                <div class="form-group flex-column flex-md-row mb-0">
                                    <input type="submit" class="btn btn-success" value="Filter">
                                    <a href="javascript:void(0);" class="btn waves-effect waves-light btn-primary reset">Reset</a>
                                </div>
                            </div>
                    
                </div>
            </div>
        </div>
    </div>
</form>

<script>
     $('#from_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false ,minDate: new Date()}).on('change', function(e, date){
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#end_date').val('');
            });
            $('#end_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });
</script>
