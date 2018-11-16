@extends("layouts.admin")
@section("title", trans('inbound-messages.inboundMessagesTitle'))

@section("custom-script")
<script type="text/javascript">
window.settings = {};
window.settings.InboundMessages = {};
window.settings.InboundMessages.getInboundMessagesUrl = "{{ route('ajaxGetInboundMessages') }}";
window.settings.InboundMessages.exportInboundMessage = "{{ route('inbound-message.export') }}";
</script>
<script src="{{ asset('js/inbound-messages.js') }}"></script>
@endsection

@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('inbound-messages.inboundMessagesTitle') }}</h1>
    </div>
    <div class="main-content">
        <div class="filter-request">
            <form id="formReportCenter">
                <div class="col-12">
                    <div class="form-group row pt-4">
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
                                    <select name="timezone" id="timezone" class="form-control">
                                        @foreach($timeZone as $key => $name)
                                            <option value="{{$key}}" {{ $client->time_zone == $key  ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">{{ trans("inbound-messages.subscriber_number") }}:</label>
                            <input type="text" name="subscriber_number" id="subscriber_number" class="form-control input-custom" value="" />
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">{{ trans("inbound-messages.hosted_number") }}:</label>
                            <input type="text" name="hosted_number" id="hosted_number" class="form-control input-custom" />
                        </div>
                    </div>
                    <div class="form-group row justify-content-center p-t-md">
                        <button type="button" class="btn btn-example" id="request">
                            {{ trans("inbound-messages.search") }}
                        </button>
                        <button type="button" class="btn btn-example" id="export">
                            {{ trans("inbound-messages.export") }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="content-data">
            <table id="table-inbound-messages" class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans("inbound-messages.receivedDate") }}</th>
                        <th>{{ trans("inbound-messages.hostedNumber") }}</th>
                        <th>{{ trans("inbound-messages.subscriberNumber") }}</th>
                        <th>{{ trans("inbound-messages.smsContent") }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection