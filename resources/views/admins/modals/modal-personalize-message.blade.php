<div class="modal fade" id="personalizeModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addPersonalizeForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('campaign.personalize') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="table-personalize" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ trans("campaign.field_name") }}</th>
                            <th class="check-all">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frmModalCheck" value="checkAll" id="frmModalCheckAll"/>
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("campaign.cancelBtn") }}</button>
                    <button type="button" class="btn btn-example" id="insertPersonalize">{{ trans("campaign.insert") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>