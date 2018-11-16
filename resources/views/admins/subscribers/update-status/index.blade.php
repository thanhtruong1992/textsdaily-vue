@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.update_status') }}</h1>
        </div>
        <div class="main-content">
            <div>
                <div class="row">
                    <div class="col-7">
                        <h5 class="title-add-subscriber">{{ trans("subscriber.choose_the_way") }}</h5>
                        <div class="col-12 p-b-50">
                            <div class="add-subscriber m-t-xl">
                                <a href="{{ url("admin/subscribers/$list_id/update/upload-csv") }}">
                                    {{ trans("subscriber.file_upload") }}
                                </a>
                                <p class="m-t-xs">
                                    {{ trans("subscriber.upload_a_csv") }}
                                </p>
                            </div>
                            <div class="add-subscriber m-t-md">
                                <a href="{{ url("admin/subscribers/$list_id/update/copy-paste") }}">
                                    {{ trans("subscriber.manually") }}
                                </a>
                                <p class="m-t-xs">
                                    {{ trans("subscriber.enter_mobile") }}
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
            </div>
            <div class="row justify-content-center p-b-lg">
                <a class="btn btn-example" href={{ url("admin/subscriber-lists/" . $list_id ) }}>
                    {{ trans("subscriber.i_am_done") }}
                </a>
            </div>
        </div>
    </div>
@endsection
