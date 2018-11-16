@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="register">
    <div class="content">
        <div>
            <img class="logo" alt="" src="{{ asset('images/textdaily.png')}}"  />
        </div>
        {{-- <form action="login" method="post" id="registerForm" class="form-register">
            <h5 class="title-login">{{ trans("login.title") }}</h5>
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
            <div class="form-group">
                <label for="exampleInputPassword1">{{ trans('login.password') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    data-rule-required="true"
                    class="form-control form-control-sm"
                    placeholder="{{ trans('login.placeholder.password') }}"
                    data-msg-required="{{ trans('validationForm.password.required') }}"
                />
            </div>
            <div class="form-group">
                <a class="forgot-password" href="/forgot-password">I forgot my password?</a>
            </div>
            <button type="submit" id="btn-register" class="btn btn-register">{{ trans('login.login') }}</button>
            <div>
                <span>{{ trans('login.best_view')}}:</span>
                <span>
                    <img src="{{ asset('images/IE.png')}}" />
                    IE 11
                </span>
                <span>
                    <img src="{{ asset('images/firefox.png')}}" />
                    Firefox 57
                </span>
            </div>
        </form> --}}
    </div>
</div>
@endsection