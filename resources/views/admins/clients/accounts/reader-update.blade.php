@extends("layouts.admin") @section("title", "DashBoard")
@section('custom-script')
<script src="{{ asset('js/addClient.js') }}"></script>
@endsection @section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('client.my_client') }}</h1>
    </div>
    <div class="main-client-header">
        <div class="row">
            <div class="container-fluid">
                <ul class="tabs">
                    <li class="account"><a>{{
                            trans("client.menu.reader") }}</a></li>
                </ul>
                @if(Auth::user()->isGroup1()) <a
                    class="float-right mr-0"
                    href="{{ route('clients.index') }}">{{trans("client.back")
                    }} </a> @else <a class="float-right mr-0"
                    href="{{ route('clients2.index') }}">{{trans("client.back")
                    }} </a> @endif
            </div>
        </div>
        <div class="client-content-data">
            <div class="col">
                <div class="row">
                    <div class="d-none d-md-inline-block col-md-1"></div>
                    <div class="col-md-7">
                        <form class="form-add" id="accountForm"
                            action="{{ route('clients2.update-reader', ['id' => $reader->id]) }}"
                            method="post"> {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $reader->id }}">
                            <div
                                class="form-group row justify-content-center group">
                                <label
                                    class="col-xs col-sm-4 col-form-label">{{
                                    trans("client.client") }}<span
                                    class="text-danger">&nbsp;*</span>
                                </label>
                                <div class="select-group col-sm-8">
                                    <div class="justify-content-start">
                                        <select name="reader_id"
                                            data-rule-required="true"
                                            data-msg-required="{{ trans('validationForm.reader_id.required') }}"
                                            class="form-control @if($errors->first('reader_id')) error @endif">
                                            <option value="">{{ trans("client.please_select") }}</option>
                                            @foreach($client as $key => $name)
                                            <option value="{{$name->id}}" {{ ( old('reader_id', $reader->reader_id) ==
                                                $name->id ) ? 'selected="selected"' : '' }}>{{$name->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row justify-content-center">
                                <label
                                    class="col-xs col-sm-4 col-form-label">{{
                                    trans('client.full_name') }}<span
                                    class="text-danger">&nbsp;*</span>
                                </label>
                                <div class="col-xs col-sm-8">
                                    <div class="justify-content-start">
                                        <input type="text" name="name"
                                            data-rule-required="true"
                                            data-msg-required="{{ trans('validationForm.client_name.required') }}"
                                            placeholder="{{ trans('client.full_name') }}"
                                            class="form-control input-custom @if($errors->first('name')) error @endif"
                                            value="{{ old('name', $reader->name) }}" />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row justify-content-center">
                                <label
                                    class="col-xs col-sm-4 col-form-label">{{
                                    trans('client.email') }}<span
                                    class="text-danger">&nbsp;*</span>
                                </label>
                                <div class="col-xs col-sm-8">
                                    <div class="justify-content-start">
                                        <input type="text" name="email"
                                            data-rule-required="true"
                                            data-msg-required="{{ trans('validationForm.email.required') }}"
                                            placeholder="{{ trans('client.email') }}"
                                            class="form-control input-custom @if($errors->first('email')) error @endif"
                                            value="{{ old('email', $reader->email) }}" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row justify-content-center">
                                <label
                                    class="col-xs col-sm-4 col-form-label">{{
                                    trans('client.password') }}
                                </label>
                                <div class="col-xs col-sm-8">
                                    <div class="justify-content-start">
                                        <input type="password"
                                            name="password"
                                            id="password"
                                            data-rule-passwordRegex="true"
                                            data-msg-passwordRegex="{{ trans('client.passwordCriteriaHelp') }}"
                                            placeholder="{{ trans('client.password') }}"
                                            class="form-control input-custom @if($errors->first('password')) error @endif"
                                            value="{{ old('password') }}" />
                                        <small id="emailHelp" class="form-text text-muted">{{ trans('client.passwordCriteriaHelp') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row justify-content-center">
                                <label
                                    class="col-xs col-sm-4 col-form-label">{{
                                    trans('client.confirm_password') }}
                                </label>
                                <div class="col-xs col-sm-8">
                                    <div class="justify-content-start">
                                        <input type="password"
                                            name="password_confirmation"
                                            data-rule-equalTo="#password"
                                            placeholder="{{ trans('client.confirm_password') }}"
                                            class="form-control input-custom @if($errors->first('password_confirmation')) error @endif"
                                            value="{{ old('password_confirmation') }}" />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row justify-content-center p-t-xl">
                                <div class="col-xs col-sm-8 ml-md-auto">
                                    @if(Auth::user()->isGroup1()) <a
                                        class="btn btn-secondary ml-0"
                                        href="{{ route('clients.index') }}">
                                        {{ trans("client.cancel") }} </a>
                                    @else <a
                                        class="btn btn-secondary ml-0"
                                        href="{{ route('clients2.index') }}">
                                        {{ trans("client.cancel") }} </a>
                                    @endif
                                    <button id="addClient" type="submit"
                                        class="btn btn-example ml-0"
                                        disabled>{{ trans("client.save")
                                        }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="d-none d-md-inline-block col-md-3">
                        <div class="content-sidebar">
                            <p>
                                <strong>Reader Information</strong>
                            </p>
                            <span class="d-block mt-3">
                                Create client with <strong>reader-only</strong> access to all information – SMS  campaigns, subscriber lists, billing information etc.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

