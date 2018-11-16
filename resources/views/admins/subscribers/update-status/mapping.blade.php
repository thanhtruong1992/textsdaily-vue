@extends("layouts.admin")

@section("title", "DashBoard")
@section("custom-script")
<script>
jQuery(function($){

    $("#submitMapping").click(function() {
        var flagValidate = true;

        $(".add-subscriber").each(function(key, value) {
            var i = key+1;
            var value = $("select[name='field_" + i + "']").val();
            if(value == 'phone'){
                flagValidate = false;
                return;
            }
        });

        if(!!flagValidate) {
            $("#importForm").validate();
        }else {
            $("#importForm").validate().destroy();
        }

        $("#importForm").trigger('submit');
    });
});
</script>
@endsection
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
            <form action="import-csv" id="importForm" method="POST">
                {{ csrf_field() }}
                <input name="list_id" type="hidden" value={{ $list_id }} />
                <div class="p-b-50">
                    <h5 class="title-add-subscriber font-bold">{{ trans("subscriber.field_mapping_and_import_setting") }}</h5>
                    <div class="col-sm-12 col-md-10 col-lg-9 col-xl-8 p-b-50">
                        @foreach($dataFile as $key => $data)
                        <div class="add-subscriber m-t-xl">
                            <div class="row justify-content-between">
                                <label class="col-xs col-sm-4 col-md-4 col-form-label">{{ $data }}</label>
                                <div class="col-xs col-sm-5 col-md-5 col-lg-5 col-xl-4">
                                    <div class="row justify-content-start">
                                        <select name="field_{{ $key+1 }}" data-rule-required="true" class="custom-select form-control select-field" id="select-{{ $key }}">
                                            <option value="">{{ trans("subscriber.ignore_this_field") }}</option>
                                            <option value="phone">Phone</option>
                                            <option value="first_name">First Name</option>
                                            <option value="last_name">Last Name</option>
                                            @foreach($customFields as $item)
                                                <option value="custom_field_{{ $item->id }}">{{ $item->field_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs col-sm-3 col-md-2">
                                    <button type="button" class="btn btn-example" data-toggle="modal" data-target="#addCustomFieldModal" onclick="openAddCustomFieldModal(event, {{ $key }})">
                                        {{ trans("subscriber.new_column") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row m-t-lg">
                            <label class="col-xs-12 col-sm-5 col-md-4 col-lg-3 col-form-label font-bold">
                                {{ trans('subscriber.change_status_to') }}:
                            </label>
                            <div class="form-group col-xs-12 col-sm-6 col-md-5 col-lg-4">
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
                    <div class="row justify-content-center">
                        <a href={{ redirect()->back()->getTargetUrl() }} class="btn btn-secondary">{{ trans("subscriber.back") }}</a>
                        <button type="button" class="btn btn-example" id="submitMapping">{{ trans("subscriber.next") }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@include("admins.modals.modal-add-customfield")
