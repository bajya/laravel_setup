@extends('layouts.backend.app')
@section('title', 'Tutorial Screens')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Tutorial Screens</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Tutorial Screens</li>
                </ol>
            </div>
        </div>
        <?php /*
        <form action="" method="get" id="filter_form">
        @include("layouts.backend.filter")
        </form> */?>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body">
                        <!-- <h4 class="card-title">Splash Screens</h4>

                            <div class="dt-buttons float-right">
                                <a href="{{route('createSplashScreen')}}" class="btn dt-button py-2">Add Splash Screen</a>
                            </div> -->
                        
                        <div class="table-responsive">
                            <div class="header_body">
                                <div class="delete_btn">
                                  <?php /*  <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('deleteSplashScreens')}}" class="btn btn-secondary disabled bulkAction deleteSplashScreens">Delete</a>
                                    </div> */?>
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('changeStatusTutorialScreens')}}" class="btn btn-secondary disabled bulkAction changeStatusTutorialScreens">Activate/Deactivate</a>
                                    </div>
                                </div>
                                <div class="dt-buttons float-right">
                                    <a href="{{route('createTutorialScreen')}}" class="btn dt-button py-2">Add Tutorial Screen</a>
                                </div>
                            </div>
                            <table id="tutorialscreensTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="tutorialscreen_ids[]">Select</label></div></th>
                                        <th>S. No.</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="tutorialscreen_ids[]">Select</label></div></th>
                                        <th>S. No.</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Status</th>
                                        <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <!-- <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('deleteSplashScreens')}}" class="btn btn-secondary disabled bulkAction deleteSplashScreens">Delete</a>
                        </div>
                        <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('changeStatusSplashScreens')}}" class="btn btn-secondary disabled bulkAction changeStatusSplashScreens">Activate/Deactivate</a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="userstatusModal" tabindex="-1" role="dialog" aria-labelledby="userstatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userstatusModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusTutorialScreen')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="tutorialscreenstatus"></span> Tutorial Screen : <span id="statuscode"></span></h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- <form class="form-valide" method="post" id="deleteForm" action="{{route('deleteTutorialScreen')}}"> --}}
                        {{csrf_field()}}
                        <input type="hidden" name="deleteid" id="deleteid">
                        <input type="hidden" name="deleteurl" id="deleteurl">
                        <h5 class="m-t-10 text-danger">Are you sure you want to delete selected Tutorial Screen?</h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info btn-flat confirmDeleteBtn m-b-30 m-t-30">Confirm</button>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script src="{{URL::asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('/js/datatable-pipeline.js')}}"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript">
    	$(function(){
        	var table;
            function myDataTableFunction(){
                $(".loading").show();
                table = $('#tutorialscreensTable').DataTable({
                    "ajax": {
                        url: "{{route('tutorialscreenAjax')}}",
                        dataSrc:"data",
                        type:"POST",
                        data:{
                            from_date: $('input[name=from_date]').val(),
                            end_date: $('input[name=end_date]').val(),
                            status: $('select[name=status]').val(),
                            name: $('input[name=name]').val(),
                        }
                        // type: "get"
                    },
                    paging: true,
                    pageLength: 50,
                    "lengthMenu": [10, 20, 50, 100],
                    lengthChange: true,
                    "bProcessing": true,
                    "bServerSide": true,
                    "bLengthChange": true,
                    'serverMethod': 'post',
                    'searching': false,
                    "aoColumns": [
                        { "data": "select" },
                        { "data": "sno" },
                        { "data": "image" },
                        { "data": "name" },
                        { "data": "description" },
                        { "data": "status" },
                        { "data": "activate" },
                        { "data": "created_at" },
                        { "data": "action" },
                    ],
                    "drawCallback": function(settings){
                        $(".bt-switch input[type='checkbox']").bootstrapSwitch();
                        $(".loading").fadeOut();
                    },

    		        dom: 'Bfrtip',
                    buttons: [
                        'pageLength',
                        {
                            extend: 'pdf',
                            exportOptions: {columns: '1,2,3,4,5,6'},
                            pageSize: 'LETTER',
                            customize: function(doc, config) {
                                doc.pageOrientation = 'landscape';
                            }
                        },
                        {extend: 'excel',exportOptions: {columns: '1,2,3,4,5,6'}},
                    ],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    "columnDefs": [
                        {"targets": [0,1,5,6,8],"orderable": false},
                        {"targets": [1,5], visible: false}
                    ],
                    "aaSorting": [],
    		    });
            }
            if ($.fn.DataTable.isDataTable("#tutorialscreensTable")) {
                $('#tutorialscreensTable').DataTable().clear().destroy();
            }
            myDataTableFunction();
            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                if ($.fn.DataTable.isDataTable("#tutorialscreensTable")) {
                  $('#tutorialscreensTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
            $(".reset").on('click', function(e) {
                 e.preventDefault();
                $(this).closest('form').find("input[type=text], input[type=number], input[type=email], input[type=radio], input[type=checkbox], textarea, select").val("");
               
                if ($.fn.DataTable.isDataTable("#tutorialscreensTable")) {
                  $('#tutorialscreensTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
            $(document).on('click','input[name="tutorialscreen_ids[]"]',function(){
                $(document).find('input[name="tutorialscreen_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="tutorialscreen_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="tutorialscreen_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteTutorialScreens').removeClass('disabled');
                    $('.changeStatusTutorialScreens').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteTutorialScreens').addClass('disabled');
                    $('.changeStatusTutorialScreens').addClass('disabled');
                    table.rows().deselect();
                }
            });

            $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="tutorialscreen_id[]"]:checked').length;
                if(length > 0)
                {
                    $('.deleteTutorialScreens').removeClass('disabled');
                    $('.changeStatusTUtorialScreens').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteTutorialScreens').addClass('disabled');
                    $('.changeStatusTutorialScreens').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="tutorialscreen_id[]"]',function(){
                var length = $('input[name="tutorialscreen_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteTutorialScreens').removeClass('disabled');
                    $('.changeStatusTutorialScreens').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteTutorialScreens').addClass('disabled');
                    $('.changeStatusTutorialScreens').addClass('disabled');
                    table.row(index).deselect();
                }

                if($(this).prop('checked'))
                {
                    table.row(index).select();
                }else{
                    table.row(index).deselect();
                }

                if(length == $('input[name="tutorialscreen_id[]"]').length){
                    $(document).find('input[name="tutorialscreen_ids[]"]').prop('checked', true);
                }else{
                    $(document).find('input[name="tutorialscreen_id[]"]').prop('checked', false);
                }

            });

            $(document).on('switchChange.bootstrapSwitch', '.statusTutorialScreen', function (event, state) {
                 if (confirm('Are you sure you want to change status?')) {
                var x;

                if($(this).is(':checked'))
                    x = 'active';
                else
                    x = 'inactive';

                var id = $(this).data('id');
                if(x == 'inactive')
                {
                    $('#statusid').val(id);
                    $('#status').val('inactive');
                    $('.tutorialscreenstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('active');
                    $('.tutorialscreenstatus').text('activate');
                }

                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxTutorialScreen')}}",
                    data: {statusid: id,status:x},
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
                            table.clearPipeline();
                            table.ajax.reload();
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

                        toastr.error("Unable to update Tutorial Screen.","Status",{
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


            $(document).on('click','.deleteTutorialScreen',function(){
                var id = $(this).data('id');
                $('#deleteid').val(id);
                $('#deleteurl').val("{{route('deleteTutorialScreen')}}");

                $('#confirmDeleteModal').modal('show');
            });


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
                            table.clearPipeline();
                            table.ajax.reload();
                            $('.deleteTutorialScreens').addClass('disabled');
                            $('.changeStatusTutorialScreens').addClass('disabled');
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

                        toastr.error("Unable to delete Tutorial Screen.","Status",{
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
                    $('#confirmDeleteModal').modal('hide');
            });


            $('.bulkAction').click(function(){
                var url = $(this).data('href');
                var id = $(this).attr('class');
                var ids = [];
                $.each($('input[name="tutorialscreen_id[]"]:checked'), function(){
                    ids.push($(this).val());
                });

                if(url.includes('delete')){
                    $('#deleteid').val(ids.join(','));
                    $('#deleteurl').val(url);
                    $('#confirmDeleteModal').modal('show');
                }
                else{
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
                                    table.clearPipeline();
                                    table.ajax.reload();

                                    $.each($('input[name="tutorialscreen_id[]"]:checked'), function(){
                                        $(this).prop('checked', false);
                                    });
                                    $.each($('input[name="tutorialscreen_ids[]"]:checked'), function(){
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

                                $('.deleteTutorialScreens').addClass('disabled');
                                $('.changeStatusTutorialScreens').addClass('disabled');
                            },
                            error: function(data)
                            {

                                toastr.error("Unable to update Tutorial Screens.","Status",{
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
                }
                $(this).blur();
            })

    	});
    </script>
@endpush