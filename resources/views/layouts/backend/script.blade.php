<script src="{{URL::asset('/js/vendor.bundle.base.js')}}"></script>
<script src="{{URL::asset('/js/vendor.bundle.addons.js')}}"></script>
<script src="{{URL::asset('/js/off-canvas.js')}}"></script>
<script src="{{URL::asset('/js/misc.js')}}"></script>
<script src="{{URL::asset('/js/jasny-bootstrap.js')}}"></script>

<script src="{{URL::asset('/plugins/bootstrap-switch/bootstrap-switch.min.js')}}"></script>
<script src="{{URL::asset('/plugins/styleswitcher/jQuery.style.switcher.js')}}"></script>
<script src="{{URL::asset('/js/jquery.validate.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('/plugins/nestable/jquery.nestable.js')}}"></script>
<script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
<script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
<script src="{{URL::asset('/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{URL::asset('/plugins/bootstrap-select/bootstrap-select.min.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('/plugins/summernote/summernote.js') }}"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="{{URL::asset('/frontend/js/soft-ui-dashboard.min.js?v=1.0.5') }}"></script>

        @stack('scripts')

        <script type="text/javascript">
          function readNotification($this) {
            var notificationId = $($this).attr('data-id');
            var redirectUrl = $($this).attr('data-url');

            if (notificationId) {
              $.ajax({
                  type: 'get',
                  data: {_method: 'get', _token: "{{ csrf_token() }}"},
                  dataType:'json',
                  url: "{!! url('admin/notifications/readNotification' )!!}" + "/" + notificationId,
                  success:function(res){
                    if(res.status === 1){ 
                      window.location.href = redirectUrl;

                    } else {
                      toastr.error(res.message);
                    }
                  },   
                  error:function(jqXHR,textStatus,textStatus){
                    console.log(jqXHR);
                    toastr.error(jqXHR.statusText)
                  }
              });
            }
          }

          function clearAllNotification() {

            var userId = $('#auth_user_id').val();
            $.ajax({
                type: 'get',
                data: {_method: 'get', _token: "{{ csrf_token() }}", userId: userId},
                dataType:'json',
                url: "{!! url('admin/notifications/clearAllNotification' )!!}",
                success:function(res){
                  if(res.status === 1){ 
                    window.location.reload();

                  } else {
                    toastr.error(res.message);
                  }
                },   
                error:function(jqXHR,textStatus,textStatus){
                  console.log(jqXHR);
                  toastr.error(jqXHR.statusText)
                }
            });
          }
        </script>




        <script type="text/javascript">
            $(".reset").on('click', function(e) {
                 e.preventDefault();
                $(this).closest('form').find("input[type=text], input[type=number], input[type=email], input[type=date], input[type=radio], input[type=checkbox], textarea, select").val("");
              
            });
           /* $.ajaxSetup({
                beforeSend: function(){
                    $(".preloader").fadeOut();
                },
                success: function(){
                    $(".preloader").fadeOut();
                }
            });*/

            $('div.alert').delay(10000).slideUp(500);

            $(".bt-switch input[type='checkbox']:visible").bootstrapSwitch();
            $(".tab-pane .bt-switch input[type='checkbox']").bootstrapSwitch();
            $(".preloader").fadeOut();

            $('body').tooltip({selector: '[data-toggle="tooltip"]'});
            $('body').tooltip({selector: '[data-toggle="popover"]'});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#from_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false }).on('change', function(e, date){
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#end_date').val('');
            });
            $('#end_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#start_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false, minDate: new Date() }).on('change', function(e, date){
                $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
                $('#end_date').val('');
            });
            $('#end_date').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

            $('#auto_deactive').bootstrapMaterialDatePicker({ 
                date: true,
                 shortTime: true,
                 twelvehour: false,
                 format: 'DD/MM/YYYY HH:mm:ss'
            });

            $('.open_time').bootstrapMaterialDatePicker({ 
                date: false,
                 shortTime: true,
                 twelvehour: false,
                 format: 'HH:mm A'
                });
            $('.closed_time').bootstrapMaterialDatePicker({ 
                date: false,
                 shortTime: true,
                 twelvehour: false,
                 format: 'HH:mm A'
                });

            /*$('form').submit(function(e){
                if($(this).valid()){
                    $('button[type="submit"]').prop('disabled',true);
                }else{
                    $('button[type="submit"]').prop('disabled',false);
                }
            });*/
  
        $(document).ready(function () {
    // Remove the "active" class from elements with the class "nav-item"
          $(".logoutAdmin").closest(".nav-item").removeClass("active");
      });
             $(".logoutAdmin").on("click",function(event){
                        event.preventDefault();
                        var type = $(this).attr("type");
                      
                        if(type=="sidebar")
                        {
                            $(".nav-item").removeClass("active");
                            $(this).closest(".nav-item").addClass("active");
                        }
                        else{
                             $(".logoutAdmin").closest(".nav-item").removeClass("active");
                        }

                jQuery.getScript('https://cdn.jsdelivr.net/npm/sweetalert2@11', function() {
                
                Swal.fire({
                  title: 'Logout',
                  text: "Are you sure you want to logout?",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#32388E',
                  cancelButtonColor: '#000',
                  confirmButtonText: 'Yes'
                }).then((result) => {
                  if (result.isConfirmed) {
                    $(this).attr("href","{{ route('logout') }}");
                    document.getElementById('logout-form').submit();
                   
                  } else {
                    console.log('clicked cancel');
                  }
                }) ;   
               });
                });
        </script>

        

      