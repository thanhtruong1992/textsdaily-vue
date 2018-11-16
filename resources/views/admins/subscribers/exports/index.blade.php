@extends("layouts.admin")

@section("title", "Remove Subscriber")
@section("custom-script")
@endsection
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.export_subscribers') }}</h1>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-9">
                    <div class="p-b-50 p-l-xl">
                        <form class="form-add" id="subscriberForm" action="{{ !!Auth::user()->isGRoup3() ? route('export-subscriber.post', array('id' => $list_id)) : route('export-subscriber-4.post', array('id' => $list_id))}}" method="post">
                            {{ csrf_field() }}
                            <div class="row justify-content-center">
                                <div class="col-xs-12 col-sm-11 col-md-10 col-lg-9 col-xl-7">
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <div class="row">
                                                <label class="col-xs col-sm-4 col-form-label font-bold justify-content-end">
                                                    {{ trans('subscriber.list_name') }}:
                                                </label>
                                                <div class="col-xs col-sm-6">
                                                    <div class="row justify-content-start">
                                                        <label class="col-form-label">
                                                            {{ $subscriber_list->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group m-t-xl">
                                            <div class="row">
                                                <label class="col-xs-12 col-sm-4 col-form-label font-bold justify-content-end">
                                                    {{ trans('subscriber.subscirbers') }}:
                                                </label>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="row justify-content-start">
                                                        <select
                                                            name="status"
                                                            class="custom-select form-control"
                                                            id="select-subscriber"
                                                        >
                                                            <option value="SUBSCRIBED" selected>{{ trans('subscriber.active_subscribers') }}</option>
                                                            <option value='UNSUBSCRIBED'>{{ trans('subscriber.inactive_subscribers') }}</option>
                                                            <option value='SUPPERSSED'>{{ trans('subscriber.supperssed_subscribers') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group m-t-xl">
                                            <div class="row">
                                                <label class="col-xs-12 col-sm-4 col-form-label font-bold justify-content-end align-items-start ">
                                                    {{ trans('subscriber.export_fields') }}:
                                                </label>
                                                <div class="col-xs-12 col-sm-6">
                                                    @foreach ($fields as $item)
                                                            <div class="row justify-content-start m-b-xs">
                                                                <label class="custom-control custom-checkbox">
                                                                    <input type="checkbox" name="{{ $item->key }}" class="custom-control-input" checked="checked" {{ $item->field == 'phone' ? 'disabled="disabled"' : '' }} />
                                                                    <span class="custom-control-indicator"></span>
                                                                    <span class="custom-control-description">{{ $item->name }}</span>
                                                                </label>
                                                            </div>
                                                        @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center m-t-xl">
                                <a href={{ route('subscriber-list.detail', array('id' => $list_id)) }} class="btn btn-secondary">{{ trans("subscriber.i_am_done") }}</a>
                                <button type="submit" class="btn btn-example">{{ trans("subscriber.export_subscribers") }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-none col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
                        <br>
                        <span class="d-block m-t-md"> Set your settings for
                            your new campaign on the left. You can create a
                            regular email campaign or a/b split test
                            campaign. </span> <span class="d-block m-t-lg">
                            You will define your contents and scheduling for
                            your email campaign in next steps. </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
