
@extends('layouts.frontend.app')

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
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 offset-xl-8">
                @auth
                    @if(Auth::user()->is_admin == 'Yes' )
                        <a href="{{ route('dashboard') }}" class="dashBoard">Dashboard</a>
                    @else
                        <a href="{{ route('home') }}" class="dashBoard">Home</a>
                    @endif
                @else
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            
                            <h5>{{ __('Confirm Password') }}</h5>
                        </div> 
                        <div class="card-body">
                            {{ __('Please confirm your password before continuing.') }}
                            <form method="POST" action="{{ route('password.confirm') }}" class="text-start">
                                @csrf
                                <div class="mb-3">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-info w-100 my-4 mb-2">{{ __('Confirm Password') }}</button>
                                </div>
                                @if (Route::has('password.request'))
                                    <div class="mb-2 position-relative text-center">
                                        <p class="text-sm font-weight-bold mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">or</p>
                                    </div>
                                    <div class="text-center">
                                        <a class="btn bg-gradient-dark w-100 mt-2 mb-4" href="{{ route('password.request') }}">Forgot your password?</a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endsection

