<div class="modal fade bd-example-modal-lg" id="summaryCampaignModal" tabindex="-1" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('campaign.summary') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row justify-content-center">
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.campaign_name") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row justify-content-start">
                                        <p id="campaign_name"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.to") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="row justify-content-start">
                                        <p id="to"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.sender") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">
                                        <p id="sender"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.message") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="row justify-content-start">
                                        <p id="message_text"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.schedule") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">
                                        <p id="schedule"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.number_of_subscriber") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">

                                        <p id="number_of_subscriber"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.duplicated_number") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">
                                        <p id="duplicated_number"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.number_of_sms") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">
                                        <p id="number_of_sms"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                       <div class="col-12 m-b-xs">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row justify-content-end">
                                        <p class="p-r-xl">
                                            {{ trans("campaign.estimated_cost") }}:
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row justify-content-start">
                                        <p id="estimated_cost"></p>
                                    </div>
                                </div>
                            </div>
                       </div>
                </div>
                <div class="row justify-content-center">
                    <table id="tableCampaignSummary" class="table table-striped" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>{{ trans("subscriber.country") }}</th>
                            <th>{{ trans("subscriber.network") }}</th>
                            <th>{{ trans("subscriber.price_per_sms") }}</th>
                            <th>{{ trans("subscriber.total_recipients") }}</th>
                            <th>{{ trans("subscriber.minimum_sms_count") }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="row justify-content-center">
                    <button type="button" class="btn btn-example m-r-lg" id="createCampaign">{{ trans("campaign.i_am_done") }}</button>
                    <button type="button" class="btn btn-secondary" id="changeCampaign">{{ trans("campaign.change") }}</button>
                </div>
            </div>
        </div>
    </div>
</div>