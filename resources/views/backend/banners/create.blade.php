@extends('layouts.backend.app')
@section('title', ucfirst($type).' Banner')

@section('content')
<style type="text/css">
    #map{
        height: 300px !important; 
    }
    
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
                <h3 class="text-primary">{{ucfirst($type)}} Banner</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('banners')}}">Banner</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Banner</li>
                </ol>
            </div>
        </div> 
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <!-- @if($type == 'add')
                            <h4>Fill In Splash Screen Details</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Splash Screen Details</h4>
                        @endif
                        <hr> -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
 
                        <form class="form-material  row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            
                             <div class="form-group col-xxl-3 col-xl-4 col-sm-4  ">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="banner_name" rows="" value="{{old('name', $banner->name)}}" placeholder="Please enter Name">
                            </div>
                               @if($type == 'add')
                                    <div class="form-group col-xxl-3 col-xl-4 col-sm-4">
                                        <label for="image">Image</label><sup class="text-reddit"> *</sup>
                                        <div class="">
                                            <div class="input-group">
                                                <div id="image_preview"><img height="100" width="100" id="previewing" src="{{ URL::asset('images/no-image-available.png')}}"></div>
                                                <input type="file" id="file" required name="image" accept=".jpg, .jpeg, .png" class="form-control">
                                            </div>
                                            <span class="text-muted">Note: Image should be JPG, JPEG, PNG Dimension 500x500.</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group col-xxl-3 col-xl-4 col-sm-4">
                                        <label for="image">Image</label><sup class="text-reddit"> *</sup>
                                        <div class="">
                                            <div class="input-group">
                                                <div id="image_preview">
                                                    <img height="100" width="100" id="previewing" src="@if($banner->image != null){{$banner->image}}@endif">
                                                </div>
                                                <input type="file" id="file" name="image" accept=".jpg, .jpeg, .png" class="form-control">
                                            </div>
                                            <span class="text-muted">Note: Image should be JPG, JPEG, PNG Dimension 500x500.</span>
                                        </div>
                                    </div>
                                @endif 
                            
                            <div class="col-12 ">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('banners')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>
                <div class="modal-body">
                    <h5 class="m-t-10 text-danger changeOffer">Are you sure you want to removed  Banner?.</h5>
                    <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            

            $('#statusCat').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusCat").is(':checked'))
                    $('input[name=status]').val('active');
                else
                    $('input[name=status]').val('inactive');
            });

            $(document).on('keyup',".decimalInput, .numberInput",function(e){

                if($(this).val().indexOf('-') >=0){
                    $(this).val($(this).val().replace(/\-/g,''));
                }
            })

            // $(document).find(".numberInput").maskAsNumber({receivedMinus:false});
            // $(document).find(".decimalInput").maskAsNumber({receivedMinus:false,decimals:6});


            $('#changeImage').click(function(){
                $('#catImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphbanner glyphbanner-file fileinput-exists"></i> <span class="fileinput-filename"><i class="fa fa-upload"></i></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" required name="image" accept=".jpg, .jpeg, .png"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#catImage').remove();
                $('#image_exists').val(0);
            });

            @if($type == 'edit')
              <?php /*  $('input[name=splashscreen_name]').rules('add', {remote: APP_NAME + "/admin/splashscreens/checkSplashScreen/{{$splashscreen->id}}"});*/?>
            @endif
            $('.confirmBtn').click(function(){
                $('#confirmDeleteModal').modal('hide');
            });

            $('.cancelBtn').click(function(){
                $('#confirmDeleteModal').modal('hide');
            });

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

//  $(document).on('keyup',".decimalInput, .numberInput",function(e){

//                 if($(this).val().indexOf('-') >=0){
//                     $(this).val($(this).val().replace(/\-/g,''));
//                 }
//             })
     </script>
@endpush
