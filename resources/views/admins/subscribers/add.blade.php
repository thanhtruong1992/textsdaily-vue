@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans('subscriber.addForm.add') }}</h1>
        </div>
        <div class="main-content">
            <div>
                <div class="row">
                    <div class="col-7">
                        <h5 class="title-add-subscriber">{{ trans("subscriber.choose_the_way") }}</h5>
                        <div class="col-12 p-b-50">
                            <div class="add-subscriber m-t-xl">
                                <a href="{{ url("admin/subscribers/$list_id/upload-csv") }}">
                                    {{ trans("subscriber.file_upload") }}
                                </a>
                                <p class="m-t-xs">
                                    {{ trans("subscriber.upload_a_csv") }}
                                </p>
                            </div>
                            <div class="add-subscriber m-t-md">
                                <a href="{{ url("admin/subscribers/$list_id/copy-paste") }}">
                                    {{ trans("subscriber.copy_and_paste") }}
                                </a>
                                <p class="m-t-xs">
                                    {{ trans("subscriber.enter_mobile") }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="d-none d-md-inline-block col-md-3">
                        <div class="content-sidebar">
                            <p><strong>Add Subscriber</strong></p>
                            <span class="d-block mt-3">
                                What's Subscriber List?
                            </span>
                            <span class="d-block mt-1">
                                Briefly, subscriber list is the container of your recipient details, custom fields, etc.
                            </span>
                            <span>
                                You can create more than one subscriber list to store different group of recipient details.
                            </span>
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
