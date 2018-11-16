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
            <div class="col-12">
                <div class="row">
                    <div class="col-md-8">
                        <form action="import-csv" id="importForm" method="POST">
                            {{ csrf_field() }}
                            <input name="list_id" type="hidden" value={{ $list_id }} />
                            <div class="p-b-50">
                                <h5 class="title-add-subscriber font-bold">{{ trans("subscriber.field_mapping_and_import_setting") }}</h5>
                                <div class="col-xs-12 p-b-50">
                                    @foreach($dataFile as $key => $data)
                                    <div class="add-subscriber m-t-xl">
                                        <div class="row justify-content-between">
                                            <label class="col-xs col-sm-4 col-md-4 col-form-label">{{ $data }}</label>
                                            <div class="col-xs col-sm-5 col-md-5 col-lg-5 col-xl-4">
                                                <div class="row justify-content-start">
                                                    <select name="field_{{ $key+1 }}" data-rule-required="true" class="custom-select form-control select-field" id="select-{{ $key }}">
                                                        <option value="">{{ trans("subscriber.ignore_this_field") }}</option>
                                                        <option value="phone">Mobile</option>
                                                        <option value="first_name">First Name</option>
                                                        <option value="last_name">Last Name</option>
                                                        @foreach($customFields as $item)
                                                            <option value="custom_field_{{ $item->id }}">{{ $item->field_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs col-sm-3 col-md-2">
                                                    <button type="button" class="btn btn-example" data-toggle="modal" data-target="#addCustomFieldModal" onclick="openAddCustomFieldModal(event, {{ $key }})">
                                                        {{ trans("subscriber.new_column") }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="m-t-lg">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="update_subscriber" checked="checked">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                                {{ trans("subscriber.update_subscriber_information") }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <a href={{ redirect()->back()->getTargetUrl() }} class="btn btn-secondary">{{ trans("subscriber.back") }}</a>
                                    <button type="button" class="btn btn-example" id="submitMapping">{{ trans("subscriber.next") }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="d-none d-md-inline-block col-md-3">
                        <div class="content-sidebar">
                            <p><strong>Field Mapping</strong></p>
                            <span class="d-block mt-3">
                                In the previous step, you have provided the import data. Now, please map your data fields with your subscriber list fields on the left.
                            </span>
                            <span class="d-block mt-1">
                                If you don't wish to import a specific field in your import data, simply select "Ignore this field" option from the list corresponding to that field.
                            </span>
                            <span class="d-block mt-1">
                                Important: You need to map at least the Mobile field.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include("admins.modals.modal-add-customfield")
