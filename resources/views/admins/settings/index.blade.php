@extends("layouts.admin")
@section("content")
<div class="main">
	<div class="main-title">
		<h1>@yield('settingTitle')</h1>
	</div>
	<div class="main-client-header">
		<div class="row">
			<div class="container-fluid">
				<?php
				$currentRoute = Route::currentRouteName();
				$settingsAccountActiveClass = $settingsMailServerActiveClass = '';
				$serviceProviderActiveClass = $settingsSMSActiveClass = '';
				$settingsWhilelableActiveClass = $settingsReportSettingsActiveClass = '';
				$settingMCCMNCActiveClass = $settingMobilePatternActiveClass = '';
				//
				switch ( $currentRoute ) {
                    case 'serviceProviderIndex':
						$serviceProviderActiveClass = 'active';
						break;
                    case 'setting.account':
                    case 'setting2.account':
                        $settingsAccountActiveClass = 'active';
				        break;
                    case 'setting.whitelabel':
                    case 'setting2.whitelabel':
                        $settingsWhilelableActiveClass = 'active';
                        break;
                    case 'settings.mnc-mcc':
                    case 'settings.mnc-mcc.edit':
                        $settingMCCMNCActiveClass = 'active';
                        break;
                    case 'settings.report':
                        $settingsReportSettingsActiveClass = 'active';
                        break;
                    case 'settingMobilePattern':
                    case 'editMobilePattern':
                        $settingMobilePatternActiveClass = 'active';
                        break;
                    case 'inboundConfig':
                    case 'editInboundConfig':
                    case 'inboundConfigClient':
                    case 'editInboundConfigClient':
                        $settingsSMSActiveClass = 'active';
                        break;
					default:
						break;
				}
				?>
				<ul class="tabs">
                @if($client->isGroup1())
					<li class="{{ $settingsAccountActiveClass }}">
						<a href="{{ route('setting.account') }}">{{ trans("settings.settingsAccountTitle") }}</a>
					</li>
					<li class="{{ $settingsMailServerActiveClass }}">
						<a href="#">{{ trans("settings.settingsMailServerTitle") }}</a>
					</li>
					<li class="{{ $serviceProviderActiveClass }}">
						<a href="{{ route('serviceProviderIndex') }}">{{ trans("settings.settingsServiceProviderTitle") }}</a>
					</li>
					<li class="{{ $settingsSMSActiveClass }}">
						<a href="{{ route('inboundConfig') }}">{{ trans("settings.settingsInboundSMS") }}</a>
					</li>
					<!-- <li class="{{ $settingsWhilelableActiveClass }}">
						<a href="{{ route('setting.whitelabel') }}">{{ trans("settings.settingsWhilelableTitle") }}</a>
					</li> -->
					<li class="{{ $settingsReportSettingsActiveClass }}">
						<a href="{{ route('settings.report') }}">{{ trans("settings.settingsReportSettingsTitle") }}</a>
					</li>
					<li class="{{ $settingMCCMNCActiveClass }}">
						<a href="{{ route('settings.mnc-mcc') }}">{{ trans("settings.mcc_mnc") }}</a>
					</li>
                    <li class="{{ $settingMobilePatternActiveClass }}">
                        <a href="{{ route('settingMobilePattern') }}">{{ trans("settings.MobilePattern") }}</a>
                    </li>
                @else
					<li class="{{ $settingsAccountActiveClass }}">
						<a href="{{ route('setting2.account') }}">{{ trans("settings.settingsAccountTitle") }}</a>
					</li>
					<li class="{{ $settingsSMSActiveClass }}">
						<a href="{{ route('inboundConfigClient') }}">{{ trans("settings.settingsInboundSMS") }}</a>
					</li>
					<li class="{{ $settingsWhilelableActiveClass }}">
						<a href="{{ route('setting2.whitelabel') }}">{{ trans("settings.settingsWhilelableTitle") }}</a>
					</li>
                @endif
				</ul>
			</div>
		</div>
		<div class="client-content-data">@yield("settings-content")</div>
	</div>
</div>
@endsection
