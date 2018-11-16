@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.update_status') }}</h1>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-9">
                    <div class="container-fluid">
                        <h5 class="row justify-content-center title-add">{{ trans("subscriber.copy_and_paste") }}</h5>
                    </div>
                    <div class="p-b-50 p-l-xl">
                        <form class="form-add" id="subscriberForm" actions="{{ $list_id }}/update/copy-paste" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="list_id" value="{{ $list_id }}" />
                            <div class="row justify-content-center">
                                <div class="col-xs-12 col-sm-11 col-md-10 col-lg-8 col-xl-6">
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <div class="row">
                                                <label class="col-xs col-sm-6 col-form-label font-bold">
                                                    {{ trans('subscriber.change_status_to') }}:
                                                </label>
                                                <div class="col-xs col-sm-6">
                                                    <div class="row justify-content-start">
                                                        <select
                                                            name="status"
                                                            class="custom-select form-control"
                                                            class="custom-select form-control"
                                                        >
                                                            <option value="SUBSCRIBED" selected>{{ trans('subscriber.active') }}</option>
                                                            <option value='UNSUBSCRIBED'>{{ trans('subscriber.inactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-group m-t-xl">
                                            <label class="col-form-label font-bold">
                                                {{ trans('subscriber.mobile_number') }}:
                                            </label>
                                        </div>
                                        <div class="col-12 form-group">
                                            <textarea
                                                class="form-control"
                                                name="content"
                                                rows="10"
                                                data-rule-required="true"
                                                data-msg-required="{{ trans('validationForm.content.required') }}"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <a href={{ url("admin/subscribers/" . $list_id . "/update") }} class="btn btn-secondary">{{ trans("subscriber.back") }}</a>
                                <button type="submit" class="btn btn-example">{{ trans("subscriber.next") }}</button>
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
