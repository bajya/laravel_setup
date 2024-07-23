@extends('layouts.backend.app')
@section('title', 'Change Password')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Change Password</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Change Password</li>
                </ol>
            </div>
        </div>
        <div class="row w-100">
            <div class="col-lg-12">
                @include('layouts.backend.message')
                <div class="card">
                    <div class="card-body p-3">
                        <!-- <h4 class="mb-4">Change Password</h4>
                        <hr> -->
                        <div class="row">
                            <form class="form-valide col-lg-5" method="post" action="{{route('changepasswordPost')}}">
                                {{csrf_field()}}
                                <div class="form-group">
                                    <label class="label">Old Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" placeholder="*********" name="old-pass" id="old-pass" required>
                                        <div class="input-group-append">
                                           <!--  <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span> -->
                                             <span class="input-group-text" id="togglePassword1">
                                                    <i class="fas fa-eye" id=""></i>
                                            </span>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="pass" placeholder="*********" name="pass" required>
                                        <div class="input-group-append">
                                           <!--  <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span> -->
                                             <span class="input-group-text" id="togglePassword2">
                                                    <i class="fas fa-eye" id=""></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" placeholder="*********" name="confirm-pass" id="confirm-pass" required>
                                        <div class="input-group-append">
                                           <!--  <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span> -->
                                             <span class="input-group-text" id="togglePassword3">
                                                    <i class="fas fa-eye" id=""></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-flat">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    document.getElementById('togglePassword1').addEventListener('click', function () {

        const passwordInput = document.getElementById('old-pass');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    document.getElementById('togglePassword2').addEventListener('click', function () {

        const passwordInput = document.getElementById('pass');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    document.getElementById('togglePassword3').addEventListener('click', function () {

        const passwordInput = document.getElementById('confirm-pass');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    

</script>
@endpush

