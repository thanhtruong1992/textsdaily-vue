@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
<script src="{{ asset('js/campaignListDataTable.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    window.link = {}
    window.link.group = "{{ Auth::user()->type }}"
    window.link.updateCampaignLink = "{{ Auth::user()->isGroup3() ? route('campaign.info', ['id' =>'__id']) : route('campaign4.info', ['id' =>'__id']) }}";
    window.link.changeAmendCampaignLink = "{{ Auth::user()->isGroup3() ? route('campaign.info', ['id' =>'__id']) : '' }}";
});
</script>
@endsection @section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('campaign.indexTitle') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            @if(Auth::user()->isGroup3())
                <li class="report-campaign {{ Route::currentRouteName() == 'campaign.index' ? 'active' : ''}}">
                    <a href="{{ route('campaign.index') }}">{{ trans("header.campaign_list") }}</a>
                </li>
                <li class="report-center">
                    <a href="{{ route("notification-settings.index") }}">{{ trans("header.notification_settings") }}</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="main-content">
        <div class="header-button">
            <div class="col">
                <div class="justify-content-start">
                    <div class="custom-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" class="input-search" id="input-search"
                            placeholder="{{ trans("campaign.search") }}" />
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row justify-content-end">
                @if(Auth::user()->isGroup3())
                    <a class="btn btn-example" href="{{ url("admin/campaign/create") }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ trans("campaign.new") }}
                    </a>
                @endif
                </div>
            </div>
        </div>
        <div class="content-data">
            <table id="table-campaign" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans("campaign.campaign_name") }}</th>
                        <th></th>
                        <th>{{ trans("campaign.schedule_date") }}</th>
                        <th>{{ trans("campaign.subscriber_list") }}</th>
                        <th>{{ trans("campaign.status") }}</th>
                        <th class="action-campaign">{{ trans("campaign.action") }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection