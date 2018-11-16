@extends("layouts.admin") @section("title", "DashBoard")
@section("custom-script")
<script src="{{ asset('js/templateListDataTable.js') }}"></script>
<script type="text/javascript">
    window.link = {}
    window.link.group = "{{ Auth::user()->type }}"
    window.link.api_get_list_template = "{{ route('templates.get_template') }}"
    	window.link.updateTemplateLink = "{{ route('templates.info', ['id' =>'__id']) }}"
    	window.link.deleteTemplateLink = "{{ route('templates.delete') }}"
</script>
@endsection @section("content")
<div class="main">
    <div class="main-title">
        <h1>{{ trans('template.template_list') }}</h1>
    </div>
    <div class="main-content">
        <div class="header-button">
            <div class="col">
                <div class="justify-content-start">
                    <div class="custom-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text" class="input-search" id="input-search"
                            placeholder="{{ trans("campaign.search") }}" />
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row justify-content-end">
                @if(Auth::user()->isGroup3())
                    <a class="btn btn-example" href="{{ route('templates.create') }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> {{ trans("template.new") }}
                    </a>
                @endif
                </div>
            </div>
        </div>
        <div class="content-data">
            <table id="table-campaign" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans("template.template_name_label") }}</th>
                        <th>{{ trans("template.created_date") }}</th>
                        <th>{{ trans("template.update_date") }}</th>
                        <th class="action-campaign">{{ trans("template.action") }}</th>
                    </tr>
                </thead>
                </div>
                </div>
                </div>
@endsection