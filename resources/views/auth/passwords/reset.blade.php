@extends('layouts.backend.app')

@section('content')
<style type="text/css">
    .dashBoard {
        background: #007eff;
        padding: 6px 16px;
        border-radius: 50px;
        position: fixed;
        color: #fff;
        top: 20px;
        right: 20px;
        font-size: 15px;
    }
    .dashBoard:hover {
        color: #fff;
    }
    
</style>
    <div class="container">
        <div class="row justify-content-center"></div>
            <!-- <div class="col-xl-4 col-lg-5 col-md-7"> -->
                @auth
                    @if(Auth::user()->is_admin == 'Yes' )
                        <a href="{{ route('dashboard') }}" class="dashBoard">Dashboard</a>
                    @else
                        <a href="{{ route('home') }}" class="dashBoard">Home</a>
                    @endif
                @else
                    <div class="login-card card">
                        <div class="card-header text-center pt-4">
                            
                            <h5>{{ __('Reset Password') }}</h5>
                        </div> 
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update') }}"  class="text-start">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="mb-3">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                                    @error('email')

                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                     <div class="form-group">
                                        <div class="input-group">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="New password">
                                             <span class="input-group-text togglePassword1" data-InputId = "password" id="" style="cursor: pointer;"><i class="fas fa-eye" id="EyeIcon"></i></span>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                     </div>

                                    
                                </div>
                                <div class="mb-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                         <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
                                           <span class="input-group-text togglePassword1" data-InputId = "password-confirm" id="" style="cursor: pointer;"><i class="fas fa-eye" id="EyeIcon"></i></span>
                                         @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                         </div>
                                     </div>
                                 </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-info w-100 my-4 mb-2">{{ __('Reset Password') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
            <!-- </div> -->
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script type="text/javascript">

    $(document).ready(function() {
        $(".togglePassword1").on("click",function(){
            var InputId  = $(this).attr("data-InputId");
         
       const passwordField = $('#'+InputId);
            const icon = $(this).find('i');
                if(passwordField.attr('type')==="password"){
                    passwordField.attr('type', 'text');
                     icon.removeClass('fa-eye');
                     icon.addClass('fa-eye-slash');
                }else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash');
                icon.addClass('fa-eye');
            }
        });
    });
</script>

