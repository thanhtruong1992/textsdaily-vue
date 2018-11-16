@extends("admins.settings.index")
@section('settingTitle', trans('settings.settingsAccountTitle'))
@section('custom-script')
<script src="{{ asset('js/settings-account.js') }}"></script>
<script type="text/javascript">
window.link = {}
</script>
@endsection
@section("settings-content")
<div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="whiteLabelForm"
                    @if(Auth::user()->isGroup1())
                    action="{{ route('setting.whitelabel-update') }}"
                    @else
                    action="{{ route('setting2.whitelabel-update') }}"
                    @endif
                    enctype="multipart/form-data"
                    method="post">
                        {{ csrf_field() }}
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.hostname') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="host_name"
                                        data-rule-url="true"
                                        data-msg-url="{{ trans("validationForm.link.url")}}"
                                        placeholder="{{ trans('settings.hostname') }}"
                                        class="form-control input-custom @if($errors->first('host_name')) error @endif"
                                        value="{{ old('host_name', $client->host_name) }}"/>
                                    <label id="host_name-error" class="error" for="host_name">{{ $errors->first('host_name') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.logo') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input
                                            type="file"
                                            name="avatar"
                                            accept=".jpeg,.png,.jpg,.gif,.svg"
                                            class="form-control"
                                            placeholder="Choose file"
                                            data-rule-filesize="25000000"
                                            data-msg-filesize="{{ trans('validationForm.file_image.filesize') }}"
                                        />
                                    <label id="avatar-error" class="error" for="avatar">{{ $errors->first('avatar') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label"></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <img class="img-fluid rounded" alt="" 
                                    src="{{ $client->avatar ? '/'. config('constants.path_file_logo') . md5($client->id) . '/' . $client->avatar : 'http://rwamittu.com/wp-content/themes/white/assets/images/placeholder.jpg' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <button id="addClient" type="submit" class="btn btn-example ml-0" disabled>
                                    {{ trans("client.save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none d-md-inline-block col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
                        <br>
                        <span class="d-block m-t-md">
                            Set your settings
                            for your new campaign on the left. You can
                            create a regular email campaign or a/b split
                            test campaign.
                        </span>
                        <span class="d-block m-t-lg">
                            You will define your
                            contents and scheduling for your email
                            campaign in next steps.
                        </span>
                    </div>
                </div>
            </div>
        </div>
@endsection

