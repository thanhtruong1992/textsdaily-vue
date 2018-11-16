@extends("layouts.admin")

@section("title", "DashBoard")
@section("custom-script")
    <script>
        const goolge_key = "{{ env('KEY_SHORT_LINK_GOOGLE') }}";
    </script>
    <script src="{{ asset('js/subscriberListViewDataTable.js') }}"></script>
@endsection
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans("subscriber.overview_subscriber") . ": " . $subscrilerList->name }}</h1>
        </div>
        <div class="main-content">
            <div class="header-button">
                <div class="col">
                    <div class="row justify-content-end">
                        <div class="column" dropdown">
                            <button class="btn btn-example dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans("subscriber.subscriber_action") }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ $user->isGroup3() ? route('subscibers.detail', ['id'=>$list_id]) : route('subscribers_list_group4.info', ['id'=>$list_id]) }}">{{ trans("subscriber.browse_subscribers") }}</a>
                                @if($user->isGroup3())
                                    <a class="dropdown-item" href="{{  url("admin/subscribers/$list_id/add")  }}">{{ trans("subscriber.add_subscribers") }}</a>
                                    <a class="dropdown-item" href="{{  url("admin/subscribers/$list_id/update")  }}">{{ trans("subscriber.update_subscribers_status") }}</a>
                                    <a class="dropdown-item" href="{{  url("admin/subscribers/$list_id/remove")  }}">{{ trans("subscriber.remove_subscribers") }}</a>
                                @endif
                                <a class="dropdown-item" href="{{ $user->isGroup3() ? route('export-subscriber.view', array('list_id' => $list_id)) : route('export-subscriber-4.view', array('list_id' => $list_id))}}">{{ trans("subscriber.export_subscribers") }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-data">
                <div class="row">
                    <div id="columnChart" class="chartdiv"></div>
                    <div id="mapChart"></div>
                </div>
                <table class="table table-striped m-t-lg" id="subscriberListOverview">
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
