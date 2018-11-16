$(document).ready(function() {
	var arr = window.location.pathname.split("/");
	var dataChart = {data: '', chart: ''};
	var dataMap = {data: '', chart: ''};
	// chart column
	google.charts.load('current', {packages: ['corechart', 'bar']});
	google.charts.setOnLoadCallback(function() {
		 dataChart = drawBasic([], dataChart);
	});
	
	// chart map
	google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': goolge_key
	});
	google.charts.setOnLoadCallback(function() {
		dataMap = drawRegionsMap([], dataMap);
	});
	
	$.ajax({
		type: "GET",
		url: window.location.origin + "/admin/apis/reports/campaigns/" + userID + "/" + campaignID,
		success: function(res) {
			
			if(res.chartColunm.length > 0) {
				// chart column
				google.charts.setOnLoadCallback(function() {
					dataChart = drawBasic(res.chartColunm, dataChart);
				});
			}

			if(res.chartMap.length > 0) {
				// chart map		
			    google.charts.setOnLoadCallback(function() {
			    	dataMap =  drawRegionsMap(res.chartMap, dataMap);
			    });
			}

			$('#reportCamapignTable').DataTable( {
				bInfo: false,
		        processing: false,
		        serverSide: false,
		        searching: false,
		        ordering: false,
		        lengthChange: false,
		        bDestroy: false,
		        paging: false,
		        data: res.data,
		        columns: [
					{ data: "", mRender: function() {
						return "";
					}},
					{ data: "country"},
			        { data: "network", },
			        { data: "totals"},
			        { data: "delivered"},
			        { data: "pending"},
			        { data: "failed"},
			        { data: "expired"},
			        { data: "total_price"},
			        { data: "delivery_rate"},
				],
		        fnRowCallback: function(nRow, aData, iDisplayIndex){
		        	$("td:first", nRow).html(iDisplayIndex +1);
		        	return nRow;
	        	},
		    });
			
			$('#tableShortLink').DataTable( {
				bInfo: false,
		        processing: false,
		        serverSide: false,
		        searching: false,
		        ordering: false,
		        lengthChange: false,
		        bDestroy: false,
		        paging: false,
		        data: res.dataShortLink,
		        columns: [
					{ data: "", mRender: function() {
						return "";
					}},
					{ data: "url", class: "short-link", mRender: function(data, type, row) {
						return '<a href="' + document.URL + "/" + row.id + '">' + data +'</a>';
					}},
					{ data: "short_link", class: "total-click", mRender: function(data, type, row) {
						return row.total_clicks;
					}},
			        { data: "delivered", class: "total-click"}
				],
		        fnRowCallback: function(nRow, aData, iDisplayIndex){
		        	$("td:first", nRow).html(iDisplayIndex +1);
		        	return nRow;
	        	},
		    });
		},
		error: function() {
			
		}
	});
	
	var options = {
        "backdrop" : "static",
        "show":true
    };

    $('#export-csv').on('click', function () {
        $('#exportCampaignModal').modal(options);
    });
    
    function drawBasic(value, dataChart) {
    	value = Array.isArray(value) ? value : [];
		var countData = value.length;
		// config chart
		var options = {
		        title: '',
		        chartArea: {
		        	width: '100%', // width of chart
		        	height: '100%', // height of chart
		        	left: '15%', // padding left chart
		        	top: '5%', // padding top chart
		        	bottom: '10%',
		        	right: '5%',
		        },
		        bar: {groupWidth: countData * 12.5 + "%"},
		        legend: {position: 'none'},
		        animation: {duration: 500, easing: 'out',},
		        hAxis: {
		          title: '',
		          viewWindow: {
			  	        min: 0
		  	      },
		        },
		        vAxis: {
		          title: '',
		          textStyle : {
		        	  fontSize: 12 // or the number you want
		          }
		        }
	      };
		  var data = dataChart.data;
		  var chart = dataChart.chart;
		  var oldData = "";
		  var row = 0;
		  
		  if(value.length == 0) {dataMap
			  // first create chart
			  
			  // new data chart
			  data = new google.visualization.DataTable();
			  
			  // set header
	    	  data.addColumn('string', 'Country');
	    	  data.addColumn('number', 'Total Price');
	    	  data.addColumn({type:'string', role:'style'});
		      
	    	  // new chart
		      chart = new google.visualization.BarChart(document.getElementById('columnChart'));
		      // render chart
		      chart.draw(data, options);
		  }else{
			  oldData = data.getNumberOfRows();
		      row = oldData == 0 ? 0 : oldData.length;
		  }
	      
	      value.forEach(function(item) {
	    	  // add value chart
	    	  data.insertRows(row, [[ item.country, item.total, item.color ]]);
	    	  row += 1;
	      });
	      // render chart
	      chart.draw(data, options);
	      
	      return {data: data, chart: chart};
    }
	
	function drawRegionsMap(value, dataMap) {
		var data = dataMap.data;
		var chart = dataMap.chart;
		
		// config chart
		var options = {
        		colorAxis: {
        			colors: [
        				"#b9f79e",
        				"#95f26d",
        				"#5ebf35",
        				"#4f9b2e",
        				"#327017",
        			]
        		}
        		
        };
		
		if(value.length == 0) {
			data = new google.visualization.DataTable();
	        // set header 
	        data.addColumn('string', 'Country');
	  	  	data.addColumn('number', 'Total Price');

	        chart = new google.visualization.GeoChart(document.getElementById('mapChart'));
	        // render chart
	        chart.draw(data, options);
		}
        
        // set total value
        data.addRows(value.length);
        
        var row = 0;
        value.forEach(function(item) {
			if(item.country != 'Unknown') {
				// set value
				data.setValue(row, 0, item.country);
				data.setValue(row, 1, item.total);
				row += 1;
			}
        });
        // render chart
        chart.draw(data, options);
        
        return {data: data, chart: chart};
   }
});
