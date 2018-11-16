@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.addForm.add') }}</h1>
        </div>
        <div class="main-content">
            <div class="col">
                <div class="row justify-content-end p-t-md">
                    <a class="btn btn-example" href={{ url("admin/subscriber-lists/" . $list_id) }}>
                        {{ trans("subscriber.return_to_list") }}
                    </a>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-7">
                        <h5 class="row justify-content-center title-add">{{ trans("subscriber.upload_a_csv") }}</h5>
                        <form class="form-add" id="copyPasteForm" actions="{{ $list_id }}/upload-csv" method="post"  enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input
                                type="hidden"
                                name="list_id"
                                value="{{ $list_id }}"
                            />
                            <div class="form-group row justify-content-center">
                                <label class="col-12 col-lg-3 col-xs-2 col-form-label">{{ trans('subscriber.file') }}</label>
                                <div class="col-12 col-lg-7 col-xl-5">
                                    <div class="row justify-content-start">
                                        <input
                                            type="file"
                                            name="file"
                                            accept=".csv"
                                            class="form-control"
                                            placeholder="Choose file"
                                            data-rule-required="true"
                                            data-rule-filesize="40000000"
                                            data-msg-required="{{ trans('validationForm.file_csv.required') }}"
                                            data-msg-filesize="{{ trans('validationForm.file_csv.filesize') }}"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-12 col-lg-3 col-xs-2 col-form-label">{{ trans('subscriber.file_terminated_by') }}</label>
                                <div class="col-12 col-lg-7 col-xl-5">
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
                                <label class="col-12 col-lg-3 col-xs-2 col-form-label">{{ trans('subscriber.filed_enclosed_by') }}</label>
                                <div class="col-12 col-lg-7 col-xl-5">
                                    <div class="row justify-content-start">
                                        <select
                                            name="file_enclosed"
                                            class="custom-select form-control"
                                            class="custom-select form-control">
                                            <option value="" selected>{{ trans("subscriber.not_enclosed") }}</option>
                                            <option value="'">'</option>
                                            <option value='"'>"</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <div class="col-12 col-lg-10 col-xl-8">
                                    <div class="justify-content-start">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" name="check_header" class="custom-control-input">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                                {{ trans("subscriber.this_file_include_a_header") }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <a href={{ url("admin/subscribers/" . $list_id . "/add") }} class="btn btn-secondary">{{ trans("subscriber.back") }}</a>
                                <button type="submit" class="btn btn-example">{{ trans("subscriber.next") }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="d-none d-md-inline-block col-md-3">
                        <div class="content-sidebar">
                            <p><strong>CSV File Upload</strong></p>
                            <span class="d-block mt-3">
                                If your CSV data file is a big sized one, you can upload it from your computer by using this section.
                            </span>
                            <span class="d-block mt-1">
                                Please be sure that your file is in CSV format which means fields are separated with a comma, semicolon or tab character.
                            </span>
                            <span class="d-block mt-1">
                                In the next step, we will ask you to map your fields with subscriber fields.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
