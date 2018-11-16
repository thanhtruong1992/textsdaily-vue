@extends("admins.settings.index")
@section('settingTitle', trans('settings.settingsReportSettingsTitle'))
@section('custom-script')
<script src="{{ asset("js/settings-report.js") }}"></script>
<script type="text/javascript">
window.link = {}
</script>
@endsection
@section("settings-content")
<div class="col">
    <div class="row justify-content-center">
        <h1 class="mt-5"><strong>{{ trans('settings.reportSettingTitle') }}</strong></h1>
    </div>
    <div class="row">
        <div class="d-none d-md-inline-block col-md-1"></div>
            <div class="col-md-7">
                <form class="form-add" id="whiteLabelForm"
                    action="{{ route('settings.report-update') }}"
                    method="post">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label class="col-xs col-sm-4 col-form-label text-left">{{ trans('settings.emailReceipient') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="email"
                                        name="email"
                                        data-rule-required="true"
                                        data-rule-emailRegex="true"
                                        data-msg-required="{{ trans('validationForm.email.required')}}"
                                        data-msg-emailRegex="{{ trans('validationForm.email.email')}}"
                                        placeholder="{{ trans('settings.email') }}"
                                        class="form-control input-custom @if($errors->first('email')) error @endif"
                                        value="{{ old('email', $email) }}"/>
                                    <label id="email-error" class="error" for="email">{{ $errors->first('email') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-xs col-sm-4 col-form-label text-left">{{ trans('settings.time') }}</label>
                            <div class="col-xs col-sm-5 col-md-4">
                                <div class="justify-content-start">
                                    <div class="input-group">
                                        <input type="text"
                                            name="time"
                                            placeholder="00:00"
                                            class="form-control input-custom @if($errors->first('time')) error @endif"
                                            data-rule-required="true"
                                            data-rule-hourMinute="true"
                                            data-msg-required="{{ trans('validationForm.time.required')}}"
                                            data-msg-hourMinute="{{ trans('validationForm.time.time')}}"
                                            value="{{ old('time', $time) }}" />
                                        <span class="input-group-addon @if($errors->first('time')) error @endif"><i class="col-xs fa fa-clock-o" aria-hidden="true"></i></span>
                                    </div>
                                    <label id="time-error" class="error" for="time">{{ $errors->first('time') }}</label>
                                </div>
                            </div>
                            <div class="col-xs col-sm-5"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.report') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="form-check justify-content-start">
                                    <label class="custom-control custom-checkbox">
                                      <input name="reseller" class="custom-control-input"
                                        type="checkbox"
                                        {{ ($reseller == 'on') ? "checked" : "" }} />
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description">{{ trans('settings.reseller') }}</span>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                      <input name="detail" class="custom-control-input"
                                        type="checkbox"
                                        {{ ($detail == 'on') ? "checked" : "" }} />
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description">{{ trans('settings.detail') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <button id="saveConfig" type="submit" class="btn btn-example ml-0">
                                    {{ trans("client.save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection

