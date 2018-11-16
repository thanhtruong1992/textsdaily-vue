<div class="modal fade" id="addUrlModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addLinkForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('campaign.insert_url') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row justify-content-center">
                        <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans("campaign.url") }}</label>
                        <div class="col-xs col-sm-8 col-md-9">
                            <input
                                type="text"
                                name="linkShort"
                                class="form-control input-custom"
                                placeholder="{{ trans("campaign.url") }}"
                                data-rule-required="true"
                                data-msg-required="{{ trans("validationForm.link.required")}}"
                                data-rule-url="true"
                                data-msg-url="{{ trans("validationForm.link.url")}}"
                            />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("campaign.cancelBtn") }}</button>
                    <button type="button" class="btn btn-example" id="addShortLink">{{ trans("campaign.insert") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>