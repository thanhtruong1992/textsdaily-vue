@extends("layouts.admin")

@section("title", "DashBoard")

@section("content")
    <div class="main">
        <div class="main-title">
            <h1>{{ trans("subscriber.subscriber_list") }}</h1>
        </div>
        <div class="main-content">
            <div class="header-button">
                <div class="col">
                    <div class="row justify-content-start">
                        <div class="custom-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="text" id="input-search" class="input-search" placeholder="{{ trans("campaign.search") }}" />
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row justify-content-end">
                        <div class=olum"dropdown">
                            <button class="btn btn-example dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ trans("subscriber.subscriber_action") }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item">
                                    <input type="checkbox" class="form-controll show-column" value="position" />position</label>
                                </a>
                                <a class="dropdown-item">
                                    <input type="checkbox" class="form-controll show-column" value="name" />name</label>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-data">
                <table id="example" class="display table table-striped" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Extn.</th>
                            <th>Start date</th>
                            <th>Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
