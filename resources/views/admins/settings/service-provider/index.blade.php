@extends("admins.settings.index")
@section("title", trans('settings.serviceProviderTitle'))
@section('settingTitle', trans('settings.serviceProviderTitle'))

@section('custom-script')
<script type="text/javascript">
window.settings = {};
window.settings.allCountries = {!! json_encode($allCountries) !!};
window.settings.serviceProvider = {};
window.settings.serviceProvider.ajaxGetServiceProviderUrl = "{{ Route('ajaxGetPreferredServiceProvider') }}";
window.settings.serviceProvider.ajaxSaveServiceProviderUrl = "{{ Route('ajaxSaveServiceProvider') }}";
window.settings.serviceProvider.ajaxUploadServiceProviderUrl = "{{ Route('ajaxUploadServiceProvider') }}";
window.settings.serviceProvider.requiredCountryError = "{{ trans('settings.requiredCountryError') }}";
window.settings.serviceProvider.requiredNetworkError = "{{ trans('settings.requiredNetworkError') }}";
window.settings.serviceProvider.requiredServiceProviderError = "{{ trans('settings.requiredServiceProviderError') }}";
window.settings.serviceProvider.allData = {!! json_encode($preferredServiceProviderData) !!};
</script>
<script src="{{ asset('js/control-listgroup.js') }}"></script>
<script src="{{ asset('js/settings-service-provider.js') }}"></script>
@endsection

@section("settings-content")
	<div id="settings-service-provider">
        <div class="header-button">
            <div class="col">
                <div class="row justify-content-end pt-3 pr-1">
                    <!-- <a href="{{ storage_path('csv/preferred_service_provider.csv') }}" target="blank" class="btn btn-link">{{ trans('settings.DownloadTemplate') }}</a> -->
                    <a class="btn btn-example" href="{{ route('settings.get_service_provider') }}">
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
                            <p class="text-center mb-2""><strong>{{ trans('settings.countryListTitle') }}</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control searchControl" placeholder="{{ trans('settings.inputSearchPlaceholder') }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-example btn-custom btnSearch" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <ul class="list-group listControl"></ul>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="customListGroupControl" id="networkList">
                        <div class="form-group">
                            <p class="text-center mb-2""><strong>{{ trans('settings.networkListTitle') }}</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control searchControl" placeholder="{{ trans('settings.inputSearchPlaceholder') }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-example btn-custom btnSearch" type="button"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                        <ul class="list-group listControl"></ul>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <p class="text-center pb-4" for="serviceProvider"><strong>{{ trans('settings.PreferredNetworkProviderTitle') }}</strong></p>
                        <p class="pb-4"></p>
                    </div>
                    <div class="input-group">
                        <select class="form-control" id="serviceProviderControl">
                            <option value="">{{ trans('settings.Select') }}</option>
                            @foreach( $allServiceProvider as $key => $value )
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button class="btn btn-example btn-custom" id="btnSave" type="button"><i class="fa fa-floppy-o"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-sm"></div>
            </div>
        </div>
	</div>
@endsection

@include("admins.modals.modal-settings-upload")
