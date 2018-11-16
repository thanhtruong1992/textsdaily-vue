@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
<script type="text/javascript">
$(document).ready(function () {
    $("#newSubscriberListForm").on('submit', function (event) {
          var el = $('#btnSubmitSubscriberList');
          el.prop('disabled', true);
          setTimeout(function(){el.prop('disabled', false); }, 3000);
    });
});
</script>
@endsection
@section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('subscriber.addForm.add') }}</h1>
    </div>
    <div class="main-content">
        <div class="row">
            <div class="d-none d-md-inline-block col-md-1"></div>
            <div class="col-md-7">
                <form class="form-add" id="newSubscriberListForm"
                    actions="{{ route('subscriberList.create') }}"
                    method="POST">
                    {{ csrf_field() }}
                    <div class="form-group row justify-content-center">
                        <label class="col-xs col-sm-3 col-form-label">{{
                            trans('subscriber.list_name') }}</label>
                        <div class="col-xs col-sm-8">
                            <div class="row justify-content-start">
                                <input type="text" name="name"
                                    data-rule-required="true"
                                    class="form-control input-custom"
                                    placeholder="{{ trans('subscriber.list_name') }}"
                                    data-msg-required="{{ trans("validationForm.subscriber_list_name.required") }}"
                                    />
                            </div>
                        </div>
                    </div>
                    <div
                        class="form-group row justify-content-center m-t-xl">
                        <button type="submit" id="btnSubmitSubscriberList" class="btn btn-example">{{
                            trans("subscriber.create_list") }}</button>
                        <a class="btn btn-secondary" href="{{ url("admin/subscriber-lists") }}">{{
                            trans("subscriber.cancel") }}</a>
                    </div>
                </form>
            </div>
            <div class="d-none d-md-inline-block col-md-3">
                <div class="content-sidebar">
                    <p>
                        <strong>Add Subscribers To Your List</strong>
                    </p>
                    <span class="d-block mt-3">
                        Select one of the import methods on the left to add subscribers to your list. You can import by copying and pasting or uploading from your computer
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
