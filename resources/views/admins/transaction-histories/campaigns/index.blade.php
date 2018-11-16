@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script>
        const api_get_data = '<?php echo route(Auth::user()->isGroup1() ? "api.transaction-histories.getCampaigns" : "api.transaction-histories-2.getCampaigns") ?>';
        const url_detail = '<?php echo route(Auth::user()->isGroup1() ? "transaction-histories.campaigns.detail" : "transaction-histories-2.campaigns.detail", array("user_id" => "", "campaign_id" => "")) ?>';
        const url_export_csv = '<?php echo route(Auth::user()->isGroup1() ? "transaction-histories.campaigns.csv" : "transaction-histories-2.campaigns.csv") ?>';
        const role_user = '<?php echo Auth::user()->type; ?>';
    </script>
    <script src="{{ asset("js/transactionHistoryCampaign.js") }}"></script>
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('transaction-history.cost_campaign') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            <li class="client">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.client.index") : route("transaction-histories-2.client.index") }}">{{ trans("transaction-history.client") }}</a>
            </li>
            <li class="report-center active">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.campaigns.index") : route("transaction-histories-2.campaigns.index") }}">{{ trans("transaction-history.cost_campaign") }}</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="col-12 p-t-lg">
            <form id="formFilterCampaign">
                <div class="row justify-content-start">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                        <div class="row justify-content-start m-b-md">
                            <label class="col-form-label col-xs-12 col-sm-4 col-md-3 col-lg-2">{{ trans("transaction-history.from") }}</label>
                            <div class="col-xs-12 col-sm-8 col-md-9">
                                <input type="text"
                                name="from"
                                id="inputStartDate"
                                class="form-control input-custom"
                                value="" />
                            </div>

                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-b-md">
                        <div class="row justify-content-start">
                            <label class="col-form-label col-xs-12 col-sm-4 col-md-3 col-lg-2">{{ trans("transaction-history.to") }}</label>
                            <div class="col-xs-12 col-sm-8 col-md-9">
                                <input type="text"
                                name="to"
                                id="inputEndDate"
                                class="form-control input-custom"
                                value="" />
                            </div>

                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 p-b-md">
                        <div class="row justify-content-start">
                            <label class="col-form-label col-xs-12 col-sm-4 col-md-4 col-lg-4 col-xl-3">{{ trans("transaction-history.timezone") }}</label>
                            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 col-xl-9">
                                <select name="timezone" class="form-control">
                                @foreach($timeZone as $key => $name)
                                    <option value="{{$key}}" {{ $user_timeZone == $key  ? 'selected="selected"' : '' }}>{{$name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-5 col-lg-2 col-xl-1">
                        <div class="row justify-content-start align-items-center p-l-xs">
                            <button type="button" class="btn btn-example" id="filterCamapign">{{ trans("transaction-history.request") }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="content-data p-t-lg">
            <table id="tableListCampaign" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans("transaction-history.date") }}</th>
                    <th>{{ trans("transaction-history.campaign") }}</th>
                    <th>{{ trans("transaction-history.client") }}</th>
                    <th>{{ trans("transaction-history.client_type") }}</th>
                    <th>{{ trans("transaction-history.total_charge") }}</th>
                    <th>{{ trans("transaction-history.currency") }}</th>
                    <th>{{ trans("transaction-history.action") }}</th>
                </tr>
                </thead>
            </table>
            <div class="col-12">
                <div class="row justify-content-center">
                    <button class="btn btn-example" id="exportCSVCampaign">{{ trans("transaction-history.export_to_csv") }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection