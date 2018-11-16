@extends("admins.clients.client") @section("title", "DashBoard")
@section('custom-script')
<script src="{{ asset('js/addClient.js') }}" ></script>
<script type="text/javascript">
window.link = {};
window.link.no_client_was_created_message = "{{ trans('validationForm.client_id.required') }}";
@if(Auth::user()->isGroup1())
window.link.api_add_credit = "{{ route('clients.addCredit') }}";
window.link.api_withdraw_credit = "{{ route('clients.withdrawCredit') }}";
window.link.api_increase_credit_limit = "{{ route('clients.increaseCredit') }}";
window.link.api_descrease_credit_limit = "{{ route('clients.descreaseCredit') }}";
@else
window.link.api_add_credit = "{{ route('clients2.addCredit') }}";
window.link.api_withdraw_credit = "{{ route('clients2.withdrawCredit') }}";
window.link.api_increase_credit_limit = "{{ route('clients2.increaseCredit') }}";
window.link.api_descrease_credit_limit = "{{ route('clients2.descreaseCredit') }}";
@endif
window.link.client_id = @if($client) "{{ $client->id }}" @else "" @endif;
</script>
@endsection @section("client-content")
<div class="content">
    <div class="col">
        <div class="row billing-content">
            <div class="col-3"></div>
            @if($client->billing_type === "ONE_TIME")
                <div class="col">
                    <div class="row client-billing-row d-flex justify-content-center">
                        <label class="col-12 custom-control d-flex justify-content-center"><b>{{ trans('client.current_balance') }}</b></label>
                        <label class="col-12 custom-control d-flex justify-content-center">{{ $client ? "" . number_format($client->getBalance(), 2) . " " . $client->currency : "0.00 SGD" }}</label>
                    </div>
                    <div class="row m-t-xl">
                        <div class="col billing-col justify-content-center">
                            <button type="button"
                                id="add_credit"
                                class="btn btn-example ml-0">{{ trans("client.add_credit") }}</button>
                            <button type="button"
                                id="withdraw_credit"
                                class="btn btn-example ml-0">{{ trans("client.withdraw_credit") }}</button>
                        </div>
                    </div>
                </div>
            @elseif ($client->billing_type === "MONTHLY")
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <div class="row client-billing-row d-flex justify-content-center mr-1">
                                <label class="col-12 custom-control d-flex justify-content-center"><b>{{ trans('client.current_mmonthly') }}</b></label>
                                <label class="col-12 custom-control d-flex justify-content-center">{{ $client ? "" . number_format($client->getCreditsLimit(), 2) . " " . $client->currency : "0.00 SGD" }}</label>
                            </div>
                            <div class="row m-t-xl d-flex justify-content-center mr-1">
                                <button type="button"
                                    id="increase_credit_limit"
                                    class="btn btn-example ml-0">{{ trans("client.increase_monthly") }}</button>
                                <button type="button"
                                    id="descrease_credit_limit"
                                    class="btn btn-example ml-0 mt-2">{{ trans("client.decrease_monthly") }}</button>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row client-billing-row d-flex justify-content-center ml-1">
                                <label class="col-12 custom-control d-flex justify-content-center"><b>{{ trans('client.current_balance') }}</b></label>
                                <label class="col-12 custom-control d-flex justify-content-center">{{ $client ? "" . number_format($client->getBalance(), 2) . " " . $client->currency : "0.00 SGD" }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($client->billing_type === "UNLIMITED")
                <div class="col">
                    <div class="row d-flex justify-content-center">
                        <label class="col-12 custom-control d-flex justify-content-center">{{ trans('client.default_label_unlimited') }}</label>
                    </div>
                </div>
            @endif
            <div class="col-3"></div>
        </div>

        <div class="row">
            <div class="col"></div>
            <div class="col d-flex justify-content-center">
                <a class="btn btn-secondary ml-0" href="{{ route('clients.index') }}"> {{ trans("client.cancel") }} </a>
            </div>
            <div class="col"></div>
        </div>
    </div>
</div>
@endsection

@include("admins.modals.modal-billing-add")
@include("admins.modals.modal-billing-withdraw")
@include("admins.modals.modal-increase-limit")
@include("admins.modals.modal-descrease-limit")
