@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="register">
        <div class="show-message">
            @include('flash-message')
        </div>
        <div class="content">
            <form action="/register" method="post" id="registerForm" class="form-register">
                <h5>{{ trans('register.title') }}</h5>
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="exampleInputEmail1">{!! trans('register.name') !!}</label>
                    <input
                        type="text"
                        name="name"
                        data-rule-required="true"
                        class="form-control form-control-sm"
                        data-msg-required="{{ trans('validationForm.name.required') }}"
                        placeholder="{!! trans('register.placeholder.name') !!}"
                    />
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">{!! trans('register.email') !!}</label>
                    <input
                        type="email"
                        name="email"
                        data-rule-email="true"
                        data-rule-required="true"
                        class="form-control form-control-sm"
                        placeholder="{!! trans('register.placeholder.email') !!}"
                        data-msg-email="{{ trans('validationForm.email.email') }}"
                        data-msg-required="{{ trans('validationForm.email.required') }}"
                    />
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">{!! trans('register.password') !!}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        data-rule-required="true"
                        class="form-control form-control-sm"
                        placeholder="{!! trans('register.placeholder.password') !!}"
                        data-msg-required="{{ trans('validationForm.password.required') }}"
                    />
                </div>
                <button type="submit" id="btn-register" class="btn btn-register">{!! trans('register.register') !!}</button>
            </form>
        </div>
    </div>
@endsection