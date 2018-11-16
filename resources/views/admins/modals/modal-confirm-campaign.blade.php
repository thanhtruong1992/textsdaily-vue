<div class="modal fade" id="modalConfirmCampaign" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ trans("campaign.confirm_campaign") }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" id="contentConfirm">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-example" id="acceptConfirm">{{ trans("campaign.continue") }}</button>
        <button type="button" class="btn btn-secondary" id="cancelConfirm">{{ trans("campaign.cancel") }}</button>
      </div>
    </div>
  </div>
</div>