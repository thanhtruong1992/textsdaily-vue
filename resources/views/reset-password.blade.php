@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="register">
    <div class="content">
        <div>
            <img class="logo" alt="" src="{{ asset('images/textdaily.png')}}"  />
        </div>
        <form action="{{ route('reset-password') }}" method="post" id="resetPassword" class="form-register">
            <h5 class="title-login">{{ trans("login.change_password") }}</h5>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="token_reset" value="{{ $token_reset }}" />
            <div class="form-group">
                <label for="exampleInputPassword1">{!! trans('login.new_password') !!}</label>
                <input
                    id="password"
                    type="password"
                    name="new_password"
                    data-rule-required="true"
                    class="form-control form-control-sm"
                    placeholder="{!! trans('login.placeholder.password') !!}"
                    data-msg-required="{{ trans('validationForm.password.required') }}"
                />
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">{!! trans('login.confirm_password') !!}</label>
                <input
                    type="password"
                    name="confirm_password"
                    data-rule-required="true"
                    data-rule-equalTo="#password"
                    class="form-control form-control-sm"
                    placeholder="{!! trans('login.placeholder.confirm_password') !!}"
                    data-msg-required="{{ trans('validationForm.password.required') }}"
                    data-msg-equalTo="{{ trans('validationForm.password.confirmed') }}"
                />
            </div>
            <button type="submit" id="btn-register" class="btn btn-register">{{ trans('login.change_password') }}</button>
            <div class="form-group">
                <a class="return-user" href="/login">{{ trans("login.back_to_login") }}</a>
            </div>
        </form>
    </div>
</div>
@endsection