@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script type="text/javascript">
        const campaignID = <?php echo json_encode($campaign_id) ?>;
        const userID = <?php echo json_encode($user_id) ?>;
        const goolge_key = "{{ env('KEY_SHORT_LINK_GOOGLE') }}";
        const url_export_csv = '<?php
            $url_export_pdf = "";
            if(!!Auth::user()->isGroup1()) {
                $url_export_pdf = route("export-campaign-1.pdf", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
                echo route("export-campaign-1.csv", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
            }else if(!!Auth::user()->isGroup2()) {
                $url_export_pdf = route("export-campaign-2.pdf", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
                echo route("export-campaign-2.csv", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
            }else if(!!Auth::user()->isGroup4()) {
                $url_export_pdf = route("export-campaign-4.pdf", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
                echo route("export-campaign-4.csv", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
            }else {
                $url_export_pdf = route("export-campaign.pdf", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
                echo route("export-campaign.csv", array('user_id' => $user_id, 'campaign_id' => $campaign_id));
            }
        ?>';
        jQuery(function($) {
        	$("#exportCSV").click(function() {
            	var detailed = $("input[name='detailed']").is(":checked") ? 1 : 0;
            	var pending = $("input[name='pending']").is(":checked") ? 1 : 0;
            	var delivered = $("input[name='delivered']").is(":checked") ? 1 : 0;
            	var expired = $("input[name='expired']").is(":checked") ? 1 : 0;
            	var failed = $("input[name='failed']").is(":checked") ? 1 : 0;

            	if(detailed == 0 && pending == 0 && delivered == 0 && expired == 0 && failed == 0) {
                console.log("Show error");
                $('#checkboxError').text("{{ trans('report.no_selected') }}");
                return false;
            	}
            if(detailed == 1 || pending == 1 || delivered == 1 || expired == 1 || failed == 1) {
                    var param = "detailed=" + detailed + "&pending=" + pending + "&delivered=" + delivered + "&expired=" + expired + "&failed=" + failed;
                	var url = url_export_csv + "?" + param;
                	window.open(url, "_blank");
                }

                $('#checkboxError').text("");
                $("#exportCampaignModal").modal('hide');
                $("#fromExportCampaign")[0].reset();
            })

            $("input[name='detailed']").on('change', function() {resetCheckboxError();});
            $("input[name='pending']").on('change', function() {resetCheckboxError();});
            $("input[name='delivered']").on('change', function() {resetCheckboxError();});
            $("input[name='expired']").on('change', function() {resetCheckboxError();});
            $("input[name='failed']").on('change', function() {resetCheckboxError();});

            $("#close-exportCSV").on('click', function() { resetModalForm(); });
            $("#exit-exportCSV").on('click', function() { resetModalForm(); });

            function resetCheckboxError() {
        		  $('#checkboxError').text("");
            }

            function resetModalForm() {
            	   $("#exportCampaignModal").modal('hide');
            	   resetCheckboxError();
            	   $("input[name='detailed']").prop('checked', true);
            	   $("input[name='pending']").prop('checked', true);
            	   $("input[name='delivered']").prop('checked', true);
            	   $("input[name='expired']").prop('checked', true);
            	   $("input[name='failed']").prop('checked', true);
            }
        });

    </script>
    <script src="{{ asset('js/reportCampaign.js') }}"></script>
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
                        <div class="dropdown">
                            <button class="btn btn-example dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans("report.action") }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @if (!!Auth::user()->isGroup3())
                                    <a class="dropdown-item" href="{{ route("campaign.info", ["campaign_id" => $campaign_id]) }}">{{ trans("report.view_campaign_summary") }}</a>
                                @endif
                                <a class="dropdown-item" id="export-csv">{{ trans("report.export_to_csv") }}</a>
                                <a class="dropdown-item" href="{{ $url_export_pdf }}" id="export-pdf">{{ trans("report.export_to_pdf") }}</a>
                            </div>
                        </div>
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
                        <th>{{ trans("report.delivered") }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="content-data">
                <div class="row">
                    <div id="columnChart" class="chartdiv"></div>
                    <div id="mapChart"></div>
                </div>
                <table class="table table-striped m-t-lg" id="reportCamapignTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans("subscriber.country") }}</th>
                        <th>{{ trans("subscriber.network") }}</th>
                        <th>{{ trans("subscriber.sent") }}</th>
                        <th>{{ trans("subscriber.delivered") }}</th>
                        <th>{{ trans("subscriber.pending") }}</th>
                        <th>{{ trans("subscriber.failed") }}</th>
                        <th>{{ trans("subscriber.expired") }}</th>
                        <th>{{ trans("subscriber.expenses") }}</th>
                        <th>{{ trans("subscriber.delivery_rate") }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
</div>
@endsection
@include("admins.modals.modal-report-campaign")
