<div class="modal fade" id="modalIncreaseCreditLimit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="increaseCreditForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModalAddCredit">{{ trans('client.increase_monthly') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentModalIncreaseCredit">
                    <div class="form-group row justify-content-center">
                        <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans("client.amount") }}</label>
                        <div class="col-xs col-sm-3 col-md-5">
                            <input
                                type="number"
                                step=".01"
                                name="increaseNumberLimit"
                                min="0"
                                @if($max_limit) max="{{ $max_limit }}" @endif
                                value="0.00"
                                class="form-control input-custom"
                                data-rule-required="true"
                                data-msg-required="{{ trans("validationForm.credit_amount.required")}}"
                            />
                            <label id="increaseNumberLimit-error" class="error" for="increaseNumberLimit">{{ $errors->first('increaseNumberLimit') }}</label>
                        </div>
                        <label class="col-xs col-sm-3 col-md-2 col-form-label">@if($client) {{ $client->currency }} @else SGD @endif</label>
                    </div>
                    <div class="form-group row justify-content-center">
                        <label class="col-xs col-sm-3 col-md-2 col-form-label">{{ trans('client.description') }}</label>
                        <div class="col-xs-2 col-sm-6 col-md-7">
                            <div class="justify-content-start">
                                <input type="text" name="increaseNumberLimitDescription"
                                    class="form-control input-custom"
                                    data-rule-required="true"
                                    data-msg-required="{{ trans("validationForm.description.required")}}"
                                    value="{{ old('increaseNumberLimitDescription') }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnModalIncreaseClose">{{ trans("client.cancel") }}</button>
                    <button type="button" class="btn btn-example"
                        id="btnModalIncreaseSave">{{ trans("client.save") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>