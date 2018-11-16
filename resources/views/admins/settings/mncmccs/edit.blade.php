@extends("admins.settings.index")
@section("title", trans('settings.settingsMCCMNCTitle'))
@section("settingTitle", trans('settings.settingsMCCMNCTitle'))

@section('custom-script')
@endsection

@section("settings-content")
<div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="mccmncForm" method="post" action="{{ route('settings.mnc-mcc.update', ['id' => $mccmnc->id])  }}">
                        {{ csrf_field() }}
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.mcc_mnc') }}</label>
                            <div class="col-xs col-sm-8">
                                {{ $mccmnc->mccmnc }}
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.countryCode') }}</label>
                            <div class="col-xs col-sm-8">
                                {{ $mccmnc->country }}
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.countryName') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="country_name"
                                        class="form-control input-custom @if($errors->first('country_name')) error @endif"
                                        value="{{ old('country_name', $country->name) }}"/>
                                    <label id="country_name-error" class="error" for="country_name">{{ $errors->first('country_name') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.network') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="network"
                                        class="form-control input-custom @if($errors->first('network')) error @endif"
                                        value="{{ old('network', $mccmnc->network) }}"/>
                                    <label id="network-error" class="error" for="network">{{ $errors->first('network') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <a class="btn btn-secondary ml-0" href="{{ route('settings.mnc-mcc') }}">
                                    {{ trans("settings.Cancel") }}
                                </a>
                                <button type="submit" class="btn btn-example ml-0">
                                    {{ trans("settings.save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none d-md-inline-block col-md-3">
                    <div class="content-sidebar">
                        <p><strong>Update MCC-MNC</strong></p>
                        <br>
                        <span class="d-block m-t-md">Description here!!!</span>
                        <span class="d-block m-t-lg"></span>
                    </div>
                </div>
            </div>
        </div>
@endsection

