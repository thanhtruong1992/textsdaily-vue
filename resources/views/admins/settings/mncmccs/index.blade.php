@extends("admins.settings.index")
@section("title", trans('settings.settingsMCCMNCTitle'))
@section('settingTitle', trans('settings.settingsMCCMNCTitle'))

@section("custom-script")
<script type="text/javascript">
window.settings = {};
window.settings.MCCMNC = {};
window.settings.MCCMNC.getMCCMNCUrl = "{{ route('setting.ajaxGetMCCMNC') }}";
window.settings.MCCMNC.ajaxUploadMCCMNCUrl = "{{ Route('ajaxUploadMCCMNC') }}";
window.settings.MCCMNC.editTitle = "{{ trans('settings.MCCMNC.editTitle') }}";
window.settings.MCCMNC.editURL = "{{ route('settings.mnc-mcc.edit', ['id' => '__ID__'])  }}";
window.settings.MCCMNC.deleteTitle = "{{ trans('settings.MCCMNC.deleteTitle') }}";
window.settings.MCCMNC.deleteConfirm = "{{ trans('settings.MCCMNC.deleteConfirm') }}";
window.settings.MCCMNC.deleteURL = "{{ route('settings.mnc-mcc.delete')  }}";
</script>
<script src="{{ asset('js/settings-mncmcc.js') }}"></script>
@endsection

@section("settings-content")
<div id="settings-service-provider">
    <div class="col header-button">
        <div class="row pt-3">
            <div class="col">
                <div class="m-l-sm">
                    <div class="custom-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" class="input-search" id="input-search"
                            placeholder="{{ trans("campaign.search") }}" />
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row justify-content-end pr-3">
                    <a class="btn btn-example" href="{{ route('settings.get_mcc_mnc') }}">
                            <i class="fa fa-download" aria-hidden="true"></i>
                            {{ trans("settings.download") }}
                    </a>
                    <a class="btn btn-example" href="#" data-toggle="modal" data-target="#settingsUploadModel" data-backdrop="static" data-keyboard="false">
                        <i class="fa fa-upload" aria-hidden="true">&nbsp;</i>{{ trans("settings.uploadBtn") }}
                   </a>
               </div>
           </div>
        </div>
    </div>
    <div class="content-data">
	   <table id="table-mncmcc" class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans("settings.mcc_mnc") }}</th>
                    <th>{{ trans("settings.country") }}</th>
                    <th>{{ trans("settings.network") }}</th>
                    <th class="action-campaign text-center">{{ trans("settings.action") }}</th>
                </tr>
            </thead>
	   </table>
	</div>
</div>
@endsection

@include("admins.modals.modal-settings-upload")