@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
<script type="text/javascript">
$(document).ready(function() {
    window.link = {}
});
</script>
@endsection @section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('campaign.notification_settings') }}</h1>
    </div>
    <div class="sub-tab">
        <ul class="tabs">
            @if(Auth::user()->isGroup3())
                <li class="report-campaign">
                    <a href="{{ route('campaign.index') }}">{{ trans("header.campaign_list")}}</a>
                </li>
                <li class="report-center {{Route::currentRouteName() == 'notification-settings.index' ? 'active' : ''}}">
                    <a href="{{ route("notification-settings.index") }}" >{{ trans("header.notification_settings") }}</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="main-content">
        <div class="col">
            <div class="row">
                <div class="col-md-8 ml-5">
                    <h3 class="mt-4 mb-4">{{ trans("campaign.enter_emails_to_receive_campaign_notifications") }}</h3>
                    <form action="{{ route('notification-settings.add') }}" method="post" id="formNotificationSetting">
                        {{ csrf_field() }}
                        <div class="form-group item-field">
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 d-flex">
                                <label class="row justify-content-start align-items-center">{{ trans("campaign.schedule") }}</label>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 ">
                                <div class="row justify-content-start">
                                    <textarea
                                        name="scheduled"
                                        rows="2"
                                        class="form-control text-left"
                                        placeholder="{{ trans('campaign.email') }}"
                                        data-rule-emailMultipleRegex="true"
                                        data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    >{{ $notification ? trim($notification->notification->scheduled) : ''}}</textarea>
                                </div>
                                <div class="row multiple-email">
                                    <small>
                                        {{ trans("campaign.multiple_values_separated_by_comma") }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group item-field">
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 d-flex">
                                <label class="row justify-content-start align-items-center">{{ trans("campaign.in_progress") }}</label>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 ">
                                <div class="row justify-content-start">
                                    <textarea
                                        name="progress"
                                        rows="2"
                                        class="form-control text-left"
                                        placeholder="{{ trans('campaign.email') }}"
                                        data-rule-emailMultipleRegex="true"
                                        data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    >{{ $notification ? trim($notification->notification->progress) : ''}}</textarea>
                                </div>
                                <div class="row multiple-email">
                                    <small>
                                        {{ trans("campaign.multiple_values_separated_by_comma") }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group item-field">
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 d-flex">
                                <label class="row justify-content-start align-items-center">{{ trans("campaign.paused") }}</label>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 ">
                                <div class="row justify-content-start">
                                    <textarea
                                        name="paused"
                                        rows="2"
                                        class="form-control text-left"
                                        placeholder="{{ trans('campaign.email') }}"
                                        data-rule-emailMultipleRegex="true"
                                        data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    >{{ $notification ? trim($notification->notification->paused) : ''}}</textarea>
                                </div>
                                <div class="row multiple-email">
                                    <small>
                                        {{ trans("campaign.multiple_values_separated_by_comma") }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group item-field">
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 d-flex">
                                <label class="row justify-content-start align-items-center">{{ trans("campaign.finished") }}</label>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 ">
                                <div class="row justify-content-start">
                                    <textarea
                                        name="finished"
                                        rows="2"
                                        class="form-control text-left"
                                        placeholder="{{ trans('campaign.email') }}"
                                        data-rule-emailMultipleRegex="true"
                                        data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    >{{ $notification ? trim($notification->notification->finished) : ''}}</textarea>
                                </div>
                                <div class="row multiple-email">
                                    <small>
                                        {{ trans("campaign.multiple_values_separated_by_comma") }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group item-field">
                            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 d-flex">
                                <label class="row justify-content-start align-items-center">{{ trans("campaign.failed") }}</label>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 ">
                                <div class="row justify-content-start">
                                    <textarea
                                        name="failed"
                                        rows="2"
                                        class="form-control text-left"
                                        placeholder="{{ trans('campaign.email') }}"
                                        data-rule-emailMultipleRegex="true"
                                        data-msg-emailMultipleRegex="{{ trans("validationForm.email.multiple") }}"
                                    >{{ $notification ? trim($notification->notification->failed) : ''}}</textarea>
                                </div>
                                <div class="row multiple-email">
                                    <small>
                                        {{ trans("campaign.multiple_values_separated_by_comma") }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="from-group text-center mb-5 mt-4">
                            <button class="btn btn-example">
                                {{ trans("campaign.save") }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="d-none col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Campaign</strong></p>
                        <br>
                        <span class="d-block m-t-md">
                            Set your settings
                            for your new campaign on the left. You can
                            create a regular email campaign or a/b split
                            test campaign.
                        </span>
                        <span class="d-block m-t-lg">
                            You will define your
                            contents and scheduling for your email
                            campaign in next steps.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection