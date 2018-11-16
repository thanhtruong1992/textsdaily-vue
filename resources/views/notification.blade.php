<div id="alert" class="d-none">
    @if ($message = Session::get('success'))
        <div id="alert-success" class="success">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div id="alert-danger" class="error">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div id="alert-warning" class="warning">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('info'))
        <div id="alert-info" class="info">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('danger'))
        <div id="alert-danger" class="error">
            {{ $message }}
        </div>
    @endif
    @if ($errors->any())
        <div id="alert-danger" class="error">
            Please check the form below for errors
        </div>
    @endif
</div>