<div class="modal fade" id="modalSMSReview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titleModalSMSReview">{{ trans('campaign.test_result') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contentModalSMSReview">
        <span id="messageSMSReview"></span>
        <div id="contentSMSSuccess" class="notify-resutl">
            <span>{{ trans('campaign.test_result_success') }}</span>
            <span class="item-phone-number-success"></span>
        </div>
        <div id="contentSMSPending" class="notify-resutl">
            <span id="messagePeddingSuccess">{{ trans('campaign.test_result_pending_success') }}</span>
            <span id="messagePeddingFail">{{ trans('campaign.test_result_pending_fail') }}</span>
            <div id="itemPendingGroup" class="col-xs">
                <span class="item-phone-number-pending"></span>
            </div>
        </div>
        <div id="contentSMSRejected" class="notify-resutl">
            <span>{{ trans('campaign.test_result_rejected') }}</span>
            <div id="itemRejectedGroup" class="col-xs">
                <span class="item-phone-number-rejected"></span>
            </div>
        </div>
        <div id="contentSMSExpired" class="notify-resutl">
            <span>{{ trans('campaign.test_result_expired') }}</span>
            <div id="itemExpiredGroup" class="col-xs">
                <span class="item-phone-number-expired"></span>
            </div>
        </div>
        <div id="contentSMSFailed" class="notify-resutl">
            <span>{{ trans('campaign.test_result_invalid') }}</span>
            <div id="itemFailedGroup" class="col-xs">
                <span class="item-phone-number-invalid"></span>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btnModalClose">{{ trans("campaign.close") }}</button>
      </div>
    </div>
  </div>
</div>