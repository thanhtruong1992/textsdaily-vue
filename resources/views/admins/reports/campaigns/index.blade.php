@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script>
        const url_detail = '<?php
            $user = Auth::user();
            if(!!$user->isGroup4()) {
                echo "/reader/reports/campaigns/";
            }else if(!!$user->isGroup3()) {
                echo "/admin/reports/campaigns/";
            }else if(!!$user->isGroup2()) {
                echo "/client/reports/campaigns/";
            }else {
                echo "/agency/reports/campaigns/";
            }
        ?>';
    </script>
    <script src="{{ asset('js/reportListCampaignDatatable.js') }}"></script>
@endsection
<?php
    $user = Auth::user();
    $url_report_camapgin = '';
    $url_report_center = '';
    if(!!$user->isGroup4()) {
        $url_report_camapgin = route("report-campaign-4.index");
        $url_report_center = route("report-center-4.index");
    }else if(!!$user->isGroup3()) {
        $url_report_camapgin = route("report-campaign.index");
        $url_report_center = route("report-center.index");
    }else if(!!$user->isGroup2()) {
        $url_report_camapgin = route("report-campaign-2.index");
        $url_report_center = route("report-center-2.index");
    }else {
        $url_report_camapgin = route("report-campaign-1.index");
        $url_report_center = route("report-center-1.index");
    }
?>
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('report.campaign_report') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            <li class="report-campaign">
                <a href="{{ $url_report_camapgin }}">{{ trans("header.campaign_report") }}</a>
            </li>
            <li class="report-center">
                <a href="{{ $url_report_center }}">{{ trans("header.report_center") }}</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="col pt-4">
            <div class="row justify-content-start pl-4">
                <div class="custom-search">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="text" class="input-search" id="input-search"
                        placeholder="{{ trans("campaign.search") }}" />
                </div>
            </div>
        </div>
        <div class="content-data pt-0">
            <table id="tableListReportCampaign" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans("report.campaign_name") }}</th>
                    <th>{{ trans("report.name_of_user") }}</th>
                    <th>{{ trans("report.sent_date") }}</th>
                    <th>{{ trans("report.recipients") }}</th>
                    <th>{{ trans("report.delivery_rate") }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection