@extends("admins.clients.client")
@section("title", trans('client.configPriceTitle'))
@section('settingTitle', trans('client.configPriceTitle'))

@section('custom-script')
<script type="text/javascript">
window.client = {};
window.client.clientID = `{{ $client->id }}`;
window.client.allCountries = `{!! json_encode($allCountries) !!}`;
window.client.dataEnabled = `{!! json_encode($dataEnabled) !!}`;
window.client.dataDisabled = `{!! json_encode($dataDisabled) !!}`;
window.client.priceConfiguration = {};
window.client.priceConfiguration.allData = `{!! json_encode($allData) !!}`;
window.client.priceConfiguration.dataEnabled = `{!! json_encode($dataEnabled) !!}`;
window.client.priceConfiguration.dataDisabled = `{!! json_encode($dataDisabled) !!}`;
window.client.priceConfiguration.ajaxGetPriceConfigurationUrl = "{{ Route('clients.ajaxGetPriceConfiguration') }}";
window.client.priceConfiguration.ajaxSavePriceConfigurationUrl = "{{ Route('clients.ajaxSavePriceConfiguration') }}";
window.client.priceConfiguration.ajaxUploadPriceConfigurationUrl = "{{ Route('ajaxUploadPriceConfiguration', ['id' => $client->id]) }}";
window.client.priceConfiguration.requiredCountryError = "{{ trans('client.requiredCountryError') }}";
window.client.priceConfiguration.requiredNetworkError = "{{ trans('client.requiredNetworkError') }}";
window.client.priceConfiguration.requiredPriceError = "{{ trans('client.requiredPriceError') }}";
</script>
<script src="{{ asset('js/control-listgroup.js') }}"></script>
<script src="{{ asset('js/price-config.js') }}"></script>
@endsection

@section("client-content")
	<div id="settings-service-provider">
        <div class="header-button">
            <div class="col">
                <div class="row justify-content-end pt-3 pr-1">
                    <a class="btn btn-example" href="{{ route('client.downloadTemplacePriceConfig') }}">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        {{ trans("settings.download") }}
                    </a>
                    <a class="btn btn-example" href="#" data-toggle="modal" data-target="#settingsUploadModel" data-backdrop="static" data-keyboard="false">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        {{ trans("settings.uploadBtn") }}
                    </a>
                </div>
            </div>
        </div>
        <div class="content-data">
            <div class="row">
                <div class="col-sm">
                    <div class="customListGroupControl" id="countryList">
                        <div class="form-group">
                            <p class="text-center mb-2""><strong>{{ trans('client.countryListTitle') }}</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control searchControl" placeholder="{{ trans('client.inputSearchPlaceholder') }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-example btn-custom btnSearch" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between flex-row">
                            <div class="">
                                <label class="custom-control custom-radio">
                                    <input type="radio" name="show_country" class="custom-control-input" checked="true" value="all">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">
                                        All
                                    </span>
                                </label>
                            </div>
                            <div class="">
                                <label class="custom-control custom-radio">
                                    <input type="radio" name="show_country" class="custom-control-input" value="enabled">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">
                                        Enabled
                                    </span>
                                </label>
                            </div>
                            <div class="">
                                <label class="custom-control custom-radio">
                                    <input type="radio" name="show_country" class="custom-control-input" value="disabled">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">
                                        Disabled
                                    </span>
                                </label>
                            </div>
                        </div>
                        <ul class="list-group listControl"></ul>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <p class="text-center pb-4" for="priceCountry"><strong>{{ trans('client.priceForCountry') }}</strong></p>
                        <p class="pb-4"></p>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon p-1">
                            <label class="fs-12">
                                <input type="checkbox" id="disablePriceCheckbox"> {{ trans('client.disablePrice') }}
                            </label>
                        </span>
                        <input type="number" id="priceCountry" class="form-control fs-12" step="0.01" min="0" placeholder="{{ $client->default_price_sms }} {{ trans('client.defaultPrice') }}">
                        <span class="input-group-addon p-1 fs-12">{{ $client->currency }}</span>
                        <span class="input-group-btn">
                            <button class="btn btn-example btn-custom" id="btnCountrySave" type="button"><i class="fa fa-floppy-o"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="customListGroupControl" id="networkList">
                        <div class="form-group">
                            <p class="text-center mb-2""><strong>{{ trans('client.networkListTitle') }}</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control searchControl" placeholder="{{ trans('client.inputSearchPlaceholder') }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-example btn-custom btnSearch" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <ul class="list-group listControl"></ul>
                    </div>
                </div>
                <div class="col-sm" id="priceNetworkGroup">
                    <div class="form-group">
                        <p class="text-center pb-4" for="priceNetwork"><strong>{{ trans('client.priceForNetwork') }}</strong></p>
                        <p class="pb-4"></p>
                    </div>
                    <div class="input-group">
                        <input type="number" id="priceNetwork" class="form-control fs-12" step="0.01" min="0" placeholder="{{ $client->default_price_sms }} {{ trans('client.defaultPrice') }}">
                        <span class="input-group-addon fs-12">{{ $client->currency }}</span>
                        <span class="input-group-btn">
                            <button class="btn btn-example btn-custom" id="btnNetworkSave" type="button"><i class="fa fa-floppy-o"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
	</div>
@endsection
@include("admins.modals.modal-settings-upload")