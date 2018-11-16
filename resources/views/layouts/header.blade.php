@inject('authService', 'App\Services\Auth\IAuthenticationService')

<header class="header">
    <div class="top">
        <div class="logo col">
            <img class="logo-dashboard" src={{ !empty(Auth::user()->url_avatar) ? Auth::user()->url_avatar : asset("images/textdaily.png") }} />
        </div>
        <div class="col">
            <ul class="menu-header">
                <li>
                    <a href="#">Hey, {{ Auth::user()->name }}!</a>
                </li>
                <?php
                    $childCurrentUser = $authService->getAllUserChildrenByParent(Auth::user()->id);
                    $value = session('other_role');
                ?>
                @if(count($childCurrentUser) > 0)
                <li>
                    <a class="font-bold" href="#">{{ trans("header.select_client") }}</a>
                    <div class="sub-menu border pb-3 bt-3">
                        <ul>
                        @foreach ($authService->getAllUserChildrenByParent(Auth::user()->id) as $child)
                            <li>
                                <p class="title">{{ $child['name'] }}</p>
                                <p class="action-menu">
                                    @if ($child['status'] == "DISABLED")
                                    <span class="badge badge-secondary">DISABLED</span>
                                    @endif
                                    <a class="edit" href="{{ Auth::user()->isGroup1() ? route('clients.info', ['id' => $child['id']]) : ($child['type'] == 'GROUP4' ? route('clients2.info-reader', ['id' => $child['id']]) : route('clients2.info', ['id' => $child['id']])) }}">{{ trans("header.edit") }}</a>
                                    <a class="account" href="{{ route('loginOther.other', ['id' => $child['id']]) }}">{{ trans("header.client_dashboard") }}</a>
                                </p>
                            </li>
                        @endforeach
                        </ul>
                    </div>
                </li>
                @endif

                @if(count($value) > 0)
                <li>
                    <a href="{{ route('loginOther.return') }}">{{ trans("header.return_original_user") . end($value) }}</a>
                </li>
                @endif
                @if(Auth::user()->isGroup1())
                    <li>
                        <a href="{{ route('setting.index') }}">{{ trans("header.menu.setting") }}</a>
                    </li>
                    <li>
                        <a href="{{ url('agency/logout') }}">{{ trans("header.logout") }}</a>
                    </li>
                    <li>
                        <a href="{{ url('help/') }}" target="_blank">{{ trans("header.help") }}</a>
                    </li>
                @elseif (Auth::user()->isGroup2())
                    <li>
                        <a href="{{ route('setting2.index') }}">{{ trans("header.menu.setting") }}</a>
                    </li>
                    <li>
                        <a href="{{ url('client/logout') }}">{{ trans("header.logout") }}</a>
                    </li>
                    <li>
                        <a href="{{ url('help1/') }}" target="_blank">{{ trans("header.help") }}</a>
                    </li>
                @elseif (Auth::user()->isGroup3())
                    <li>
                        <a href="{{ url('admin/logout') }}">{{ trans("header.logout") }}</a>
                    </li>
                    <li>
                        <a href="{{ url('help2/') }}" target="_blank">{{ trans("header.help") }}</a>
                    </li>
                @elseif (Auth::user()->isGroup4())
                    <li>
                        <a href="{{ url('reader/logout') }}">{{ trans("header.logout") }}</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="bottom">
        <ul class="menu">
        	<?php
				$currentRoute = Route::currentRouteName();
				$settingActiveClass = '';
				$templateActiveClass = '';
				$transactionActiveClass = '';
				$inboundMessagesActiveClass = '';
				//
				switch ( $currentRoute ) {
				    case 'setting2.account':
					case 'setting.account':
					case 'settings.mnc-mcc':
					case 'settings.mnc-mcc.edit':
					case 'settings.report':
					case 'setting.whitelabel':
					case 'setting2.whitelabel':
					case 'serviceProviderIndex':
					case 'inboundConfig':
					case 'editInboundConfig':
					case 'settingMobilePattern':
					case 'editMobilePattern':
						$settingActiveClass = 'active';
						break;
					case 'transaction-histories.client.index':
					case 'transaction-histories-2.client.index':
					case 'transaction-histories.campaigns.index':
					case 'transaction-histories-2.campaigns.index':
					    $transactionActiveClass = 'active';
					    break;
					case 'templates.index':
					case 'templates.create':
					case 'templates.info':
					    $templateActiveClass = 'active';
					    break;
				    case 'inboundMessages':
				        $inboundMessagesActiveClass = 'active';
				        break;
					default:
						break;
				}
			?>
            @if(Auth::user()->isGroup3())
                @if(Auth::user()->isApi())
                    <li class="dashboard">
                        <a href="{{ url('admin/dashboard') }}">{{ trans("header.menu.dashboard") }}</a>
                    </li>
                    <li class="report">
                        <a href="{{ url('admin/reports/campaigns') }}">{{ trans("header.menu.report") }}</a>
                    </li>
                    <li class="token">
                        <a href="{{ url('admin/tokens') }}">{{ trans("header.menu.token") }}</a>
                    </li>
                @elseif(!Auth::user()->isApi())
                    <li class="dashboard">
                        <a href="{{ url('admin/dashboard') }}">{{ trans("header.menu.dashboard") }}</a>
                    </li>
                    <li class="subscribers">
                        <a href="{{ url('admin/subscriber-lists') }}">{{ trans("header.menu.subscriber") }}</a>
                    </li>
                    <li class="campaigns">
                        <a href="{{ route('campaign.index') }}">{{ trans("header.menu.campaign") }}</a>
                    </li>
                    <li class="report">
                        <a href="{{ url('admin/reports/campaigns') }}">{{ trans("header.menu.report") }}</a>
                    </li>
                    <li class="inboundMessages pl-4 pr-4 {{ $inboundMessagesActiveClass }}">
                        <a href="{{ route('inboundMessages') }}">{{ trans("header.menu.inboundMessages") }}</a>
                    </li>
                    <li class="template right-menu {{ $templateActiveClass }}">
                        <a href="{{ route('templates.index') }}">{{ trans("header.menu.template") }}</a>
                    </li>
                @endif
                
            @elseif(Auth::user()->isGroup4())
                <li class="dashboard">
                    <a href="{{ url('reader/dashboard') }}">{{ trans("header.menu.dashboard") }}</a>
                </li>
                <li class="subscribers">
                    <a href="{{ url('reader/subscriber-lists') }}">{{ trans("header.menu.subscriber") }}</a>
                </li>
                <li class="campaigns">
                    <a href="{{ url('reader/campaigns') }}">{{ trans("header.menu.campaign") }}</a>
                </li>
                <li class="report">
                    <a href="{{ route('report-campaign-4.index') }}">{{ trans("header.menu.report") }}</a>
                </li>
            @elseif(Auth::user()->isGroup2())
                <li class="dashboard">
                    <a href="{{ url('client/dashboard') }}">{{ trans("header.menu.dashboard") }}</a>
                </li>
                <li class="client">
                    <a href="{{ route('clients2.index')}}">{{ trans("header.menu.my_client") }}</a>
                </li>
                <li class="transaction header-transaction {{ $transactionActiveClass }}">
                    <a href="{{ route("transaction-histories-2.client.index") }}">{{ trans("header.menu.transaction_history") }}</a>
                </li>
                <li class="report">
                    <a href="{{ route('report-campaign-2.index') }}">{{ trans("header.menu.report") }}</a>
                </li>
            @else
                <li class="dashboard">
                    <a href="{{ url('agency/dashboard') }}">{{ trans("header.menu.dashboard") }}</a>
                </li>
                <li class="client">
                    <a href="{{ route('clients.index')}}">{{ trans("header.menu.my_client") }}</a>
                </li>
                <li class="transaction header-transaction {{ $transactionActiveClass }}">
                    <a href="{{ route("transaction-histories.client.index") }}">{{ trans("header.menu.transaction_history") }}</a>
                </li>
                <li class="report">
                    <a href="{{ route('report-campaign-1.index') }}">{{ trans("header.menu.report") }}</a>
                </li>
            @endif
        </ul>
    </div>
</header>