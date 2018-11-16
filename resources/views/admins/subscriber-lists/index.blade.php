@extends("layouts.admin")
<?php $user = Auth::user(); ?>
@section("custom-script")
<script type="text/javascript">
window.link = {};
window.link.group = "{{Auth::user()->type}}";
window.subscriber = {};
window.subscriber.subscriberAction = '{{ trans("subscriber.subscriber_action") }}';
window.subscriber.detailUrl = "{{ $user->isGroup3() ? route('subscibers.detail', ['id' => '__ID__']) : route('subscribers_list_group4.info', ['id' => '__ID__']) }}";
window.subscriber.viewAddUrl = "{{ route('subscibers.viewAdd', ['id' => '__ID__']) }}";
window.subscriber.viewUpdateUrl = "{{ route('subscibers.viewUpdate', ['id' => '__ID__']) }}";
window.subscriber.viewRemoveUrl = "{{ route('subscibers.viewRemove', ['id' => '__ID__']) }}";
window.subscriber.exportSubscriberUrl = "{{ $user->isGroup3() ? route('export-subscriber.view', array('list_id' => '__ID__')) : route('export-subscriber-4.view', array('list_id' => '__ID__'))}}";
window.subscriber.add_subscribers = '{{ trans("subscriber.add_subscribers") }}';
window.subscriber.update_subscribers_status = '{{ trans("subscriber.update_subscribers_status") }}';
window.subscriber.remove_subscribers = '{{ trans("subscriber.remove_subscribers") }}';
window.subscriber.export_subscribers = '{{ trans("subscriber.export_subscribers") }}';
window.subscriber.browse_subscribers = '{{ trans("subscriber.browse_subscribers") }}';
</script>
<script src="{{ asset('js/subscriberListDataTable.js') }}"></script>
@endsection
@section("title", "DashBoard")
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans("subscriber.subscriber_list") }}</h1>
        </div>
        <div class="main-content">
            <div class="header-button">
                <div class="col">
                    <div class="justify-content-start">
                        <div class="custom-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text" id="input-search" class="input-search" placeholder="{{ trans("campaign.search") }}" />
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row justify-content-end m-r-sm">
                        @if(Auth::user()->isGroup3())
                        <a class="btn btn-example" href="{{ url("admin/subscriber-lists/add") }}">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ trans("subscriber.create_new_list") }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="content-data">
                <table id="table-subscribers" class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans("subscriber.subscriber_list") }}</th>
                        <th>{{ trans("subscriber.last_update") }}</th>
                        <th>{{ trans("subscriber.active_subscriber") }}</th>
                        <th>{{ trans("subscriber.inactive_subscriber") }}</th>
                        <th>{{ trans("subscriber.actions") }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
