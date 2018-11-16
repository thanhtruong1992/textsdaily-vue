@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script") @endsection @section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('client.my_client') }}</h1>
    </div>
    <div class="main-client-header">
        <div class="row">
            <div class="container-fluid">
                <?php
                $currentRoute = Route::currentRouteName();
                //
                $clientInfoActiveClass = $clientInfoBillingActiveClass = $clientPriceConfigActiveClass= '';
                $client2InfoActiveClass = $client2InfoBillingActiveClass = $client2PriceConfigActiveClass = '';
                switch ( $currentRoute ) {
                    case 'clients.info':
                        $clientInfoActiveClass = 'active';
                        break;
                    case 'clients.info-billing':
                        $clientInfoBillingActiveClass = 'active';
                        break;
                    case 'clients.priceConfig':
                        $clientPriceConfigActiveClass = 'active';
                        break;
                    case 'clients2.info':
                        $client2InfoActiveClass = 'active';
                        break;
                    case 'clients2.info-billing':
                        $client2InfoBillingActiveClass = 'active';
                        break;
                    case 'clients2.priceConfig':
                        $client2PriceConfigActiveClass = 'active';
                        break;
                    default:
                        break;
                }
                ?>
                <?php if(!$client) $disabled = ' disabled'; else $disabled = ''; ?>
                <ul class="tabs">
                @if(Auth::user()->isGroup1())
                    <li class="account {{$clientInfoActiveClass}}"><a @if($client) href="{{ route('clients.info', ['id'=> $client->id]) }}" @else href="{{ route('clients.create') }}" @endif>{{ trans("client.menu.account") }}</a></li>
                    <li class="billing{{ $disabled }} {{ $clientInfoBillingActiveClass }}"><a href="{{ $client ? route('clients.info-billing', ['id'=> $client->id]) : 'javascript:void(0);' }}">{{
                            trans("client.menu.billing") }}</a></li>
                    <li class="price{{ $disabled }} {{ $clientPriceConfigActiveClass }}"><a href="{{ $client ? route('clients.priceConfig', ['id' => $client->id]) : 'javascript:void(0);' }}">
                        {{ trans("client.menu.price") }}</a>
                    </li>
                @else
                    <li class="account {{$client2InfoActiveClass}}"><a @if($client) href="{{ route('clients2.info', ['id'=> $client->id]) }}" @else href="{{ route('clients2.create') }}" @endif>{{ trans("client.menu.account") }}</a></li>
                    <li class="billing{{ $disabled }} {{ $client2InfoBillingActiveClass }}"><a href="{{ $client ? route('clients2.info-billing', ['id'=> $client->id]) : 'javascript:void(0);' }}">{{
                            trans("client.menu.billing") }}</a></li>
                    <li class="price{{ $disabled }} {{ $client2PriceConfigActiveClass }}"><a href="{{ $client ? route('clients2.priceConfig', ['id' => $client->id]) : 'javascript:void(0);' }}">
                        {{ trans("client.menu.price") }}</a>
                    </li>
                @endif
                </ul>
                @if(Auth::user()->isGroup1())
                <a class="float-right mr-0" href="{{ route('clients.index') }}">{{trans("client.back") }} </a>
                @else
                <a class="float-right mr-0" href="{{ route('clients2.index') }}">{{trans("client.back") }} </a>
                @endif
            </div>
        </div>
        <div class="client-content-data">@yield("client-content")</div>
    </div>
</div>
@endsection