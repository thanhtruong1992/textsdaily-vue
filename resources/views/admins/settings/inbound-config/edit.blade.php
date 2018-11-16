@extends("admins.settings.index")
@section("title", trans('settings.settingsInboundSMSTitle'))
@section('settingTitle', trans('settings.settingsInboundSMSTitle'))

@section('custom-script')
@endsection

@section("settings-content")
<div class="col">
            <div class="row">
                <div class="d-none d-md-inline-block col-md-1"></div>
                <div class="col-md-7">
                    <form class="form-add" id="inboundConfigForm" method="post" action="{{ route('updateInboundConfig', ['id' => $inboundConfig->id])  }}">
                        {{ csrf_field() }}
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.inboundNumber') }}</label>
                            <div class="col-xs col-sm-8 col-form-label">
                                {{ $inboundConfig->number }}
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.inboundExpiryDate') }}</label>
                            <div class="col-xs col-sm-8 col-form-label">
                                {{ $inboundConfig->expiry_date }}
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.inboundClient') }}<span class="text-danger">&nbsp;*</span></label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <select class="custom-select form-control" name="user_id">
                                        <option value="">{{ trans('settings.selectClient') }}</option>
                                        @foreach( $allUsers as $user )
                                        <?php
                                            $selected = '';
                                            if (( $client->type == 'GROUP1' && $user->id == $inboundConfig->group2_user_id )
                                                    || ( $client->type == 'GROUP2' && $user->id == $inboundConfig->group3_user_id )) {
                                                $selected = 'selected';
                                            }
                                        ?>
                                        <option value="{{ $user->id }}" {{ $selected }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <label id="user_id-error" class="error" for="user_id">{{ $errors->first('user_id') }}</label>
                                </div>
                            </div>
                        </div>
                        @if( $client->type == 'GROUP2' )
                        <div class="form-group row justify-content-center">
                            <label class="col-xs col-sm-4 col-form-label">{{ trans('settings.inboundKeyworks') }}</label>
                            <div class="col-xs col-sm-8">
                                <div class="justify-content-start">
                                    <input type="text" name="keyworks"
                                        class="form-control input-custom @if($errors->first('keyworks')) error @endif"
                                        value="{{ old('keyworks', $inboundConfig->keyworks) }}"/>
                                    <small id="keyworksHelp" class="form-text text-muted">{{ trans('settings.keyworksHelp') }}</small>
                                    <label id="keyworks-error" class="error" for="keyworks">{{ $errors->first('keyworks') }}</label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row justify-content-center p-t-xl">
                            <div class="col-xs col-sm-8 ml-md-auto">
                                <?php
                                if ($client->type == 'GROUP2'){
                                    $cancelUrl = route('inboundConfigClient');
                                } else {
                                    $cancelUrl = route('inboundConfig');
                                }
                                ?>
                                <a class="btn btn-secondary ml-0" href="{{ $cancelUrl }}">
                                    {{ trans("settings.Cancel") }}
                                </a>
                                <button type="submit" class="btn btn-example ml-0">
                                    {{ trans("settings.save") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="d-none d-md-inline-block col-md-3">
                    <div class="content-sidebar">
                        <p><strong>Assign inbound number</strong></p>
                        <br>
                        <span class="d-block m-t-md">Description here!!!</span>
                        <span class="d-block m-t-lg"></span>
                    </div>
                </div>
            </div>
        </div>
@endsection

