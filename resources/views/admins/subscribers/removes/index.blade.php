@extends("layouts.admin")

@section("title", "Remove Subscriber")
@section("custom-script")
    <script src="{{ asset('js/removeSubscriber.js') }}"></script>
@endsection
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.remove_subscribers') }}</h1>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-9">
                    <div class="p-b-50 p-l-xl">
                        <form class="form-add" id="subscriberForm" action="{{ route('remove-subscriber', array('id' => $list_id))}}" method="post">
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
                                                            {{ $subscriber_list['name'] }}
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
                                                            <option value='MOBILE'>{{ trans('subscriber.mobile_number') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group m-t-xl" id="text-phone">
                                            <div class="row">
                                                <label class="col-xs-12 col-sm-4 col-form-label font-bold justify-content-end">
                                                </label>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="row justify-content-start">
                                                        <textarea
                                                        rows="7"
                                                        class="form-control"
                                                        data-rule-required="true"
                                                        data-msg-required="{{ trans("validationForm.content.required") }}"
                                                        name="content"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group m-t-xl m-b-xl" id="checkbox-remove">
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" name="flagSupperssed" class="custom-control-input">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">{{ trans("subscriber.add_removes_subscribers") }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center m-t-xl">
                                <a href={{ route('subscriber-list.detail', array('id' => $list_id)) }} class="btn btn-secondary">{{ trans("subscriber.i_am_done") }}</a>
                                <button type="submit" class="btn btn-example">{{ trans("subscriber.remove_subscribers") }}</button>
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
