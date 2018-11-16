<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TextsDaily - @yield("title")</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/chartPDF.css') }}" />
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/googleChart.js') }}"></script>
    <script type="text/javascript">

        var data = <?php echo json_encode($data); ?>;
        showChart(data.valueStatus, data.valueTotal, data.valueDelivered);
        function showChart(valueStatus, valueTotal, valueDelivered) {
        	google.charts.load('current', {packages:['corechart']});
        	google.charts.setOnLoadCallback(function() {
        		chartStatus(valueStatus, 'chartStatus');
        		chartTotal(valueTotal, 'chartExpenses');
        		chartDelivered(valueDelivered);
        	});
        }

        function chartStatus(value, id) {
            var data = google.visualization.arrayToDataTable(value);
            var options = {
              title: '',
              titleTextStyle: {
            	  fontSize: 14,
            	  fontName: 'Arial',
              },
              chartArea: {
            	  width: '100%', // width of chart
            	  height: '100%', // height of chart
            	  left: '10%', // padding left chart
            	  top: '5%', // padding top chart
            	  bottom: '5%',
        	  }
            };

            var chartStatus = new google.visualization.PieChart(document.getElementById(id));
            chartStatus.draw(data, options);
        }

        function chartTotal(value, id) {
            var data = google.visualization.arrayToDataTable(value);
            var options = {
              title: '',
              titleTextStyle: {
            	  fontSize: 14,
            	  fontName: 'Arial',
              },
              chartArea: {
            	  width: '100%', // width of chart
            	  height: '100%', // height of chart
            	  left: '10%', // padding left chart
            	  top: '5%', // padding top chart
            	  bottom: '5%',
        	  }
            };

            var chartStatus = new google.visualization.PieChart(document.getElementById(id));
            chartStatus.draw(data, options);
        }

        function chartDelivered(value) {
        	var countData = value.length;
        	var data = google.visualization.arrayToDataTable(value);

          var view = new google.visualization.DataView(data);
          view.setColumns([0, 1, 2, {
        	  calc: "stringify",
              sourceColumn: 3,
              type: "string",
              role: "annotation" }
          ]);

          var optionsDelivered = {
            title: "",
            bar: {groupWidth: countData * 12 + "%"}, // width of col chart
            legend: { position: "none" },
            color: "red",
            titleTextStyle: {
            	fontSize: 14,
            	fontName: 'Arial',
            },
            chartArea: {
            	left: '10%',
            	top: '5%',
            	right: '10%',
            	bottom: '20%'
            },
            vAxis: {
            	minValue: 0,
                maxValue: 100,
                format: "decimal",
            	viewWindow: {
                    min: 0,
                    max: 100
            	}
            }
          };
          var chartDelivered = new google.visualization.ColumnChart(document.getElementById("chartDelivered"));
          chartDelivered.draw(view, optionsDelivered);
        }
    </script>
    <style>
    </style>
</head>
<body>
    <div class="template-pdf">
        <div class="info-campaign">
            <p>
                <span class="title">
                    {{ trans("report.campaign_name") }}:
                </span>
                <span>
                    {{ $data['campaign']->name }}
                </span>
            </p>
            <p>
                <span class="title">
                    {{ trans("report.subscriber_list") }}:
                </span>
                <span>
                    {{ $data["campaign"]->list_name }}
                </span>
            </p>
            <p>
                <span class="title">
                    {{ trans("report.time_stampt_of_job_sendt") }}:
                </span>
                <span>
                    {{ $data["campaign"]->send_time. " " . $data["campaign"]->send_timezone }}
                </span>
            </p>
        </div>
        <div class="charts" id="charts">
            <div class="chart">
                <h5 class="title">
                    {{ trans("report.total_message_by_status") }}
                </h5>
                <div class="chart-pie" id="chartStatus"></div>
            </div>
            <div class="chart">
                <h5 class="title">
                    {{ trans("report.total_expenses") }}
                </h5>
                <div class="chart-pie" id="chartExpenses"></div>
            </div>
            <div class="chart">
                <h5 class="title">
                    {{ trans("report.delivery_rate") }}
                </h5>
                <div class="chart-col" id="chartDelivered"></div>
            </div>
        </div>
        <div class="table-data">
            <table class="table table-striped table-bordered table-custom" cellspacing="0">
                <thead>
                <tr>
                    <th>{{ trans("subscriber.country") }}</th>
                    <th>{{ trans("subscriber.network") }}</th>
                    <th>{{ trans("subscriber.sent") }}</th>
                    <th>{{ trans("subscriber.delivered") }}</th>
                    <th>{{ trans("subscriber.pending") }}</th>
                    <th>{{ trans("subscriber.failed") }}</th>
                    <th>{{ trans("subscriber.expired") }}</th>
                    <th>{{ trans("subscriber.expenses") }} ({{ $data['currency'] }})</th>
                    <th>{{ trans("subscriber.delivery_rate") }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($data['dataTable'] as $item)
                    <tr>
                        <td>{{ ucwords(strtolower($item->country)) }}</td>
                        <td>{{ ucfirst(strtolower($item->network)) }}</td>
                        <td class="text-right">{{ $item->totals }}</td>
                        <td class="text-right">{{ $item->delivered }}</td>
                        <td class="text-right">{{ $item->pending }}</td>
                        <td class="text-right">{{ $item->failed }}</td>
                        <td class="text-right">{{ $item->expired }}</td>
                        <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                        <td class="text-right">{{ $item->delivery_rate }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>