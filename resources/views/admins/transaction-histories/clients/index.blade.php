@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script src="{{ asset("js/transactionHistoryClient.js") }}"></script>
    <script type="text/javascript">
        window.link = {};
        window.link.api_get_client_transaction = "{{ $user->isGroup1() ? route('transaction-histories.clients'):route('transaction-histories-2.clients') }}"
    	window.link.api_export_client_transaction = "{{ $user->isGroup1() ? route('transaction-histories.client.csv'):route('transaction-histories-2.client.csv') }}"
    </script>
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('transaction-history.client') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            <li class="client active">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.client.index") : route("transaction-histories-2.client.index") }}">{{ trans("transaction-history.client") }}</a>
            </li>
            <li class="report-center">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.campaigns.index") : route("transaction-histories-2.campaigns.index") }}">{{ trans("transaction-history.cost_campaign") }}</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="col-12 p-t-lg">
            <form id="formFilterClient">
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
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 col-xl-3">
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
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xl-4">
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
                        <div class="row justify-content-start align-items-center">
                            <button type="button" id="queryClientTransaction" class="btn btn-example">{{ trans("transaction-history.request") }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="content-data p-t-lg">
            <table id="table-transaction-histories-client" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans("transaction-history.date") }}</th>
                    <th>{{ trans("transaction-history.description") }}</th>
                    <th>{{ trans("transaction-history.action") }}</th>
                    <th>{{ trans("transaction-history.client") }}</th>
                    <th>{{ trans("transaction-history.client_type") }}</th>
                    <th>{{ trans("transaction-history.change") }}</th>
                    <th>{{ trans("transaction-history.currency") }}</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="col">
            <div class="col">
                <div class="row justify-content-end pb-3 pr-5">
                    <a id="exportTransactionClient" class="btn btn-example" href="#">
                        {{ trans("transaction-history.export_to_csv") }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection