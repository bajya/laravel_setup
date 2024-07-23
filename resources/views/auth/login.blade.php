@extends('layouts.backend.app')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">-->


@section('content')
<link id="pagestyle" href="http://44.221.149.95/family_tree/frontend/css/soft-ui-dashboard.css?v=1.0.6" rel="stylesheet" /> 
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

    .input-group {
    position: relative;
}

.input-group-text {
    cursor: pointer;
}

/* Style the password input field */
#password {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

/* Optional: Add padding and margin to the input field */
.form-control {
    padding-right: 40px; /* Space for the eye icon */
    margin-right: -10px; /* Adjust spacing */
}
.logo-cls {
    background: #f1f1f1;
    border-radius: 15px 15px 0 0;
    text-align: center;
    padding: 10px 0;
}

.logo-cls img {
    max-width: 220px;
}

.login-card.card {
    max-width: 550px;
    margin: 10px auto;
}
.login-card.card .card-header {
    padding: 10px 15px 0 !important;
    text-align: left !important;
    border-bottom: 0px;
}
.login-card.card .card-header h5 {
    font-size: 22px !important;
    font-weight: 600;
    margin-bottom: 10px;
}
.login-card.card .card-body {
    padding: 0px 15px 10px;
    box-shadow: none;
}
.remember_me_main {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-check .form-check-label {
    font-size: 13px;
    line-height: 1.5;
    padding-left: 22px;
}
.form-check.form-switch {
    padding: 0px;
    margin:0px
}
button.btn.bg-gradient-info {
    display: flex;
    justify-content: center;
    align-items: center;
    color: #ffff;
    background: #32388E !important;
    transform: unset !important;
    text-transform: uppercase;
    box-shadow: 0 4px 7px -1px rgba(0, 0, 0, 0.11), 0 2px 4px -1px rgba(0, 0, 0, 0.07);
        border-radius: 0.5rem;
}
.mask.bg-gradient-dark {
  background-image: unset;
  background: #6c7380;
}
.mask {
    position: absolute;
    background-size: cover;
    background-position: center center;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.8;
}
.remember_right a.btn {
    margin: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    padding: 0;
    color: #1a56d7 !important;
    transform: unset !important;
    border-bottom: solid 1px #1a56d7;
    border-radius: 0 !important;
    text-transform: capitalize;
    font-weight: 600;
    min-height: auto;
    font-size: 12px
}
.remember_right {
    margin-left: auto;
}
.main-panel{
        width: 66%;
}

</style>
<span class="mask bg-gradient-dark opacity-6"></span>
    <div class="container">
                @auth
                    @if(Auth::user()->is_admin == 'Yes' && Auth::user()->role == 'Vendor')
                        <a href="{{ route('dashboard') }}" class="dashBoard">Dashboard</a>
                    @else
                        <a href="{{ route('home') }}" class="dashBoard">Home</a>
                    @endif
                @else
                    <div class="login-card card">
                        <div class="logo-cls">
                            <img src="{{ asset('images/offerlogo.gif') }}" style=""> 
                        </div>
                        <div class="card-header text-center pt-4">
                            <h5>Sign in</h5>
                        </div> 
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}" class="text-start">
                                @csrf
                                <div class="mb-3">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="<?php if(!empty($_COOKIE['admin_email'])) {echo $_COOKIE['admin_email']; }else { echo old('email'); }  ?>" required autocomplete="email" autofocus placeholder="Email Address">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                     <div class="form-group">
                                    <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" value="<?php if(!empty($_COOKIE['admin_password'])) {echo $_COOKIE['admin_password']; }else { echo old('password'); } ?>" placeholder="Password">
                                      <span class="input-group-text"  id="togglePassword1" style="cursor: pointer;"><i class="fas fa-eye" id="EyeIcon"></i></span>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                </div>

                                </div>
                                <div class="remember_me_main">
                                    <div class="remember_left">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember" <?php if(!empty($_COOKIE['admin_remember']) ) { echo "checked";} ?> >
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="remember_right">
                                    @if (Route::has('password.request'))
                                        <!-- <div class="mb-2 position-relative text-center">
                                            <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">or</p>
                                        </div> -->
                                        <div class="text-center">
                                            <a class="btn bg-gradient-dark w-100 mt-2 mb-4" href="{{ route('password.request') }}">Forgot password?</a>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-info w-100 my-4 mb-2">Sign in</button>
                                </div>
                                <?php /*@if (Route::has('register'))
                                    <div class="mb-2 position-relative text-center">
                                        <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">or</p>
                                    </div>
                                    <div class="text-center">
                                        <a class="btn bg-gradient-dark w-100 mt-2 mb-4" href="{{ route('register') }}">Register</a>
                                    </div>
                                @endif */?>

                            </form>
                        </div>
                    </div>
                @endauth
            </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script type="text/javascript">

    $(document).ready(function() {
        $("#togglePassword1").on("click",function(){
       const passwordField = $('#password');
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
