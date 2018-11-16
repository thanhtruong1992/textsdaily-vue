@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
<script type="text/javascript">
window.link = {};
window.link.no_item_delete = "{{ trans('client.delete_no_items') }}";
window.link.no_item_enable = "{{ trans('client.enable_no_items') }}";
window.link.no_item_disable = "{{ trans('client.disable_no_items') }}";
@if(Auth::user()->isGroup1())
window.link.api_delete_client = "{{ route('clients.delete') }}";
window.link.api_update_status_client = "{{ route('clients.updateStatusClient') }}";
@else
window.link.api_delete_client = "{{ route('clients2.delete') }}";
window.link.api_update_status_client = "{{ route('clients2.updateStatusClient') }}";
@endif
</script>
<script src="{{ asset('js/clientDataList.js') }}"></script>
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('client.client_list') }}</h1>
    </div>
    <div class="main-content">
        <div class="header-button">
            <div class="col">
                <div class="row justify-content-start">
                    <a id="deleteClient" class="client-action mr-1 ml-2" href="#"><u>{{ trans("client.delete") }}</u></a>
                    <a id="enableClient" class="client-action mr-1 ml-2" href="#"><u>{{ trans("client.enable") }}</u></a>
                    <a id="disableClient" class="client-action mr-1 ml-2" href="#"><u>{{ trans("client.disable") }}</u></a>
                    <label class="custom-control custom-checkbox ml-2">
                        <input type="checkbox" class="custom-control-input frmClientCheck" value="checkAll" id="frmClientCheckAll"/>
                        <span class="custom-control-indicator"></span>
                    </label>
                </div>
            </div>
            <div class="col">
                <div class="row justify-content-end">
                @if(Auth::user()->isGroup1())
                    <a class="btn btn-example" href="{{ route('clients.create') }}">{{ trans("client.new") }}</a>
                @else
                    <a class="btn btn-example" href="{{ route('clients2.createApiAccount') }}">{{ trans("client.new_api_account") }}</a>
                    <a class="btn btn-example" href="{{ route('clients2.create') }}">{{ trans("client.new") }}</a>
                    <a class="btn btn-example" href="{{ route('clients2.createReader') }}">{{ trans("client.new_reader") }}</a>
                @endif
                </div>
            </div>
        </div>
        <div class="content-data">
            <div class="col">
                 @foreach($clients as $client)
                    <div class="row client-row">
                        <div class="col-1 billing-col">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input frmClientCheck" value="{{$client->id}}"/>
                                <span class="custom-control-indicator"></span>
                            </label>
                        </div>
                        <div class="col-2 billing-col">
                            <div class="inline-block padding-block-md">
                                <img class="img-fluid rounded img-client" alt=""
                                src="{{ $client->avatar ? '/'. config('constants.path_file_logo') . md5($client->id) . '/' . $client->avatar : 'http://rwamittu.com/wp-content/themes/white/assets/images/placeholder.jpg' }}">
                            </div>

                        </div>
                        <div class="col">
                            <div class="row">
                                <label class="custom-control m-t-sm h5"><b>{{ $client->name }}</b></label>
                            </div>
                            <div class="row">
                                <div class="col-1 m-t-sm">
                                    <i class="custom-control fa fa-envelope" aria-hidden="true"></i>
                                </div>
                                <div class="col-sm-6 m-t-sm">
                                    <label class="">{{ $client->email }} @if($client->status == "DISABLED")<span class="badge badge-secondary">{{ $client->status }}</span>@endif</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1 m-t-xs">
                                    <i class="custom-control fa fa-map-marker" aria-hidden="true"></i>
                                </div>
                                <div class="col-sm-6 m-t-xs">
                                    <label class="">{{ $client->country }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1 m-t-xs">
                                    <i class="custom-control fa fa-globe" aria-hidden="true"></i>
                                </div>
                                <div class="col-sm-6 m-t-xs">
                                    <label class="">{{ $client->time_zone }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 m-t-xs">
                                @if(Auth::user()->isGroup1())
                                    <a class="custom-control m-t-xs" href="{{ route('clients.info', ['id'=> $client->id ])}}"><u>{{ trans("client.edit_client") }}</u></a>
                                @else
                                    @if($client->type == "GROUP4")
                                    <a class="custom-control m-t-xs" href="{{ route('clients2.info-reader', ['id'=> $client->id ])}}"><u>{{ trans("client.edit_client") }}</u></a>
                                    @else
                                    <a class="custom-control m-t-xs" href="{{ route('clients2.info', ['id'=> $client->id ])}}"><u>{{ trans("client.edit_client") }}</u></a>
                                    @endif
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                 @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
