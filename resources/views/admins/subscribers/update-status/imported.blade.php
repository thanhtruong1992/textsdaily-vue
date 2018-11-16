@extends("layouts.admin")

@section("title", "DashBoard")
@section("custom-script")
<script>
jQuery(function($){
    if (window.history && window.history.pushState) {
        window.history.pushState('Gotootherpage', null, '');
        $(window).on('popstate', function () {
            var url = window.location.origin;
            var path = window.location.pathname;
            var arrPath = path.split("/");
            if(arrPath[3]) {
                 var url = url + "/admin/subscribers/" + arrPath[3] + "/update";
            }else {
                var url = url + "/admin/subscriber-lists";
            }
            window.location.href = url;
        });
    }
});
</script>
@endsection

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.update_result') }}</h1>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-8">
                    <div class="p-b-50 p-l-xl">
                        <h5 class="title-add-subscriber">{{ trans("subscriber.import_process") }}</h5>
                        <div class="col-sm-12 col-md-10 col-lg-9 col-xl-8 p-b-50 p-l-xl">
                            <p class="p-l-xl m-t-xl">
                                <span>
                                    {{ trans("subscriber.total_subscriber") }}
                                </span>
                                <span>
                                    {{ $data['TotalSubscribers'] }}
                                </span>
                            </p>
                            <p class="p-l-xl m-t-md">
                                <span>
                                    {{ trans("subscriber.skip_subscribers") }}
                                </span>
                                <span>
                                    {{ $data['TotalSkip'] }}
                                </span>
                                @if ($data['TotalSkip'] > 0)
                                    <a href="{{ url("admin/downloads/subscribers/skip/" . $data['fileSkip']) }}" target="_blank">
                                        Download
                                    </a>
                                @endif
                            </p>
                            <p class="p-l-xl m-t-md">
                                <span>
                                    {{ trans("subscriber.updated_subscribers") }}
                                </span>
                                <span>
                                    {{ $data['TotalUpdated'] }}
                                </span>
                            </p>
                            <p class="p-l-xl m-t-md">
                                <span>
                                    {{ trans("subscriber.duplicate_subscribers") }}
                                </span>
                                <span>
                                    {{ $data['TotalDuplicates'] }}
                                </span>
                                @if ($data['TotalDuplicates'] > 0)
                                    <a href="{{ url("admin/downloads/subscribers/duplicaste/" . $data['fileDuplicate']) }}" target="_blank">
                                        Download
                                    </a>
                                @endif
                            </p>
                            <p class="p-l-xl m-t-md">
                                <span>
                                    {{ trans("subscriber.invalid_subscribers") }}
                                </span>
                                <span>
                                    {{ $data['TotalInvalid'] }}
                                </span>
                                @if ($data['TotalInvalid'] > 0)
                                    <a href="{{ url("admin/downloads/subscribers/invalid/" . $data['fileInvalid']) }}" target="_blank">
                                        Download
                                    </a>
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
                <div class="d-none col-md-3">
                    <div class="content-sidebar">
                        <p><strong>New Email Campaign</strong></p>
                        <br>
                        <span class="d-block m-t-md"> Set your settings for
                            your new campaign on the left. You can create a
                            regular email campaign or a/b split test
                            campaign. </span> <span class="d-block m-t-lg">
                            You will define your contents and scheduling for
                            your email campaign in next steps. </span>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center p-b-lg">
                <a href={{ url("admin/subscribers/" . $list_id) }} class="btn btn-example">{{ trans("subscriber.i_am_done") }}</a>
                <a href={{ url("admin/subscriber-lists/" . $list_id) }} class="btn btn-example">{{ trans("subscriber.view_subscriber") }}</a>
            </div>
        </div>
    </div>
@endsection
