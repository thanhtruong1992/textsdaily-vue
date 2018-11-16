<div class="modal fade" id="selectGroupModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="get" onsubmit="insertGroup(event)" />
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelTitleId">
                        {{ trans('campaign.add_list') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans("modal.name") }}</th>
                            <th class="check-all">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frmCheck" value="checkAll" id="frmCheckAll"/>
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mark</td>
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frmCheck" value="Mark" name="Mark"/>
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Jacob</td>
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frmCheck" value="Jacob" name="Jacob"/>
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Larry</td>
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input frmCheck" value="Larry" name="Larry"/>
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Insert</button>
                </div>
            </div>
        </form>
    </div>
</div>
