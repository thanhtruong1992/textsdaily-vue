<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addTemplateForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('campaign.template') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow: auto;">
                    <table id="table-template" class="table table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-truncate border-top-0">{{ trans("campaign.template_name") }}</th>
                            <th class="border-top-0">{{ trans("campaign.message") }}</th>
                            <th class="border-top-0"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("campaign.cancelBtn") }}</button>
                    <button type="button" class="btn btn-example" id="useTemplate">{{ trans("campaign.use") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>