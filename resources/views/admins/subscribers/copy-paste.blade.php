@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.addForm.add') }}</h1>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-9">
                    <div class="container-fluid">
                        <h5 class="row justify-content-center title-add">{{ trans("subscriber.copy_and_paste") }}</h5>
                        <div class="row justify-content-between">
                            <div class="col">
                                <div class="row align-items-center p-t-xl p-l-lg font-bold import-result">
                                    {{ trans("subscriber.enter_mobile_numbers_below") }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-b-50 p-l-xl">
                        <h5 class="title-add-subscriber">{{ trans("subscriber.enter_each_mobile_number") }}</h5>
                        <h5 class="title-add-subscriber">{{ trans("subscriber.you_may_import_additional") }}</h5>
                        <h5 class="title-add-subscriber">
                            {{ trans("subscriber.example") }}
                        </h5>
                        <form class="form-add" id="subscriberForm" actions="{{ $list_id }}/copy-paste" method="post" accept-charset="UTF-8">
                            {{ csrf_field() }}
                            <div class="col-12 m-t-xl">
                                <div class="form-group justify-content-center">
                                    <textarea
                                        class="form-control"
                                        name="content"
                                        rows="10"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans('validationForm.content.required') }}"
                                    ></textarea>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-xs col-sm-3 col-md-2 col-form-label">
                                        {{ trans('subscriber.file_terminated_by') }}:
                                    </label>
                                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                                        <div class="row justify-content-start">
                                            <select
                                                name="file_terminated"
                                                data-rule-required="true"
                                                class="custom-select form-control"
                                                data-msg-required="{{ trans('validationForm.file_terminated.required') }}"
                                            >
                                                <option value="">{{ trans("subscriber.select_format") }}</option>
                                                <option value="," selected>Comma(,)</option>
                                                <option value=";">Semicolon(;)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-xs col-sm-3 col-md-2 col-form-label">
                                        {{ trans('subscriber.filed_enclosed_by') }}:
                                    </label>
                                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                                        <div class="row justify-content-start">
                                            <select
                                                name="file_enclosed"
                                                class="custom-select form-control"
                                                class="custom-select form-control"
                                            >
                                                <option value="" selected>{{ trans("subscriber.not_enclosed") }}</option>
                                                <option value="'">'</option>
                                                <option value='"'>"</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <a href={{ url("admin/subscribers/" . $list_id . "/add") }} class="btn btn-secondary">{{ trans("subscriber.back") }}</a>
                                <button type="submit" class="btn btn-example">{{ trans("subscriber.next") }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-none d-md-inline-block col-md-3">
                    <div class="content-sidebar">
                        <p><strong>Data Entry</strong></p>
                        <span class="d-block mt-3">
                            Copy and paste your CSV data to the field on the left. Be sure that your data is in CSV (comma separated value) format. Then select your CSV properties such as the character used to separate fields and enclose them.
                        </span>
                        <span class="d-block mt-1">
                            TextsDaily prepares your CSV data for import process and you will be asked to map fields of the CSV data with your subscriber list fields in the next step.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
