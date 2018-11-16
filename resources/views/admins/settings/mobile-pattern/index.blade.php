@extends("admins.settings.index")
@section("title", trans('settings.settingsMobilePatternTitle'))
@section('settingTitle', trans('settings.settingsMobilePatternTitle'))

@section("custom-script")
<script type="text/javascript">
window.settings = {};
window.settings.MobilePattern = {};
window.settings.MobilePattern.getMobilePatternUrl = "{{ route('ajaxGetMobilePattern') }}";
window.settings.MobilePattern.ajaxUploadMobilePatternUrl = "{{ Route('ajaxUploadMobilePattern') }}";
window.settings.MobilePattern.editTitle = "{{ trans('settings.MobilePattern.editTitle') }}";
window.settings.MobilePattern.editURL = "{{ route('editMobilePattern', ['id' => '__ID__'])  }}";
window.settings.MobilePattern.deleteTitle = "{{ trans('settings.MobilePattern.deleteTitle') }}";
window.settings.MobilePattern.deleteConfirm = "{{ trans('settings.MobilePattern.deleteConfirm') }}";
window.settings.MobilePattern.deleteURL = "{{ route('deleteMobilePattern')  }}";
</script>
<script src="{{ asset('js/settings-mobile-pattern.js') }}"></script>
@endsection

@section("settings-content")
<div id="settings-mobile-pattern">
    <div class="col header-button">
        <div class="row pt-3">
            <div class="col pl-4">
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
                    <a class="btn btn-example" href="{{ route('settings.get_mobile_pattern_sample') }}">
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
	   <table id="table-mobile-pattern" class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans("settings.numberPattern") }}</th>
                    <th>{{ trans("settings.country") }}</th>
                    <th>{{ trans("settings.network") }}</th>
                    <th class="action text-center">{{ trans("settings.action") }}</th>
                </tr>
            </thead>
	   </table>
	</div>
</div>
@endsection

@include("admins.modals.modal-settings-upload")