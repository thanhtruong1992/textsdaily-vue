@extends('layouts.app')

@section('title', 'Login')

@section('script')
<script src=<?php echo 'https://www.google.com/recaptcha/api.js?hl=' . session('language') ?>></script>
<script type="text/javascript">
    function recaptchaCallback() {
        $('#btn-unsubscribe').removeAttr('disabled');
    };
</script>
@endsection

@section('content')
<div class="register">
    <div class="content">
        <div>
            <img class="logo" alt="" src="{{ asset('images/textdaily.png')}}"  />
        </div>
        <form action="/unsubscribe" method="post" id="unsubscribeForm" class="form-register">
            <p class="mb-3">
                You are about to optout/unsubscribe from receiving sms from us.
                Please confirm by entering your registered mobile number below.
                Format must include country code without the plus (+) sign e.g. 65123456789
            </p>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="campaign_id" value="{{ $campaign_id }}" />
            <input type="hidden" name="user_id" value="{{ $user_id }}" />
            <div class="form-group">
                <label for="exampleInputEmail1">{{ trans('unsubscribe.phone') }}</label>
                <input
                    type="text"
                    name="phone"
                    data-rule-required="true"
                    data-rule-digits="true"
                    class="form-control form-control-md"
                    placeholder="{{ trans('unsubscribe.phone') }}"
                    data-msg-required="{{ trans('validationForm.username.required') }}"
                />
            </div>
            <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="{{ env('KEY_GOOGLE_CAPTCHA') }}"></div>
            <button type="submit" id="btn-unsubscribe" class="btn btn-register mt-2" disabled>{{ trans('unsubscribe.unsubscribe') }}</button>
        </form>
    </div>
</div>
@endsection