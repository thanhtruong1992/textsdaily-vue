<div class="modal fade" id="exportCampaignModal" tabindex="-1" role="dialog" aria-hidden="true">
    <form id="fromExportCampaign">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('report.select_report') }}</h5>
                    <button type="button" id="exit-exportCSV" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <h4 class="title-export text-center">{{ trans("report.select_type_of_report_you_want") }}</h5>
                    <div class="col-12 export-checkbox">
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" name="detailed" class="custom-control-input" checked>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ trans("report.detailed_report") }}</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" name="pending" class="custom-control-input" checked>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ trans("report.pending") }}</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" name="delivered" class="custom-control-input" checked>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ trans("report.delivered") }}</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" name="failed" class="custom-control-input" checked>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ trans("report.failed") }}</span>
                        </label>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" name="expired" class="custom-control-input" checked>
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">{{ trans("report.expired") }}</span>
                        </label>
                    </div>
                    <label id="checkboxError" class="error text-center"></label>
                </div>
                <div class="modal-footer justify-content-center ">
                    <button type="button" class="btn btn-secondary" id="close-exportCSV" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-example" id="exportCSV">{{ trans("report.export_to_csv") }}</button>
                </div>
            </div>
        </div>
    </form>
</div>