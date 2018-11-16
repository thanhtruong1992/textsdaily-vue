@extends("admins.clients.client") @section("title", "DashBoard")
@section('custom-script')
<script src="{{ asset('js/addClient.js') }}" ></script>
<script type="text/javascript">
window.link = {}
window.link.api_add_sender = "{{ route('campaign.add_sender') }}"
window.link.api_check_username = "{{ route('ajaxCheckUsername') }}"
</script>
@endsection
@section("client-content")
<div class="col">
    <div class="row">
        <div class="d-none d-md-inline-block col-md-1"></div>
        <div class="col-md-7">
            <form class="form-add" id="accountForm"
            @if(Auth::user()->isGroup1())
            action="{{ route('clients.store') }}"
            @else
            action="{{ route('clients2.storeApiAccount') }}"
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
                                value="{{ old('name') }}"/>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans('client.username') }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start" id="inputUsername">
                            <input
                                type="text"
                                name="username"
                                id="username"
                                data-rule-required="true"
                                data-rule-minlength="8"
                                data-msg-required="{{ trans('validationForm.username.required') }}"
                                data-msg-min="{{ trans('validationForm.username.min') }}"
                                placeholder="{{ trans('client.username') }}"
                                class="form-control input-custom @if($errors->first('username')) error @endif"
                                value="{{ old('username') }}"/>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans('client.email') }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start">
                            <input type="email" name="email"
                                data-rule-required="true"
                                data-rule-emailRegex="true"
                                data-msg-emailRegex="{{ trans('validationForm.email.emailRegex') }}"
                                data-msg-required="{{ trans('validationForm.email.required') }}"
                                placeholder="{{ trans('client.email') }}"
                                class="form-control input-custom @if($errors->first('email')) error @endif"
                                value="{{ old('email') }}"/>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans('client.password') }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start">
                            <input type="password" name="password"
                                id="password"
                                data-rule-required="true"
                                data-msg-required="{{ trans('validationForm.password.required') }}"
                                data-rule-passwordRegex="true"
                                data-msg-passwordRegex="{{ trans('client.passwordCriteriaHelp') }}"
                                placeholder="{{ trans('client.password') }}"
                                class="form-control input-custom @if($errors->first('password')) error @endif"
                                value="{{ old('password') }}" />
                            <small id="emailHelp" class="form-text text-muted">{{ trans('client.passwordCriteriaHelp') }}</small>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans('client.confirm_password') }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start">
                            <input type="password" name="password_confirmation"
                                data-rule-required="true"
                                data-msg-required="{{ trans('validationForm.confirm_password.required') }}"
                                data-rule-equalTo="#password"
                                placeholder="{{ trans('client.confirm_password') }}"
                                class="form-control input-custom @if($errors->first('password_confirmation')) error @endif"
                                value="{{ old('password_confirmation') }}" />
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
                                    <option value="{{$key}}" {{ ( old('country') == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                @endforeach
                            </select>
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
                                    <option value="{{$key}}" {{ ( old('time_zone') == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                @endforeach
                            </select>
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
                                    <option value="{{$key}}" {{ ( old('language') == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center group">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans("client.currency") }}<span class="text-danger">&nbsp;*</span></label>
                @if(Auth::user()->isGroup1())
                    <div class="select-group col-sm-8">
                        <div class="justify-content-start">
                            <select name="currency"
                                data-rule-required="true"
                                data-msg-required="{{ trans('validationForm.currency_select.required') }}"
                                class="form-control @if($errors->first('currency')) error @endif">
                                <option value="">{{ trans("client.please_select") }}</option>
                                @foreach($currencies as $key => $code)
                                    <option value="{{$key}}" {{ ( old('currency') == $key ) ? 'selected="selected"' : '' }}>{{$code->code}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <div class="select-group col-sm-8">
                        <div class="justify-content-start">
                            <label name="currency">{{ Auth::user()->currency}}</label>
                        </div>
                    </div>
                @endif
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans('client.default_price') }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start">
                            <input type="number" name="default_price_sms"
                                step="any"
                                class="form-control input-custom @if($errors->first('default_price_sms')) error @endif"
                                data-rule-required="true"
                                data-msg-required="{{ trans('validationForm.default_price.required') }}"
                                data-rule-min="0.00000000001"
                                data-msg-min="{{ trans('validationForm.default_price.min') }}"
                                value="{{ old('default_price_sms', 0.20) }}"/>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans("client.sender") }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="col-xs col-sm-8">
                        <div class="justify-content-start send-select">
                            <select
                                id="sender"
                                class="form-control select2 select2-custom select-sender"
                                name="sender[]"
                                multiple="multiple"
                                tag="true"
                                data-placeholder="{{ trans('campaign.sender') }}"
                                data-rule-required="true"
                                data-rule-maxlength="11"
                                data-msg-required="{{ trans('validationForm.sender.required') }}"
                                data-msg-maxlength="{{ trans('validationForm.sender.maxlength') }}">
                                @foreach($senderList as $id => $name)
                                    <option value="{{$name}}" {{ ( $name == old('sender',[])) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center group">
                    <label class="col-xs col-sm-4 col-form-label">{{ trans("client.account_type") }}<span class="text-danger">&nbsp;*</span></label>
                    <div class="select-group col-sm-8">
                        <div class="justify-content-start">
                            <select name="billing_type"
                                data-rule-required="true"
                                data-msg-required="{{ trans('validationForm.language_select.required') }}"
                                class="form-control @if($errors->first('language')) error @endif">
                                @foreach($account_type as $type)
                                    <option value="{{$type}}" {{ ( old('billing_type') == $type ) ? 'selected="selected"' : '' }}>{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @if(Auth::user()->isGroup2())
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-form-label"></label>
                    <div class="select-group col-sm-8">
                        <div class="form-check justify-content-start">
                            <label class="form-check-label">
                                <input name="encrypted" class="form-check-input"
                                type="checkbox"
                                value="1"> {{ trans('client.encrypt_user') }}
                            </label>
                        </div>
                        <div class="form-check justify-content-start">
                            <label class="form-check-label">
                                <input name="is_tracking_link" class="form-check-input"
                                type="checkbox"
                                value="1"
                                checked> {{ trans('client.tracking_link') }}
                            </label>
                        </div>
                    </div>
                </div>
            @endif
                <div class="form-group row justify-content-center p-t-xl">
                    <div class="col-xs col-sm-8 ml-md-auto">
                    @if(Auth::user()->isGroup1())
                        <a class="btn btn-secondary ml-0" href="{{ route('clients.index') }}">
                            {{ trans("client.cancel") }}
                        </a>
                    @else
                        <a class="btn btn-secondary ml-0" href="{{ route('clients2.index') }}">
                            {{ trans("client.cancel") }}
                        </a>
                    @endif
                        <button id="addClient" type="button" class="btn btn-example ml-0" disabled>
                            {{ trans("client.save") }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="d-none d-md-inline-block col-md-3">
            <div class="content-sidebar">
                <p><strong>My API Clients</strong></p>
                <br>
                <span class="d-block mt-1">
                    Account Information
                </span>
                <span class="d-block">
                    Create client accounts have full access to their SMS campaigns, subscriber lists, billing information etc. They can send sms campaigns, call api etc.
                </span>
                <span class="d-block mt-2">
                    Pricing Configuration
                </span>
                <span class="d-block">

                    You must set the pricing configuration after the client has been created
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
