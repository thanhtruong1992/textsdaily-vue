@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script type="text/javascript">
        const campaignID = <?php echo json_encode($campaign_id) ?>;
        const userID = <?php echo json_encode($user_id) ?>;
        window.campaign_id = <?php echo $campaign_id ?>;
        window.campaign_link_id = <?php echo $campaign_link_id ?>;
        window.dct_token = "<?php  echo env('DCT_TOKEN_SHORT_LINK') ?>";
        window.api_get_short_links = "<?php echo env('DCT_API_GET_SHORT_LINKS') ?>";
        window.api_export_csv = "<?php echo env('DCT_API_EXPORT_CSV') ?>";
        window.dtc_download_csv = "<?php echo env('DCT_DOWNLOAD_CSV') ?>";
        window.time_zone_user = "<?php echo Auth::user()->time_zone ?>";
    </script>
    <script src="{{ asset('js/shortLinks.js') }}"></script>
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
        <h1>{{ trans('report.campaign_statistics') }}: {{ $campaign->name }}</h1>
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
        <div class="header-button">
            <div class="col">
                <div class="row justify-content-end">
                    <button class="btn btn-example" type="button" id="exportCSV">
                    {{ trans("report.export_to_csv") }}
                    </button>
                </div>
            </div>
        </div>
        <div class="content-data">
            <table class="table table-striped m-t-lg" id="tableShortLink">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans("report.url") }}</th>
                    <th>{{ trans("report.total_clicks") }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
