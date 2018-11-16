<div class="modal fade" id="settingsUploadModel" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="settingsUploadForm" enctype="application/x-www-form-urlencoded" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">{{ trans('settings.uploadBtn') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row justify-content-center">
                        <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans("settings.File") }}<span class="text-danger">&nbsp;*</span></label>
                        <div class="col-xs col-sm-8 col-md-9">
                            <input type="file" name="fileUpload" class="form-control input-custom"
                                data-rule-required="true"
                                data-msg-required="{{ trans("validationForm.file_csv.required")}}"
                                accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/plain"
                            />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('settings.Cancel') }}</button>
                    <button type="button" class="btn btn-example" id="btnUpload">{{ trans("settings.save") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>