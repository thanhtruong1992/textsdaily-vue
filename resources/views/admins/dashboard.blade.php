@extends("layouts.admin")

@section("title", "DashBoard")
@section('custom-script')
<script src="{{ asset('js/dashboardChart.js') }}"></script>
@endsection
@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans("dashboard.title") }}</h1>
        </div>
        <div class="main-content">
            <div class="col">
                <div class="row">
                    <div class="col-sm-12 col-md-9">
                        <div class="col pt-3">
                            <form id="formFilterCampaign">
                                <div class="row justify-content-start">
                                    <div class="col-xs-12 col-sm-4 col-md-2" id="select-filter">
                                        <select class="select-filter" name="state">
                                          <option value="month" selected>{{ trans("dashboard.month") }}</option>
                                          <option value="day">{{ trans("dashboard.day") }}</option>
                                          <option value="hour">{{ trans("dashboard.hour") }}</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-2 p-b-md invisible" id="select-date">
                                        <div class="justify-content-start">
                                            <select class="select-date" name="state">
                                                @for($i = 1; $i <= $totalDay; $i++)
                                                    <option value="{{ $i }}" {{ $i == $day ? 'selected' : '' }} >{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-2 p-b-md invisible" id="select-month">
                                        <div class="row justify-content-start">
                                            <select class="select-month" name="state">
                                              @foreach($dataMonth as $key => $item)
                                                <option value="{{ $key+1 }}" {{ $key+1 == $month ? 'selected' : '' }} >{{ $item }}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-2 p-b-md">
                                        <div class="justify-content-start">
                                            <select class="select-year" name="state">
                                                @for($i = $year; $i >= 2016; $i--)
                                                    <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 p-b-md">
                                        <div class="justify-content-start">
                                            <select class="select-timezone" name="state">
                                                @foreach($timezone as $key => $name)
                                                    <option value="{{$key}}" {{ $user->time_zone == $key  ? 'selected' : '' }} >{{$name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="chartDashBoard"></div>
                        <div class="total-usage d-flex flex-row ml-4 mb-5">
                            <p class="text-bold">{{ trans('dashboard.total_usage') }}:</p>
                            <ul class="d-flex flex-column ml-3 border border-main-color p-3" id="show-usage">
                                <li class="">0</li>
                            </ul>
                            <span class="date-show pl-3"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="row justify-content-center mt-3"><strong>{{ trans('dashboard.quick_access') }}</strong></div>
                    @if(Auth::user()->isGroup3())
                        <div class="row justify-content-center">
                            <a class="btn-new-list" href="{{ url('admin/subscriber-lists/add') }}">
                                {{ trans("dashboard.create_new_list") }}
                            </a>
                        </div>
                        <div class="row justify-content-center">
                            <a class="btn-new-list" href="{{ url('admin/campaign/create') }}">
                                {{ trans("dashboard.create_new_campaign") }}
                            </a>
                        </div>
                    @endif
                    @if(Auth::user()->isGroup2())
                        <div class="row justify-content-center">
                            <a class="btn-new-list" href="{{ route('clients2.create') }}">
                                {{ trans("client.new") }}
                            </a>
                        </div>
                        <div class="row justify-content-center">
                            <a class="btn-new-list" href="{{ route('clients2.createReader') }}">
                                {{ trans("client.new_reader") }}
                            </a>
                        </div>
                    @endif
                    @if(Auth::user()->isGroup1())
                        <div class="row justify-content-center">
                            <a class="btn-new-list" href="{{ route('clients.create') }}">
                                {{ trans("client.new") }}
                            </a>
                        </div>
                    @endif
                    @if(!(Auth::user()->isGroup4()) && $user->billing_type != "UNLIMITED")
                        <div class="row justify-content-center  ">
                            <div class="w-200 m-t-lg text-center">
                                    <strong class="title-dashboard w-100">
                                        {{ trans("dashboard.balance") }}
                                    </strong>
                                    <hr class="my-2">
                                    <h4 class="font-15rem">
                                        {{ number_format($user->credits - $user->credits_usage, 2) ." ". $user->currency }}
                                    </h4>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
