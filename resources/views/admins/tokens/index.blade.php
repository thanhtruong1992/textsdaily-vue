@extends("layouts.admin")

@section("title", "Token")
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans("token.title") }}</h1>
        </div>
        <div class="main-content">
            <div class="header-button">
                <div class="col">
                    <div class="row justify-content-start">
                
                    </div>
                </div>
                <div class="col">
                    <div class="row justify-content-end">
                        <a class="btn btn-example" href="{{ route('tokens.create') }}">Create Token</a>
                    </div>
                </div>
            </div>
            <div class="content-data">
                <div class="col">
                    <b>Token: </b> 
                    @if( $token  )
                        {{ $token }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
