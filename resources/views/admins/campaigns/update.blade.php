@extends("layouts.admin") @section("title", "DashBoard")
@section('custom-script')
<script type="text/javascript">
window.link = {};
window.link.test_recipients_error_message = "{{ trans('validationForm.phone_number_test.required') }}";
window.link.api_test_send_sms = "{{ route('campaign.test_sms') }}";
window.link.api_add_sender = "{{ route('campaign.add_sender') }}";
window.link.api_get_personalize = "{{ route('customfield.personalize') }}";
window.link.api_get_template = "{{ route('template.template') }}";
window.link.api_add_ubsubscriber = "{{ route('campaign.unsubscriber', ['campaign_id' => $item->id]) }}";
const not_enough_balance_with_personalize = "{{ trans('notify.not_enough_balance_with_personalize') }}";
const not_enough_balance = "{{ trans('notify.not_enough_balance') }}";
const enough_balance = "{{ trans('notify.enough_balance') }}";
const limit_balance_when_create_campaign = "{{ trans('notify.limit_balance_when_create_campaign') }}";
</script>
<script>
    jQuery(function($){
        @if($item->status === "DRAFT" || ($item->status === "READY" && $item->schedule_type === "FUTURE"))
        $('input[name="schedule_type"]').on('change', function(){
            if( $(this).val() == 'FUTURE' ) {
                $('#inputDate').removeAttr('disabled');
                $('#send_timezone').removeAttr('disabled');
            } else {
                $('#inputDate').attr('disabled', 'disabled');
                $('#send_timezone').attr('disabled', 'disabled');
                $("#validDate").css("display", "none");
            	$("#inputDate").removeClass("error");
            }
        })
        @endif
    });
</script>
<script type="text/javascript" src="{{ asset('js/updateCampaign.js') }}"></script>
@endsection

@section("content")
<?php
    $isDisabled = false;
    if($item->status === "DRAFT" || ($item->status === "READY" && $item->schedule_type === "FUTURE")) {
        $isDisabled = true;
    }
    if (Auth::user()->type === "GROUP4") {
        $isDisabled = false;
    }
?>
<div class="main">
    <div class="main-title">
        <h1>{{ trans("campaign.titleUpdate") }}</h1>
    </div>
    <div class="main-content">
        <div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="campaignForm" action="{{ route('campaign.update', ['id' => $item->id]) }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="campaign_id" value="{{ $item->id }}" />
                        <input type="hidden" name="submit_form" value="false" />
                        <input type="hidden" name="isPersonalize" id="isPersonalize" value="false" />
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.campaign_name") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="name"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.campaign_name.required') }}"
                                        placeholder="Campaign Name"
                                        class="form-control input-custom"
                                        value="{{ old('name', $item->name) }}"
                                        @if(!$isDisabled) disabled @endif />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.subscriber_list") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <select class="form-control select2 select-multiple select2-custom"
                                        id="selectList"
                                        name="list_id[]"
                                        multiple="multiple"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.subscriber_list_name.required') }}"
                                        data-placeholder="Select"
                                        @if(!$isDisabled) disabled @endif >
                                        @foreach($allList as $id => $name)
                                            <option value="{{$id}}" {{ ( in_array($id, old('list_id', $list_subscriber_id)) ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.sender") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <select
                                        id="sender"
                                        class="form-control select2 select2-custom select-sender"
                                        name="sender"
                                        data-placeholder="{{ trans('campaign.sender') }}"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.sender.required') }}"
                                        value="{{ old('sender', $item->sender) }}"
                                        @if(!$isDisabled) disabled @endif>
                                        @foreach($senderList as $id => $name)
                                            <option value="{{$name}}" {{ ( $id == old('sender', $item->sender)) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.language") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <label class="custom-control custom-radio">
                                        <input id="ascii" type="radio" value="ASCII" name="language"
                                        class="custom-control-input"
                                        onchange="calculateMessage(event)"
                                        {{ ( old('language', $item->language) == 'ASCII' ) ? 'checked="checked"' : '' }}
                                        @if(!$isDisabled && $item->language !== 'ASCII')
                                            disabled
                                        @endif />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">ASCII</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input id="unicode" type="radio" value="UNICODE" name="language"
                                        class="custom-control-input"
                                        onchange="calculateMessage(event)"
                                        {{ ( old('language', $item->language) == 'UNICODE' ) ? 'checked="checked"' : '' }}
                                        @if(!$isDisabled && $item->language !== 'UNICODE')
                                            disabled
                                        @endif />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Unicode</span>
                                    </label>
                                </div>
                            </div>
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.message") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <textarea rows="3" id="message"
                                        name="message"
                                        class="form-control"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.message.required') }}"
                                        onkeyup="calculateMessage(event)"
                                        @if(!$isDisabled) disabled @endif >{{ trim( old('message', $item->message) ) }}</textarea>
                                </div>
                                <div class="m-t-xs">
                                    <span id="lengthMessage" class="total-message"> 0 Char(s) </span>
                                    <span class="total-message">- </span>
                                    <span id="totalMessage" class="total-message" total="1"> 1 SMS </span>
                                </div>
                                <div class="">
                                    <button type="button"
                                        id="loadPersonalize"
                                        class="btn btn-remove"
                                    @if(!$isDisabled) disabled @endif>{{ trans("campaign.personalize") }}</button>
                                @if($isTrackingLink)
                                    {{-- <button type="button"
                                        class="btn btn-remove"
                                        id="addUrl" data-toggle="modal"
                                        data-target="#addUrlModal"
                                        @if(!$isDisabled) disabled @endif>{{ trans("campaign.insert_url") }}</button> --}}
                                        <div class="dropdown dropdown-custom">
                                                <button 
                                                    class="btn btn-remove dropdown-toggle" 
                                                    id="dropdownMenu2" 
                                                    data-toggle="dropdown" 
                                                    aria-haspopup="true" 
                                                    aria-expanded="false"
                                                    @if(!$isDisabled) disabled @endif
                                                >
                                                    {{ trans("campaign.insert_url") }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                    <button
                                                        type="button"
                                                        class="dropdown-item" 
                                                        id="addUrl" 
                                                        data-toggle="modal"
                                                        data-target="#addUrlModal"
                                                        @if(!$isDisabled) disabled @endif
                                                    >{{ trans("campaign.base_url") }}</button>
                                                    <button 
                                                        type="button"
                                                        id="loadPersonalizeUrl"
                                                        class="dropdown-item"
                                                        data-toggle="modal"
                                                        data-target="#personalizeUrlModal"
                                                        @if(!$isDisabled) disabled @endif
                                                    >{{ trans("campaign.personalize_url") }}</button>
                                                </div>
                                            </div>
                                @endif
                                    <button type="button"
                                        id="loadTemplate"
                                        class="btn btn-remove"
                                        @if(!$isDisabled) disabled @endif>{{ trans("campaign.template") }}</button>
                                    <button type="button" id="unsubscribe" class="btn btn-remove" @if(!$isDisabled) disabled @endif>
                                        {{ trans("campaign.unsubscribe") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.schedule_date") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <label class="custom-control custom-radio">
                                        <input type="radio" name="schedule_type" value="NOT_SCHEDULED"
                                        class="custom-control-input"
                                        {{ ( old('schedule_type', $item->schedule_type) == 'NOT_SCHEDULED' ) ? 'checked="checked"' : '' }}
                                        @if(!$isDisabled && $item->schedule_type !== 'NOT_SCHEDULED')
                                            disabled
                                        @endif />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description"> {{ trans("campaign.draft") }}</span>
                                    </label>
                                </div>
                                <div class="justify-content-start">
                                    <label class="custom-control custom-radio">
                                        <input type="radio" name="schedule_type" value="IMMEDIATE"
                                        class="custom-control-input"
                                        {{ ( old('schedule_type', $item->schedule_type) == 'IMMEDIATE' ) ? 'checked="checked"' : '' }}
                                        @if(!$isDisabled && $item->schedule_type !== 'IMMEDIATE')
                                            disabled
                                        @endif />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ trans("campaign.now") }}</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="custom-control custom-radio mr-0">
                                        <input type="radio" name="schedule_type" value="FUTURE"
                                        class="custom-control-input"
                                        {{ ( old('schedule_type', $item->schedule_type) == 'FUTURE' ) ? 'checked="checked"' : '' }}
                                        @if(!$isDisabled && $item->schedule_type !== 'FUTURE')
                                            disabled
                                        @endif />
                                        <span class="custom-control-indicator radio-custom"></span>
                                        <span class="custom-control-description">
                                            <span class="pull-left mb-1">
                                                <?php $sendTime = ($item->send_time) ? date('Y/m/d H:i', strtotime($item->send_time) ) : ''; ?>
                                                <input type="text" id="inputDate" name="send_time" disabled="disabled"
                                                data-rule-required="true"
                                                autocomplete="off"
                                                data-msg-required="{{ trans('validationForm.send_time.required') }}"
                                                class="form-control input-date disabled" placeholder="Date"
                                                value="{{ old('send_time', $sendTime ) }}" />
                                                <span id="validDate" class="has-error">{{ trans('validationForm.datetime.valid') }}</span>
                                            </span>
                                            <span class="pull-left">
                                                <select id="send_timezone" name="send_timezone" disabled="disabled"
                                                data-rule-required="true"
                                                data-msg-required="{{ trans("validationForm.send_timezone.required") }}"
                                                class="form-control input-timezone">
                                                    <option value="">{{ trans("campaign.timezone") }}</option>
                                                    @foreach($timeZone as $key => $name)
                                                        <option value="{{$key}}" {{ ( old('send_timezone', ( isset($item->send_timezone) ? $item->send_timezone : $user_timezone)) == $key ) ? 'selected="selected"' : '' }}>{{$name}}</option>
                                                    @endforeach
                                                </select>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center group">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("campaign.valid_period") }}</label>
                            <div class="select-group col-sm-8">
                                <div class="justify-content-start">
                                    <select name="valid_period"
                                        class="form-control"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.valid_period.required') }}"
                                        @if(!$isDisabled) disabled @endif>
                                        <option value="">{{ trans("campaign.select_hours") }}</option>
                                        @for( $i = 1; $i <= 24; $i++ )
                                            <option value="{{ $i }}" {{ ( old('valid_period', $item->valid_period) == $i ) ? 'selected="selected"' : '' }}>{{ $i }} Hour(s)</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4">
                                <span class="notify-email">{{ trans("campaign.send_test_message") }}</span>
                            </label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start row">
                                    <div class="col-9">
                                        <input type="text"
                                            name="test_recipients"
                                            class="form-control input-custom test_message"
                                            placeholder="{{ trans('campaign.enter_test_number') }}"
                                            value="{{ old('test_recipients', $item->test_recipients) }}"
                                            autocomplete="off"
                                            @if(!$isDisabled) disabled @endif/>
                                    </div>
                                    <div class="col-3 row">
                                        <button type="button" class="btn btn-example button-send"
                                        @if(!$isDisabled) disabled @endif>
                                            {{ trans("campaign.send") }}
                                        </button>
                                    </div>
                                </div>
                                <div class="m-t-xs multiple-email">
                                    {{ trans("campaign.multiple_phone") }} - {{ trans("campaign.note_test_message") }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                            @if($isDisabled)
                                <button id="update-campaign" type="button" class="btn btn-example ml-0">
                                    {{ trans("campaign.update") }}
                                </button>
                                <button id="reset-campaign" type="button" class="btn btn-info mr-2">
                                    {{ trans("campaign.reset") }}
                                </button>
                                <a class="btn btn-secondary ml-0" href="{{ Auth::user()->isGroup3() ? url('admin/campaigns') : url('reader/campaigns') }}">
                                    {{ trans("campaign.cancelBtn") }}
                                </a>
                            @else
                                <a class="btn btn-secondary ml-0" href="{{ url()->previous() }}">
                                    {{ trans("campaign.back") }}
                                </a>
                            @endif

                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none d-md-inline-block col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
                        <span class="d-block m-t-md">
                            Set your settings for your new campaign on the left. You can create a regular SMS campaign.
                        </span>
                        <span class="d-block m-t-xs">
                            You will define your contents and scheduling for your SMS campaign in next steps.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include("admins.modals.modal-group")
@include("admins.modals.modal-add-url")
@include("admins.modals.modal-sms-review")
@include("admins.modals.modal-personalize-message")
@include("admins.modals.modal-template")
@include("admins.modals.modal-summary-campaign")
@include("admins.modals.modal-confirm-campaign")
@include("admins.modals.modal-personalize-url")