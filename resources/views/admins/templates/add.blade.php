@extends("layouts.admin") @section("title", "DashBoard")

@section('custom-script')
<script type="text/javascript">
window.link = {};
window.link.api_get_personalize = "{{ route('customfield.personalize') }}"
</script>
<script src="{{ asset('js/addTemplate.js') }}"></script>
@endsection

@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ $template ? trans("template.title_edit") : trans("template.title_add") }}</h1>
    </div>
    <div class="main-content">
        <div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="templateForm" action="{{ $template ? route('templates.update', ['id' => $template->id]) : route('templates.store') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="submit_form" value="false" />
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("template.template_name") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="name"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.template_name.required') }}"
                                        placeholder="{{ trans('template.template_name') }}"
                                        class="form-control input-custom"
                                        value="{{ $template ? old('name', $template->name): '' }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("template.language") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <label class="custom-control custom-radio">
                                        <input id="ascii" type="radio" value="ASCII" name="language"
                                        class="custom-control-input"
                                        onchange="calculateMessage(event)"
                                        {{ ( ($template ? old('language', $template->language) : old('language', 'ASCII')) == 'ASCII' ) ? 'checked="checked"' : '' }} />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">ASCII</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input id="unicode" type="radio" value="UNICODE" name="language"
                                        class="custom-control-input"
                                        onchange="calculateMessage(event)"
                                        {{ ( ($template ? old('language', $template->language) : old('language')) == 'UNICODE' ) ? 'checked="checked"' : '' }} />
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Unicode</span>
                                    </label>
                                </div>
                            </div>
                            <label class="col-xs col-sm-4 col-form-label">{{ trans("template.message") }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <textarea rows="3" id="message"
                                        name="message"
                                        class="form-control"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.message.required') }}"
                                        onkeyup="calculateMessage(event)">{{ trim( $template ? old('message', $template->message) : old('message')) }}</textarea>
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
                                        data-toggle="modal"
                                        data-target="#personalizeModal">{{ trans("template.personalize") }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <button id="create-template" type="button" class="btn btn-example ml-0">
                                    {{ $template ? trans("template.update") : trans("template.create") }}
                                </button>
                                <button id="reset-template" type="button" class="btn btn-info mr-2">
                                    {{ trans("template.reset") }}
                                </button>
                                <a class="btn btn-secondary ml-0" href="{{ route('templates.index') }}">
                                    {{ trans("template.cancelBtn") }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
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

@include("admins.modals.modal-group")
@include("admins.modals.modal-summary-campaign")
@include("admins.modals.modal-add-url")
@include("admins.modals.modal-personalize-message")
@include("admins.modals.modal-template")
@include("admins.modals.modal-sms-review")
