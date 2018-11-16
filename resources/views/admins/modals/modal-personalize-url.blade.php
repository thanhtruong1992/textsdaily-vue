<div class="modal fade" id="personalizeUrlModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addPersonalizeForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('campaign.personalize_url') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="personalizeUrlForm">
                        <div class="">
                                <div class="">
                                    <input
                                        type="text"
                                        name="personalizeLink"
                                        class="form-control input-custom personalize-url"
                                        placeholder="{{ trans("campaign.url") }}"
                                        data-rule-required="true"
                                        data-msg-required="{{ trans("validationForm.link.required")}}"
                                        data-rule-url="true"
                                        data-msg-url="{{ trans("validationForm.link.url")}}"
                                    />
                                    <span>
                                        Example: {{ env('APP_URL') }}
                                    </span>
                                </div>
                        </div>
                        <div class="form-group">
                            <textarea name="personalizeLinkShort" id="contentLinkShort" class="form-control" rown="3" readonly></textarea>
                            <span>
                                {{ trans('campaign.content_personalize_url') }}
                            </span>
                        </div>
                        <table id="table-personalize-url" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans("campaign.field_name") }}</th>
                                    <th>{{ trans("campaign.param") }}</th>
                                    <th class="check-all">
                                        
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("campaign.cancelBtn") }}</button>
                    <button type="button" class="btn btn-example" id="addPersonalizeShortLink">{{ trans("campaign.insert") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>