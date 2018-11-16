@extends("layouts.admin") @section("title", "Report Center")
@section("custom-script")
    <script>
        const url_report_center = '<?php echo route("report-center.api"); ?>';
        const url_get_data = '<?php echo route("report-center.data"); ?>';

    </script>
    <script src="{{ asset("js/reportCenter.js") }}"></script>
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
        <h1>{{ trans('report.report') }}</h1>
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
        <div class="filter-request">
            <form id="formReportCenter">
                <div class="col-12">
                    <div class="from-group row p-t-xl p-b-md">
                        <p class="text-bold m-l-md">{{ trans("report.peiod") }}</p>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 col-md-4">
                            <div class="row justify-content-start">
                                <label class="col-md-2 col-form-label">{{ trans("report.from") }}:</label>
                                <div class="col-md-10">
                                    <div class="justify-content-start">
                                        <input type="text"
                                            name="from"
                                            id="inputStartDate"
                                            class="form-control input-custom"
                                            value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="row justify-content-start">
                                <label class="col-md-2 col-form-label">{{ trans("report.to") }}:</label>
                                <div class="col-md-10">
                                    <div class="justify-content-start">
                                        <input type="text"
                                            name="to"
                                            id="inputEndDate"
                                            class="form-control input-custom"
                                            value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="row justify-content-start">
                                <label class="col-md-3 col-form-label">{{ trans("report.timezone") }}:</label>
                                <div class="col-md-9">
                                    <select name="timezone" class="form-control">
                                        @foreach($timeZone as $key => $name)
                                            <option value="{{$key}}" {{ $user_timeZone == $key  ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <p class="text-bold m-l-md">{{ trans("report.include_in_report") }}:</p>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="client_name" class="custom-control-input">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.account_name") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="campaign_id" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.campaign_id") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="sender" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.from") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="phone" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.to") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="return_message_id" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.message_id") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="updated_at" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.sent_at") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="country" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.country_name") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="network" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.network_name") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="sum_price_client" class="custom-control-input">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.credits_per_message") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="return_status" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.status") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-md">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="return_status_message" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.reason") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="report_updated_at" class="custom-control-input">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.done_at") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="service_provider" class="custom-control-input">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.service_name") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="message_count" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.message_count") }}</span>
                            </label>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 col-xl-2">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" name="message" class="custom-control-input" checked>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description">{{ trans("report.message_text") }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label text-bold">{{ trans("report.campaign_name_contains") }}:</label>
                            <input type="text"
                                name="campaign_name"
                                class="form-control input-custom"
                                value="" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label text-bold">{{ trans("report.notify_by_email") }}:</label>
                            <input type="text"
                                    name="emails"
                                    data-rule-emailMultipleRegex="true"
                                    data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    placeholder="example1@email.com; example2@email.com"
                                    class="form-control input-custom"
                                    value="{{ $user->email }}" />
                        </div>
                    </div>
                    <div class="form-group row justify-content-center p-t-md">
                        <button type="button" class="btn btn-example" id="reportCenter" {{ !!Auth::user()->isGroup4() ? 'disabled' : '' }}>
                            {{ trans("report.request") }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="content-data p-t-lg">
            <table id="tableReportCenter" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans("report.from") }}</th>
                    <th>{{ trans("report.to") }}</th>
                    <th>{{ trans("report.timezone") }}</th>
                    <th>{{ trans("report.requested") }}</th>
                    <th>{{ trans("report.prepared") }}</th>
                    <th>{{ trans("report.status") }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection