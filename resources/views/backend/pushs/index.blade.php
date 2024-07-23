@extends('layouts.backend.app')
@section('title', 'Pushs')

@section('content')
	<div class="content-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Pushs</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Pushs</li>
                </ol>
            </div>
        </div>
            @include("layouts.backend.filter")
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body">
                        <!-- <h4 class="card-title">Pushs</h4>
                        <div class="dt-buttons float-right">
                            <button type="button" class="btn dt-button py-2"  data-toggle="modal" data-target="#pushstatusModal">Add Push Notification</button>
                        </div>
                        <h6 class="card-subtitle">Export data to Excel, PDF</h6> -->
                        <div class="table-responsive">

                            <div class="header_body">
                                <div class="dt-buttons float-right">
                                    <button type="button" class="btn dt-button py-2"  data-toggle="modal" data-target="#pushstatusModal">Add Push Notification</button>
                                </div>
                            </div>
                            <table id="pushsTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Is Send</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Is Send</th>
                                        <th>Created At</th>
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
    <div class="modal fade" id="pushstatusModal" tabindex="-1" role="dialog" aria-labelledby="pushstatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pushstatusModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('addPushs') }}" enctype="multipart/form-data" class="">
                        @csrf
                        <div class="form-group">
                            <label class="required" for="title">Title</label>
                            <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="description">Description</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group">
                            <label class="" for="description">Select User/Vendor</label>
                            <select class="form-control" id="role" name="user_type">
                                 <!-- <option value="">Select Option</option> -->
                                <option id="admin" value="admin">User</option>
                                <option id="vendor" value="vendor">Vendor</option>
                            </select>
                        </div>

                        <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">Select All</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">Deselect All</span>
                            </div>
                        
                        <div class="form-group" for="users" id="userLabel">
                            <label class="" for="users" id="userLabel">Users</label>
                            

                            <select class="form-control select2 {{ $errors->has('users') ? 'is-invalid' : '' }}" name="users[]" id="users" multiple style="display: none;">
                                @foreach($users as $id => $user)
                                    <option value="{{ $id }}" {{ in_array($id, old('users', [])) ? 'selected' : '' }}>{{ $user }}</option>
                                @endforeach
                            </select>
                        

                            @if($errors->has('users'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('users') }}
                                </div>
                            @endif
                            <span class="help-block"></span>
                        </div>
                        

                        <div class="form-group">
                            <label class="required" for="vendors" id="vendorLabel" style="display: none;">Vendors</label>
                            
                            <select class="form-control select3 {{ $errors->has('vendors') ? 'is-invalid' : '' }}" name="users[]" id="vendors" multiple style="display: none;">
                                @foreach($vendors as $id => $vendor)
                                    <option value="{{ $id }}" {{ in_array($id, old('vendors', [])) ? 'selected' : '' }}>{{ $vendor }}</option>
                                @endforeach
                            </select>

                            @if ($errors -> has('vendors'))
                                <div class="invalid-feedback">
                                    {{ $errors-> first('vendors')}}
                                </div>
                            @endif
                            <span class="help-block"></span>
                        </div >
                        


                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                Send
                            </button>
                        </div>

                        <div class="loading" style="display: none;">
                            Loading...
                        </div>
                    </form>
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
        /*
        $(document).ready(function(){
            $(".deselect-all").css('pointer-events','none');
            $(".select-all").click(function(){
                if($(".select2").find('option').prop("selected",true)){
                    $(".deselect-all").css('pointer-events','');
                } else { 
                    $(".deselect-all").css('pointer-events','none');
                }
            });
            $(".deselect-all").click(function(){
                
                $(".deselect-all").css('pointer-events','none');
            });
        });*/
    
    $(document).ready(function() {
   

     // Update visibility on page load
    updateVisibility($("#role").val());

    // Change event handler for the #role dropdown
    $('#role').change(function() {
        var selectedRole = $(this).val();
        updateVisibility(selectedRole);
    });

    // Trigger change event on page load to set initial visibility
    // Default to showing users
    // $('#role').val('admin').trigger('change');
     // Function to hide/show elements based on selected role
    function updateVisibility(selectedRole) {

         $('#userLabel, #users, #vendorLabel, #vendors').hide();

        if (selectedRole === 'admin') {
            $('#userLabel, #users').show();
             $('#vendorLabel, #vendors').hide();
            $('#users').select2({
                placeholder: 'Select User',
            });
        } else if (selectedRole === 'vendor') {
            $('#vendorLabel, #vendors').show();
             $('#userLabel, #users').hide();
            $('#vendors').select3({
                placeholder: 'Select Vendor',
            });
        }
    }

   
});

 $(document).ready(function() {
            $(".deselect-all").css('pointer-events', 'none');
            $(".select-all").click(function() {
                if ($("#role").val() === 'admin') {
                    $("#vendors").val(null).trigger('change');
                    $(".deselect-all").css('pointer-events', '');
                } else if ($("#role").val() === 'vendor') {
                    $("#users").val(null).trigger('change');
                    $(".deselect-all").css('pointer-events', '');
                }
            });

            $(".deselect-all").click(function() {
                $(".deselect-all").css('pointer-events', 'none');
            });
        });

$(document).ready(function() {
    $(".select-all").click(function() {
        $(".select2").val($(".select2 option").map(function() {
            return $(this).val();
        }).get()).trigger('change');
         $(".select3").val($(".select3 option").map(function() {
            return $(this).val();
        }).get()).trigger('change');
    });

    $(".deselect-all").click(function() {
        $(".select2").val(null).trigger('change');
        $(".select3").val(null).trigger('change');
    });
});
        
          


    </script>
    <script>
    $(document).ready(function() {
        var showChar = 100;
        var ellipsestext = "...";
        var moretext = "more";
        var lesstext = "less";
        $('.more').each(function() {
            var content = $(this).html();

            if(content.length > showChar) {

                var c = content.substr(0, showChar);
                var h = content.substr(showChar-1, content.length - showChar);

                var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                $(this).html(html);
            }

        });

        $(".morelink").click(function(){
            if($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    });
    </script>
    <script type="text/javascript">
    	$(function(){
            var table;
            function myDataTableFunction(){
                $(".loading").show();
                    table = $('#pushsTable').DataTable({
                    "ajax": {
                        url:"{{route('pushsAjax')}}",
                        dataSrc:"data",
                        type:"POST",
                        data:{
                              from_date: $('input[name=from_date]').val(),
                            end_date: $('input[name=end_date]').val(),
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
                        { "data": "sno" },
                        { "data": "title" },
                        { "data": "description" },
                        { "data": "is_send" },
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
                            exportOptions: {columns: '1,2,3,4'},
                            pageSize: 'LETTER',
                            customize: function(doc, config) {
                                doc.pageOrientation = 'landscape';
                            }
                        },
                        {extend: 'excel',exportOptions: {columns: '1,2,3,4'}},
                    ],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    "columnDefs": [
                        {"targets": [0,5],"orderable": false},
                        {"targets": [], visible: false}
                    ],
                    "aaSorting": [],
                });
            }
            if ($.fn.DataTable.isDataTable("#pushsTable")) {
                $('#pushsTable').DataTable().clear().destroy();
            }
            myDataTableFunction();
            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                if ($.fn.DataTable.isDataTable("#pushsTable")) {
                  $('#pushsTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
            $(".reset").on('click', function(e) {
                 e.preventDefault();
                $(this).closest('form').find("input[type=text], input[type=number], input[type=email], input[type=radio], input[type=checkbox], textarea, select").val("");
               
                if ($.fn.DataTable.isDataTable("#pushsTable")) {
                  $('#pushsTable').DataTable().clear().destroy();
                }
                myDataTableFunction();
            });
    	});
    </script>
@endpush