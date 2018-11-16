<div class="modal fade" id="addCustomFieldModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <form onsubmit="addCustomField(event)" id="addCustomFieldForm">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('subscriber.add_custom_field') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input
                    type="hidden"
                    name="list_id"
                    value="{{ $list_id }}" />
                    <input
                    type="hidden"
                    name="key"
                    id="keySelect"
                    value="" />
                <div class="form-group row justify-content-center">
                    <label class="col-xs col-sm-4 col-md-3 col-form-label">{{ trans("subscriber.custom_field") }}</label>
                    <input
                    type="text"
                    name="field"
                    data-rule-required="true"
                    data-msg-message="{{ trans("validationForm.field.required") }}"
                    class="form-control col-xs col-sm-7 col-md-8 input-custom"
                    placeholder="{{ trans("subscriber.enter_custom_field") }}" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("subscriber.cancel") }}</button>
                <button type="submit" class="btn btn-example">{{ trans("subscriber.insert") }}</button>
            </div>
        </div>
    </div>
    </form>
</div>