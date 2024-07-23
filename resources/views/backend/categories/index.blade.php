@extends('layouts.backend.app')
@section('title', 'Category')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Category</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Category</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form action="" method="get" id="filter_form">
                        @include("layouts.backend.filter")
                        </form>
                    @include('layouts.backend.message')
                    <div class="card-body">
                        <!-- <h4 class="card-title">Users</h4>
                        <div class="dt-buttons float-right">
                            <a href="{{route('createUsers')}}" class="btn dt-button py-2">Add User</a>
                        </div> -->
                        <!-- <h6 class="card-subtitle">Export data to Excel, PDF</h6> -->
                        <div class="table-responsive">
                    

                            <div class="header_body">
                                <div class="delete_btn">
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('deleteCategoriesBulk')}}" class="btn btn-secondary disabled bulkAction deleteCategoriesBulk">Delete</a>
                                    </div> 
                                    <div class="dt-buttons">
                                        <a href="javascript:void(0)" data-href="{{route('changeStatusCategoryBulk')}}" class="btn btn-secondary disabled bulkAction changeStatusUsersBulk">Activate/Deactivate</a>
                                    </div>
                                </div>
                                <div class="dt-buttons">
                                    <a href="{{route('createCategories')}}" class="btn dt-button py-2">Add Category</a>
                                </div>

                            </div>
                           <!--  <div class="dt-buttons">
                                <a href="javascript:void(0)" data-href="{{route('changeStatusUsersBulk')}}" class="btn btn-secondary disabled bulkAction changeStatusUsersBulk">Activate/Deactivate</a>
                            </div> -->
                            <table id="categoryTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="category_ids[]"></label></div></th>
                                         <th>Name</th>
                                        <th>Status</th>
                                         <th>Added Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><div class="form-check form-check-flat selectAll"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="category_ids[]"></label></div></th>
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

                     <!--    <div class="dt-buttons">
                            <a href="javascript:void(0)" data-href="{{route('changeStatusUsersBulk')}}" class="btn btn-secondary disabled bulkAction changeStatusUsersBulk">Activate/Deactivate</a>
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
                    <form class="form-valide" method="post" id="blockForm" action="{{route('changeStatusCategoryBulk')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="statusid" id="statusid">
                        <input type="hidden" name="status" id="status">
                        <h5 class="m-t-10 text-danger">Are you sure you want to <span class="usersstatus"></span> Category : <span id="statuscode"></span></h5>
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
                    <input type="hidden" name="deleteid" id="deleteid">
                    <input type="hidden" name="deleteurl" id="deleteurl">
                    <h5 class="m-t-10 text-danger">Are You sure you delete this category ?</h5>
                    <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info btn-flat confirmDeleteBtn m-b-30 m-t-30">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<input type="hidden" name="gender" id="gender" value="">
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
    	$(function(){
            var table;
            function myDataTableFunction(){
                $(".loading").show();
                    table = $('#categoryTable').DataTable({
                    "ajax": {
                        url:"{{route('categoriesAjax')}}",
                        dataSrc:"data",
                        type:"POST",
                        data:{
                            from_date: $('input[name=from_date]').val(),
                            end_date: $('input[name=end_date]').val(),
                            status: $('select[name=status]').val(),
                            search: $('input[name=name]').val(),
                            
                        }
                        // type: "get"
                    },
                    paging: true,
                    pageLength: 500,
                    "bProcessing": true,
                    "bServerSide": true,
                    "bLengthChange": true,
                    'serverMethod': 'post',
                    'searching': false,
                    "aoColumns": [
                        { "data": "select" },
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

                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'pdf',
                            exportOptions: {columns: '1,2,3'},
                            pageSize: 'LETTER',
                            customize: function(doc, config) {
                                doc.pageOrientation = 'landscape';
                            }
                        },
                        {extend: 'excel',exportOptions: {columns: '1,2,3'}},
                    ],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    "columnDefs": [
                        {"targets": [0,4],"orderable": false},
                        // {"targets": [5], visible: false}
                    ],
                    "aaSorting": [],
                });
            }
            if ($.fn.DataTable.isDataTable("#categoryTable")) {
                $('#categoryTable').DataTable().clear().destroy();
            }
            myDataTableFunction();
            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                if ($.fn.DataTable.isDataTable("#categoryTable")) {
                  $('#categoryTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
            $(".reset").on('click', function(e) {
                 e.preventDefault();
                $(this).closest('form').find("input[type=text], input[type=number], input[type=email], input[type=radio], input[type=checkbox], textarea, select").val("");
               
                if ($.fn.DataTable.isDataTable("#categoryTable")) {
                  $('#categoryTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
            $(document).on('click','input[name="category_ids[]"]',function(){
                $(document).find('input[name="category_ids[]"]').prop('checked', $(this).prop('checked'));
                $(document).find('input[name="category_id[]"]').prop('checked', $(this).prop('checked'));
                var length = $('input[name="category_id[]"]:checked').length;

                if(length > 0)
                {
                    $('.deleteCategoriesBulk').removeClass('disabled');
                    $('.changeStatusUsersBulk').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteCategoriesBulk').addClass('disabled');
                    $('.changeStatusUsersBulk').addClass('disabled');
                    table.rows().deselect();
                }
            });

            $('tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
                var check = $(this).find('input[type=checkbox]');
                check.prop('checked',!check.prop("checked"));
                var length = $('input[name="category_id[]"]:checked').length;
                if(length > 0)
                {
                    $('.deleteCategoriesBulk').removeClass('disabled');
                    $('.changeStatusUsersBulk').removeClass('disabled');
                    table.rows().select();
                }else{
                    $('.deleteCategoriesBulk').addClass('disabled');
                    $('.changeStatusUsersBulk').addClass('disabled');
                    table.rows().deselect();
                }

            } );

            $(document).on('click','input[name="category_id[]"]',function(){
                var length = $('input[name="category_id[]"]:checked').length;
                var row = $(this).closest('tr');
                var index = row.index();

                if(length > 0)
                {
                    $('.deleteCategoriesBulk').removeClass('disabled');
                    $('.changeStatusUsersBulk').removeClass('disabled');
                    table.row(index).select();
                }else{
                    $('.deleteCategoriesBulk').addClass('disabled');
                    $('.changeStatusUsersBulk').addClass('disabled');
                    table.row(index).deselect();
                }
                if($(this).prop('checked'))
                {
                    table.row(index).select();
                }else{
                    table.row(index).deselect();
                }
                if(length == $('input[name="category_id[]"]').length){
                    $(document).find('input[name="category_ids[]"]').prop('checked', true);
                }else{
                    $(document).find('input[name="category_ids[]"]').prop('checked', false);
                }
            });
            $(document).on('switchChange.bootstrapSwitch', '.statusCategory', function (event, state) {
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
                    $('.usersstatus').text('deactivate');
                }
                else
                {
                    $('#statusid').val(id);
                    $('#status').val('active');
                    $('.usersstatus').text('activate');
                }
                $.ajax({
                    type: "post",
                    url: "{{route('changeStatusAjaxCategories')}}",
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

                        toastr.error("Unable to update Category.","Status",{
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
                // $('#userstatusModal').modal('show');
                }else{
                    $(this).bootstrapSwitch('state', !state, true);
                }
            });


            
            $(document).on('click','.deletecategories',function(){
                var id = $(this).data('id');
                $('#deleteid').val(id);
                $('#deleteurl').val("{{route('deletecategories')}}");

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
                            table.ajax.reload();
                            $('.deleteCategoriesBulk').addClass('disabled');
                            $('.changeStatusUsers').addClass('disabled');
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
                        toastr.error("Unable to delete Category.","Status",{
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
                $.each($('input[name="category_id[]"]:checked'), function(){
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
                                    table.ajax.reload();
                                    /*var ids = JSON.parse(data.ids);
                                    for(var i = 0; i < ids.length; i++)
                                    {
                                        if(id.includes('delete'))
                                            $('tr[data-id='+ids[i]+']').remove();
                                        else{
                                            var status = $('tr[data-id='+ids[i]+'] td:nth-child(7)').text();

                                            if(status == 'Active'){
                                                $('tr[data-id='+ids[i]+'] td:nth-child(7)').text('Inactive');
                                                $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').attr('data-original-title', 'Activate User').tooltip();
                                                $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').find('.fa').removeClass('fa-lock').addClass('fa-unlock');
                                            }
                                            else{
                                                $('tr[data-id='+ids[i]+'] td:nth-child(7)').text('Active');
                                                $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').attr('data-original-title', 'Deactivate User').tooltip();
                                                $('tr[data-id='+ids[i]+'] td:nth-child(8)').find('.changeStatus').find('.fa').removeClass('fa-unlock').addClass('fa-lock');
                                            }
                                        }
                                    }*/

                                    $.each($('input[name="category_id[]"]:checked'), function(){
                                        $(this).prop('checked', false);
                                    });
                                    $.each($('input[name="category_ids[]"]:checked'), function(){
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

                                $('.deleteCategoriesBulk').addClass('disabled');
                                $('.changeStatusUsersBulk').addClass('disabled');
                            },
                            error: function(data)
                            {
                                toastr.error("Unable to update category.","Status",{
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
            }
            })
    	});
    </script>
@endpush