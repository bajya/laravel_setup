@extends('layouts.backend.app')
@section('title', 'Country')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Country</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Country</li>
                </ol>
            </div>
        </div>
               @include("layouts.backend.filter")
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">
                     
                        <div class="table-responsive">
                            <div class="header_body">
                                <div class="delete_btn">
                             <!--   <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('deleteCountriesBulk')}}" class="btn btn-secondary disabled bulkAction deleteCountriesBulk">Delete</a>
                                    </div> -->
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('changeStatusCountriessBulk')}}" class="btn btn-secondary disabled bulkAction changeStatusCountriessBulk">Activate/Deactivate</a>
                                    </div>
                                </div>
                                <div class="dt-buttons float-right">
                                    <a href="{{route('createCountries')}}" class="btn dt-button py-2">Add Country</a>
                                </div>
                            </div>

                            <table id="countriesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="country_ids[]">Select</label></div></th>
                                        <th>Short Name</th>
                                          <th>Phonecode</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                         <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                   <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="country_ids[]">Select</label></div></th>
                                        <th>Short Name</th>
                                        <th>Phonecode</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                         <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

       <div class="modal fade" id="confirmDeleteCountryModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteCountryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteCountryModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="deleteid" id="deleteid">
                    <input type="hidden" name="deleteurl" id="deleteurl">
                    <h5 class="m-t-10 text-danger">Deleting selected Country(s)?</h5>
                    <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info btn-flat confirmDeleteBtn m-b-30 m-t-30">Confirm</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')

    <script src="{{URL::asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
    <script type="text/javascript">
        $(".preloader").show();
    	$(function(){
            var table;
             myDataTableFunction();
             function myDataTableFunction(){
                $(".loading").show();

    		 table = $('#countriesTable').DataTable({
                "ajax": {
                    url:"{{route('countriesAjax')}}",
                    dataSrc:"data",
                   data:{
                            from_date: $('input[name=from_date]').val(),
                            end_date: $('input[name=end_date]').val(),
                            status: $('select[name=status]').val(),
                            search :$('input[name=name]').val(),
                        }
                },
                 paging: true,
                    pageLength: 50,
                   "lengthMenu": [10, 20, 50, 100],
                  lengthChange: true,
                    "bProcessing": true,
                    "bServerSide": true,
                    "bLengthChange": false,
                    'serverMethod': 'post',
                    'searching': false,
                "aoColumns": [
                    { "data": "select" },
                    {"data":'shortname'},
                    {"data":'phonecode'},
                    { "data": "name" },
                  //   { "data": "status" },
                    { "data": "activate" },
                    { "data": "created_at" },

                    { "data": "action" },
                ],
                 "drawCallback": function(settings){
                        $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                        $(".loading").fadeOut();
                    },
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                "columnDefs": [
                    {"targets": [0,4,6],"orderable": false},
                  //   {"targets": [4], visible: false}
                ],
                "aaSorting": [],
		    });
             }

             $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                if ($.fn.DataTable.isDataTable("#countriesTable")) {
                  $('#countriesTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });

             $(".reset").on('click', function(e) {
                 e.preventDefault();
                $(this).closest('form').find("input[type=text], input[type=number], input[type=email], input[type=radio], input[type=checkbox], textarea, select").val("");
               
                if ($.fn.DataTable.isDataTable("#countriesTable")) {
                  $('#countriesTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });

            $(document).on('click','input[name="country_ids[]"]',function(){
                $(document).find('input[name="country_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="country_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="country_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteCountriesBulk').removeClass('disabled');
                    $('.changeStatusCountriessBulk').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteCountriesBulk').addClass('disabled');
                    $('.changeStatusCountriessBulk').addClass('disabled');
                    table.rows().deselect();
                }
            });
             $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="country_id[]"]:checked').length;
                if(length > 0)
                {
                    $('.deleteCountriesBulk').removeClass('disabled');
                    $('.changeStatusCountriessBulk').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteCountriesBulk').addClass('disabled');
                    $('.changeStatusCountriessBulk').addClass('disabled');
                    table.rows().deselect();
                }

            } );

             $('.bulkAction').click(function(){
                var url = $(this).data('href');
                var id = $(this).attr('class');
                var ids = [];
                $.each($('input[name="country_id[]"]:checked'), function(){
                    ids.push($(this).val());
                });
            if (confirm('Are you sure you want to change status?')) {
                $.ajax({
                    type: "post",
                    url: url,
                    data: {ids: ids},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            table.ajax.reload();
                            

                            $.each($('input[name="country_id[]"]:checked'), function(){
                                $(this).prop('checked', false);
                            });
                            $.each($('input[name="country_ids[]"]:checked'), function(){
                                $(this).prop('checked', false);
                            });

                            toastr.success(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                        else

                        {
                            toastr.error(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }

                        $('.deleteCountriesBulk').addClass('disabled');
                        $('.changeStatusCountriessBulk').addClass('disabled');
                    },
                    error: function(data)
                    {
                        toastr.error("Unable to update users.","Status",{
                            timeOut: 5000,
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": true,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut",
                            "tapToDismiss": false

                        });

                    }
                })
            }
                $(this).blur();
            });//Close Bulk Action
            
            //Active InActive Change
            $(document).on('switchChange.bootstrapSwitch', '.statusCountry', function (event, state) {
                  if (confirm('Are you sure you want to change status?')) {
                var x;
                if($(this).is(':checked'))
                    x = 'active';
                else
                    x = 'inactive';

                var id = $(this).data('id');
                $('#statuscode').text($(this).data('code'));
                if(x == 'inactive')
                {
                    $('#statusid').val(id);
                    $('#status').val('inactive');
                    //$('.usersstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('active');
                    //$('.usersstatus').text('activate');
                }
                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxCountries')}}",
                    data: {statusid: id,status:x},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            table.ajax.reload();
                            toastr.success(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                    },
                    error: function(data)
                    {

                        toastr.error("Unable to update user.","Status",{
                            timeOut: 5000,
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": true,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut",
                            "tapToDismiss": false

                        });

                    }
                });
                }else{
                    $(this).bootstrapSwitch('state', !state, true);
                }
            });
            //Close Active inActive
            
            //Single Delete Button
             $(document).on('click','.deleteCounties',function(){
                var id = $(this).data('id');
                $('#deleteid').val(id);
                $('#deleteurl').val("{{route('deleteCounties')}}");

                $('#confirmDeleteCountryModal').modal('show');
            });

            //Single Confirm Delete
             $(document).on('click', '.confirmDeleteBtn', function (event, state) {

                $.ajax({
                    type: "post",
                    url: $('#deleteurl').val(),
                    data: {deleteid: $('#deleteid').val()},
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            toastr.success(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                            table.ajax.reload();
                            $('.deleteCountriesBulk').addClass('disabled');
                            $('.changeStatusCountriessBulk').addClass('disabled');
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                    },
                    error: function(data)
                    {
                        toastr.error("Unable to delete country.","Status",{
                            timeOut: 5000,
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": true,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": true,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut",
                            "tapToDismiss": false
                        });
                    }
                });
                $('#confirmDeleteCountryModal').modal('hide');
            }); 



    	});
    </script>
@endpush