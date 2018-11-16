$(document).ready(function() {
	var allowTimes = [];
    for(i = 0; i <= 23; i++) {
        var hour = i.toString();
        hour = hour.length > 1 ? hour : '0' + hour;
        for(j = 0; j < 60; j+=5) {
            var minute = j.toString();
            minute = minute.length > 1 ? minute : '0' + minute;
            allowTimes.push(hour + ':' + minute);
        }
    }

    $("#inputStartDate").datetimepicker({
    	maxDate: new Date(),
        dayOfWeekStart: 1,
        timepicker: true,
        allowTimes: allowTimes
    }).on('change', function(e) {
    	  $('#inputEndDate').datetimepicker({minDate: new Date(e.target.value)});
    });
    $("#inputEndDate").datetimepicker({
        maxDate: new Date(),
        dayOfWeekStart: 1,
        timepicker: true,
        allowTimes: allowTimes
    }).on('change', function(e) {
    	  $('#inputStartDate').datetimepicker({maxDate: new Date(e.target.value)});
    });
    
    $("#filterCamapign").on("click", function() {
    	reload();
    });
    
    var arrHidden = role_user == "GROUP2" ? [7] : [];
    const pre = 10;
	var table = $('#tableListCampaign').DataTable( {
		bInfo: false,
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        lengthChange: false,
        bDestroy: true,
        paging: true,
        iDisplayLength: 10, //  set max length pagination
        ajax: {
        	url: api_get_data,
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var page = parseInt(d.start / pre) + 1;
    			var data = $("#formFilterCampaign").serialize();
				data = data == "" ? "page=" +  page : data + "&page=" + page;
    			return data;
    		},
			error: function() {
				return {
					data: []
				}
			}
        },
        columns: [
        	{ data: "", mRender: function() {
				return "";
			}},
			{ data: "date_send", mRender: function(data, type, row) {
				return $.fn.formatDateTime(data);
			}},
	        { data: "campaign_name"},
	        { data: "client_name"},
	        { data: "client_type"},
	        { data: "total", mRender: function(data, type, row) {
	        	var temp = parseFloat(data);
	        	if(!!isNaN(temp)) {
	        		return data;
	        	}
	        	return temp.toFixed(2);
	        }},
	        { data: "currency"},
	        { data: "", mRender: function(data, type, row) {
	        	return '<a href="' + url_detail + '/' + row.user_id + "/" + row.campaign_id + '">Detail</a>';
	        }}
        ],
        columnDefs : [
			// hidden column
			{
				visible : false,
				targets : arrHidden
			}, 
		],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
        	$("td:first", nRow).html(iDisplayIndex +1);
        	return nRow;
    	},
    });
	
	// reload data table
	function reload() {
		table.ajax.reload( null, true );
	}
	
	$("#exportCSVCampaign").on("click", function() {
		var data = $("#formFilterCampaign").serialize();
		window.open(url_export_csv + "?" + data, "_blank");
	});
});