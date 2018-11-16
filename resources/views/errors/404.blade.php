@extends("errors.error")

@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="mt-5">
            <img class="logo-error" src={{ asset("images/textdaily.png") }} />
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h2 class="display-2">404</h2>
                <div class="error-details display-4">
                    {{ trans('error.404') }}
                </div>
                <div class="error-actions">
                    <a href="{{ url('/') }}" class="btn btn-example">
                        <span class="glyphicon glyphicon-home"></span>
                        {{ trans('error.action.take_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection