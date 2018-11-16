@extends("admins.settings.index")
@section("title", trans('settings.settingsMobilePatternTitle'))
@section('settingTitle', trans('settings.settingsMobilePatternTitle'))

@section('custom-script')
@endsection

@section("settings-content")
<div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="numberPatternForm" method="post" action="{{ route('updateMobilePattern', ['id' => $mobilePattern->id])  }}">
                        {{ csrf_field() }}
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.numberPattern') }}</label>
                            <div class="col-xs col-sm-8">
                                {{ $mobilePattern->number_pattern }}
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.countryCode') }}</label>
                            <div class="col-xs col-sm-8">
                                {{ $mobilePattern->country }}
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
                                        value="{{ old('network', $mobilePattern->network) }}"/>
                                    <label id="network-error" class="error" for="network">{{ $errors->first('network') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <a class="btn btn-secondary ml-0" href="{{ route('settingMobilePattern') }}">
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
                        <p><strong>Update Mobile Pattern</strong></p>
                        <br>
                        <span class="d-block m-t-md">Description here!!!</span>
                        <span class="d-block m-t-lg"></span>
                    </div>
                </div>
            </div>
        </div>
@endsection

