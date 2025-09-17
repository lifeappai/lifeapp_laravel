@extends('layouts.master')
@section('content')
    <div class="my-auto page page-h">
        <div class="main-signin-wrapper">
            <div class="main-card-signin d-md-flex wd-100p">
                <div class="wd-md-50p login d-none d-md-block page-signin-style p-5 text-white">
                    <div class="my-auto authentication-pages">
                        <div>
                            <img src="{{ asset('assets/img/brand/logo.png') }}" class=" m-0 mb-4" alt="logo">
                        </div>
                    </div>
                </div>
                <div class="p-5 wd-md-50p">
                    @if (session('error'))
                        <h6 class="alert alert-danger">
                            {{ session('error') }}
                        </h6>
                    @endif
                    <div class="main-signin-header">
                        <form method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div><button class="btn btn-main-primary btn-block" style="background:#c21b3f ">
                                {{ __('Login') }}</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endsection
