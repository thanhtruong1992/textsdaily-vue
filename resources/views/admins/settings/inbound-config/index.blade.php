@extends("admins.settings.index")
@section("title", trans('settings.settingsInboundSMSTitle'))
@section('settingTitle', trans('settings.settingsInboundSMSTitle'))

@section("custom-script")
<script type="text/javascript">
window.settings = {};
window.settings.InboundConfig = {};
window.settings.InboundConfig.getInboundConfigUrl = "{{ route('ajaxGetInboundConfig') }}";
window.settings.InboundConfig.assignTitle = "{{ trans('settings.assignTitle') }}";
window.settings.InboundConfig.unassignTitle = "{{ trans('settings.unassignTitle') }}";
window.settings.InboundConfig.editURL = "{{ route('editInboundConfig', ['id' => '__ID__'])  }}";
window.settings.InboundConfig.unassignConfirm = "{{ trans('settings.unassignConfirm') }}";
window.settings.InboundConfig.unassignURL = "{{ route('unassignInboundConfig')  }}";
</script>

@if( $client->type == 'GROUP2' )
<script type="text/javascript">
window.settings.InboundConfig.editURL = "{{ route('editInboundConfigClient', ['id' => '__ID__'])  }}";
</script>
@endif

<script src="{{ asset('js/settings-inbound-config.js') }}"></script>
@endsection

@section("settings-content")
<div id="settings-inbound-config">
    <div class="header-button">
        <div class="col">
            <div class="row justify-content-end pt-3 pr-1">
	       </div>
	   </div>
    </div>
    <div class="content-data">
	   <table id="table-inbound-config" class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans("settings.inboundNumber") }}</th>
                    <th>{{ trans("settings.inboundExpiryDate") }}</th>
                    <th>{{ trans("settings.inboundClient") }}</th>
                    <th>{{ trans("settings.action") }}</th>
                </tr>
            </thead>
	   </table>
	</div>
</div>
@endsection