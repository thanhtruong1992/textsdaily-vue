@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script>
        const url_export_csv = '<?php echo route(Auth::user()->isGroup1() ? "transaction-histories.campaigns.detail.csv" : "transaction-histories-2.campaigns.detail.csv", array("user_id" => $user_id, "campaign_id" => $campaign_id)) ?>';

        jQuery(function(){
        	$("#exportCSVCampaign").on("click", function() {
                window.open(url_export_csv, "_blank");
            });
        });
    </script>
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('transaction-history.service_provider') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            <li class="client">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.client.index") : route("transaction-histories-2.client.index") }}">{{ trans("transaction-history.client") }}</a>
            </li>
            <li class="report-center">
                <a href="{{ !!Auth::user()->isGroup1() ? route("transaction-histories.campaigns.index") : route("transaction-histories-2.campaigns.index") }}">{{ trans("transaction-history.cost_campaign") }}</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="col-12 p-t-lg">
            <div class="row justify-content-center">
                <div class="info-campagin">
                    <div class="m-b-sm">
                        <span class="title text-bold">{{ trans("transaction-history.campaign") }}:</span>
                        <span>{{ $campaign->name }}</span>
                    </div>
                    <div class="m-b-sm">
                        <span class="title text-bold">{{ trans("transaction-history.date") }}:</span>
                        <span>{{ $campaign->send_time }}</span>
                    </div>
                    <div class="m-b-sm">
                        <span class="title text-bold">{{ trans("transaction-history.client") }}:</span>
                        <span>{{ $campaign->user_name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-data p-t-lg">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>{{ trans("transaction-history.number_of_sent_sms") }}</th>
                    <th>{{ trans("transaction-history.service_provider") }}</th>
                    <th>{{ trans("transaction-history.country") }}</th>
                    <th>{{ trans("transaction-history.network") }}</th>
                    <th>{{ trans("transaction-history.unit_price") }}</th>
                    <th>{{ trans("transaction-history.currency") }}</th>
                    <th>{{ trans("transaction-history.total_charge") }}</th>
                </tr>
                </thead>
                <tbody>
                    <?php $total = 0; $currency = ""; ?>
                    @foreach($reports as $item)
                        <?php $total += $item->total_charge; $currency = $item->currency; ?>
                        <tr>
                            <td>{{ $item->totals }}</td>
                            <td>{{ $item->service_provider }}</td>
                            <td>{{ $item->country }}</td>
                            <td>{{ $item->network }}</td>
                            <td>{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->currency }}</td>
                            <td>{{ number_format($item->total_charge, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="text-bold text-right text-uppercase" colspan="6">{{ trans("transaction-history.total") }}</td>
                        <td class="">{{ number_format($total, 2) . " " . $currency }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="col-12 p-r-xl">
                <div class="row justify-content-center">
                    <a href={{ url()->previous() }} class="btn btn-primary" >{{ trans("transaction-history.back") }}</a>
                    <button class="btn btn-example" id="exportCSVCampaign">{{ trans("transaction-history.export_to_csv") }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection