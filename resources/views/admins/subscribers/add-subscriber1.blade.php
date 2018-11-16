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
                    <a class="btn btn-example" href="">
                        {{ trans("subscriber.return_to_list") }}
                    </a>
                </div>
            </div>

            <h5 class="row justify-content-center title-add">{{ trans("subscriber.add_a_subscriber_to_list") }}</h5>
            <form class="form-add" id="subscriberForm" actions="/add" method="post">
                {{ csrf_field() }}
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.mobileNumber') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="mobile"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.mobileNumber') }}"
                                    data-msg-required="{{ trans("validationForm.mobile_number.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.title') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="title"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.title') }}"
                                    data-msg-required="{{ trans("validationForm.title.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.firstName') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="first_name"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.firstName') }}"
                                    data-msg-required="{{ trans("validationForm.first_name.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.lastName') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="last_name"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.lastName') }}"
                                    data-msg-required="{{ trans("validationForm.last_name.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.additionalField1') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="additional_field_1"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.additionalField1') }}"
                                    data-msg-required="{{ trans("validationForm.additional_field_1.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.additionalField2') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="additional_field_2"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.additionalField2') }}"
                                    data-msg-required="{{ trans("validationForm.additional_field_2.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('subscriber.addForm.additionalField3') }}</label>
                    <div class="col-xs col-sm-6 col-md-5 col-lg-4 col-xl-3">
                        <div class="row justify-content-start">
                            <input
                                    type="text"
                                    name="additional_field_3"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.addForm.additionalField3') }}"
                                    data-msg-required="{{ trans("validationForm.additional_field_3.required") }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center">
                    <button type="submit" class="btn btn-example">{{ trans("subscriber.add") }}</button>
                    <button type="button" class="btn btn-secondary">{{ trans("subscriber.clear") }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
