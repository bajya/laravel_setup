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

a.btn.bg-gradient-info.w-100 {
    display: flex;
    justify-content: center;
    align-items: center;
    color: #ffff;
    background: #1d2043 !important;
    border-radius: 0.5rem;
}
</style>
    <div class="container">

        <div class="row justify-content-center"></div>
                @auth
                    @if(Auth::user()->is_admin == 'Yes' )
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
                            <h5>Reset Password</h5>
                        </div> 
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.email') }}"  class="text-start">
                                @csrf
                                <div class="form-group">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email" autofocus>

                                    @error('status')
                                        <span class="valid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="text-center row">
                                    <div class="col-md-6">
                                        <a href="{{ url('/') }}" class="btn bg-gradient-info w-100 ">{{ __('Back') }}</a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn bg-gradient-info w-100 ">{{ __('Send Password Reset Link') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
@endsection
