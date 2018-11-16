@extends("admins.settings.index")
@section('settingTitle', trans('settings.settingsAccountTitle'))
@section('custom-script')
<script src="{{ asset('js/settings-account.js') }}"></script>
<script type="text/javascript">
window.link = {}
</script>
@endsection
@section("settings-content")
<div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="accountForm"
                    @if($client->isGroup1())
                    action="{{ route('setting.account-update') }}"
                    @else
                    action="{{ route('setting2.account-update') }}"
                    @endif
                    method="post">
                        {{ csrf_field() }}
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('client.full_name') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="name"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.client_name.required') }}"
                                        placeholder="{{ trans('client.full_name') }}"
                                        class="form-control input-custom @if($errors->first('name')) error @endif"
                                        value="{{ old('name', $client->name) }}"/>
                                    <label id="name-error" class="error" for="name">{{ $errors->first('name') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('client.email') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="email"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.email.required') }}"
                                        placeholder="{{ trans('client.email') }}"
                                        class="form-control input-custom @if($errors->first('email')) error @endif"
                                        value="{{ old('email', $client->email) }}" disabled/>
                                    <label id="email-error" class="error" for="email">{{ $errors->first('email') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('client.password') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="password" name="password"
                                        placeholder="{{ trans('client.password') }}"
                                        class="form-control input-custom @if($errors->first('password')) error @endif"
                                        value="{{ old('password') }}" />
                                    <label id="password-error" class="error" for="password">{{ $errors->first('password') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('client.confirm_password') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="password" name="password_confirmation"
                                        placeholder="{{ trans('client.confirm_password') }}"
                                        class="form-control input-custom @if($errors->first('password_confirmation')) error @endif"
                                        value="{{ old('password_confirmation') }}" />
                                    <label id="password_confirmation-error" class="error" for="password_confirmation">{{ $errors->first('password_confirmation') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center group">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("client.country") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="select-group col-sm-8">
                                <div class="justify-content-start">
                                    <select name="country"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.country_select.required') }}"
                                        class="form-control @if($errors->first('country')) error @endif">
                                        <option value="">{{ trans("client.please_select") }}</option>
                                        @foreach($countries as $key => $name)
                                            <option value="{{$key}}" {{ ( old('country', $client->country) == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="country-error" class="error" for="country">{{ $errors->first('country') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center group">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("client.timezone") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="select-group col-sm-8">
                                <div class="justify-content-start">
                                    <select name="time_zone"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.timezone_select.required') }}"
                                        class="form-control pt-1 @if($errors->first('time_zone')) error @endif">
                                        <option value="">{{ trans("client.please_select") }}</option>
                                        @foreach($timeZone as $key => $name)
                                            <option value="{{$key}}" {{ ( old('time_zone', $client->time_zone) == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="time_zone-error" class="error" for="time_zone">{{ $errors->first('time_zone') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center group">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("client.language") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="select-group col-sm-8">
                                <div class="justify-content-start">
                                    <select name="language"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.language_select.required') }}"
                                        class="form-control @if($errors->first('language')) error @endif">
                                        @foreach($languages as $key => $name)
                                            <option value="{{$key}}" {{ ( old('language', $client->language) == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="language-error" class="error" for="language">{{ $errors->first('language') }}</label>
                                </div>
                            </div>
                        </div>
                    @if($client->isGroup1())
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.default_sms_provider') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <select name="default_service_provider"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.select_service_provider.required') }}"
                                        class="form-control @if($errors->first('default_service_provider')) error @endif">
                                        @foreach($allServiceProvider as $key => $name)
                                            <option value="{{$key}}" {{ ( old('default_service_provider', $defaultServiceProvider->code) == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="default_service_provider-error" class="error" for="default_price_sms">{{ $errors->first('default_price_sms') }}</label>
                                </div>
                            </div>
                        </div>
                    @endif
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <button id="addClient" type="submit" class="btn btn-example ml-0" disabled>
                                    {{ trans("client.save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
                        <br>
                        <span class="d-block m-t-md">
                            Set your settings
                            for your new campaign on the left. You can
                            create a regular email campaign or a/b split
                            test campaign.
                        </span>
                        <span class="d-block m-t-lg">
                            You will define your
                            contents and scheduling for your email
                            campaign in next steps.
                        </span>
                    </div>
                </div>
            </div>
        </div>
@endsection

