@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
    <script type="text/javascript">
    $(document).ready(function() {
        window.link = {};
        window.link.updateCampaignLink = "{{ Auth::user()->isGroup3() ? route('campaign.info', ['id' =>'__id']) : route('campaign4.info', ['id' =>'__id']) }}";
    });
    </script>
    @if($subscriber_list->is_global == 1)
        <script src="{{ asset('js/supperssionListDataTable.js') }}"></script>
    @else
        <script src="{{ asset('js/subscribersDataTable.js') }}"></script>
    @endif
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>
            {{ trans("subscriber.browseSubscriberTitle") . ":" }}
            <span class="title-subscriber-list">{{ $subscriber_list->name }}</span>
        </h1>
    </div>
    <div class="main-content">
        <div class="content-data">
                <input type="hidden" name="list_id" value="{{ $list_id }}" />
                <div class="row custom-filter">
                    <div class="col-xs-12 col-sm-3">
                        <div class="row">
                            <div class="col-12 m-t-lg">
                                <button type="button" class="btn btn-example btn-custom" id="resetFormFilter">
                                    {{ trans("subscriber.clear_data") }}</button>
                            </div>
                            <div class="col-12 m-t-md">
                                <div class="dropdown">
                                    <button
                                        class="btn btn-example btn-custom dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">{{
                                        trans("subscriber.all_field") }}</button>
                                    <div class="dropdown-menu"
                                        aria-labelledby="dropdownMenuButton">
                                        @foreach($filterFields as $key => $item)
                                        <label
                                            class="dropdown-item form-check-label">
                                            <input type="checkbox"
                                            name="{{ $key }}"
                                            class="form-check-input showRule"
                                            value="{{ $key }}"
                                            checked="checked"/> {{
                                            $item
                                            }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 m-t-lg">
                        <form actions="#" id="formFilter">
                        @foreach($filterFields as $key => $item)
                            <div
                                class="form-group row justify-content-center group" id="{{ $key }}">
                                <label class="col-xs col-sm-4 col-form-label">{{ $item }}</label>

                                @if ($key == "subscriber_status")
                                    <div class="col-xs col-sm-3">
                                        <select name="{{$key}}"
                                            class="form-control {{$key}}_flag">
                                            <option value="=" selected="selected">is</option>
                                            <option value="!=">is not</option>
                                        </select>
                                    </div>
                                    <div class="col-xs col-sm-4">
                                        <div class="justify-content-start">
                                            <select name="{{$key}}"
                                                class="form-control {{$key}}">
                                                <option value="" selected="selected">None</option>
                                                <option value="SUBSCRIBED">Active</option>
                                                <option value="UNSUBSCRIBED">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xs col-sm-3">
                                        <select name="{{$key}}"
                                            class="form-control {{$key}}_flag">
                                            <option value="=" selected="selected">is</option>
                                            <option value="!=">is not</option>
                                            <option value="LIKE">contain</option>
                                            <option value="NOT LIKE">not contain</option>
                                        </select>
                                    </div>
                                    <div class="col-xs col-sm-4">
                                        <div class="justify-content-start">
                                            <input type="text" class="form-control {{$key}}" name="{{$key}}" />
                                        </div>
                                    </div>
                                @endif
                                <div class="col-xs col-sm-1 display-flex">
                                    <div class="row align-items-center">
                                        <i class="fa fa-close close-filter" id="{{ $key }}"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <div class="row">
                            <div class="col-12 m-t-lg">
                                <select
                                    class="form-control btn-custom flag-filter">
                                    <option value="and" selected="selected">{{
                                        trans("subscriber.all_rule") }}</option>
                                    <option value="or"">{{
                                        trans("subscriber.any_rule") }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="row p-l-xs m-t-md">
                                    <button type="button" id="applyFilter"
                                        class="btn btn-example btn-custom">
                                        {{ trans("subscriber.apply_filter")
                                        }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="p-l-xl">
                <div class="title-table">
                    <button type="button" class="title" id="btnCheckAll">
                        {{ trans("subscriber.all") }}
                    </button>
                    <button type="button" class="title" id="btnCheckNone">
                        {{ trans("subscriber.none") }}
                    </button>
                @if(!$user->isGroup4())
                    <button  class="title delete-subscriber">{{
                        trans("subscriber.delete_subscribers") }}</button>
                @endif
                    <button onclick="exportSubscribers()" class="title">{{
                        trans("subscriber.export_results") }}</button>
                    <button onclick="exportSubscribers('true')" class="title">{{
                        trans("subscriber.export_encrypted_results") }}</button>
                    <button class="title-display title p-l-lg">{{
                        trans("subscriber.display") }}</button>
                </div>
                <div class="dropdown dropdown-custom">
                        <button class="btn btn-example dropdown-toggle"
                            type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">{{
                            trans("subscriber.all_field") }}</button>
                        @if(count($headerCustomField) > 0)
                            <div class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton">
                                <!-- <label class="dropdown-item form-check-label"> <input
                                    type="checkbox"
                                    class="form-check-input frmField"
                                    id="frmAllField" /> Check All
                                </label> -->
                                @foreach($headerCustomField as $key => $item)
                                    <label
                                        class="dropdown-item form-check-label"> <input
                                        type="checkbox"
                                        value="{{ 'custom_field_' . $item->id }}"
                                        class="form-check-input frmField show-column" /> {{ $item->field_name }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
            </div>

            <div class="content-data">
                <table id="table-subscriber" class="table table-striped" data-role="table">
                    <thead>
                    <tr>
                        <th class="check-all">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input frmCheck" value="checkAll" id="frmCheckAll"/>
                                <span class="custom-control-indicator"></span>
                            </label>
                        </th>
                        <th>{{ trans("subscriber.no") }}</th>
                        <th>{{ trans("subscriber.phone") }}</th>
                        @if($subscriber_list->is_global == 0)
                            <th>{{ trans("subscriber.first_name") }}</th>
                            <th>{{ trans("subscriber.last_name") }}</th>
                            <th>{{ trans("subscriber.status") }}</th>
                            @foreach($headerCustomField as $item)
                                <th id="{{'custom_field_' . $item->id}}" class="custom-field-table">{{ $item->field_name }}</th>
                            @endforeach
                        @else
                            <th>{{ trans("subscriber.campaign") }}</th>
                            <th>{{ trans("subscriber.unsubscription_date") }}</th>
                        @endif
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
