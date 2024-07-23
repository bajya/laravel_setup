@extends('layouts.backend.app')
@section('title', 'Edit Email Template - '.$emailtemplate->name)

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Email Template</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('emailtemplate')}}">Email Template</a></li>
                    <li class="breadcrumb-item active">Edit Email Template</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.backend.message')
                    <div class="card-body p-3">

                        <h4>Edit {{$emailtemplate->name}} Content</h4>
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

                        <form class="form-material  row form-valide" method="post" action="{{route('updateemailtemplate',['id'=>$emailtemplate->id])}}" enctype="multipart/form-data">

                            {{csrf_field()}}

                         
                                <div class="form-group col-md-12 ">
                                    <label>Name</label><sup class="text-reddit"> *</sup>
                                    <input type="Text"  class="form-control form-control-line" name="name" value="{{old($emailtemplate->name, $emailtemplate->name)}}">
                                </div>
                                <div class="form-group col-md-12 ">
                                    <label>Subject</label><sup class="text-reddit"> *</sup>
                                    <input type="Text"  class="form-control form-control-line" name="subject" value="{{old($emailtemplate->subject, $emailtemplate->subject)}}">
                                </div>
                                <?php /* <div class="form-group col-md-12 ">
                                    <label>Footer</label><sup class="text-reddit"> *</sup>
                                    <input type="Text"  class="form-control form-control-line" name="footer" value="{{old($emailtemplate->footer, $emailtemplate->footer)}}">
                                </div> */?>
                                <div class="form-group col-md-12 ">
                                    <label>Description</label><sup class="text-reddit"> *</sup>
                                    <textarea class="form-control form-control-line" name="description" rows="10">{{old($emailtemplate->description, $emailtemplate->description)}}</textarea>
                                </div>

                       

                            <div class="col-12">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('emailtemplate')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $('textarea[name=description]').summernote({
                height: 350, 
                minHeight: null, 
                maxHeight: null, 
                focus: false, 
                lineWrapping:true,
                prettifyHtml:true,
                callbacks: {
                    onChange: function(contents, $editable) {
                        $('textarea[name=description]').val($('textarea[name=description]').summernote('isEmpty') ? "" : contents);

                        $('form').data('validator').element($('textarea[name=description]'));
                        $('textarea[name=description]').rules('add','check_content');
                        $('textarea[name=description]').valid();
                    }
                }
            });

           

          

           
         

           

            $(document).on('switchChange.bootstrapSwitch', 'input[name^=val-faqstatus]', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                var id = $(this).attr('id').split('_')[1];

                if($(this).is(':checked'))
                    $('input[name=faqstatus_'+id+']').val('active');
                else
                    $('input[name=faqstatus_'+id+']').val('inactive');
            });

        });
    </script>
@endpush