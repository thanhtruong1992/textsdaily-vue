@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="register">
    <div class="content">
        <div>
            <img class="logo" alt="" src="{{ asset('images/textdaily.png')}}"  />
        </div>
        <form action="{{ route('forgot-passowrd') }}" method="post" id="registerForm" class="form-register">
            <h5 class="title-login">{{ trans("login.forgot_password") }}</h5>
            @if ($message = Session::get('error'))
                <p class="error-register m-b-md">{{ $message }}</p>
            @endif
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label for="exampleInputEmail1">{{ trans('login.username') }}</label>
                <input
                    type="text"
                    name="username"
                    data-rule-required="true"
                    class="form-control form-control-sm"
                    placeholder="{{ trans('login.username') }}"
                    data-msg-required="{{ trans('validationForm.username.required') }}"
                />
            </div>
            <button type="submit" id="btn-register" class="btn btn-register">{{ trans('login.forgot_password') }}</button>
            <div class="form-group">
                <a class="return-user" href="/login">{{ trans("login.back_to_login") }}</a>
            </div>
        </form>
    </div>
</div>
@endsection