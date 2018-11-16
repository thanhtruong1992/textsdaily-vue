$(document).ready(function() {
	$("#formReportCenter").validate();
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
    
    $("#reportCenter").on("click", function() {
    	var valid = $("#formReportCenter").valid();
    	if(!valid) {
    		$("#formReportCenter").submit();
    	}else {
    		var data = $("#formReportCenter").serialize();
        	$.ajax({
        		type: "GET",
        		url: url_report_center,
        		data: data,
        		success: function(res) {
        			reload();
        			$.fn.showNotification("success", {content: res.message});
        		},
        		error: function(err) {
        			var error = err.responseJSON;
        			$.fn.showNotification("error", {content: error.message});
        		}
        	});
    	}
    });

    
    var arrField = [
		"",
		"from",
		"to",
		"timezone",
		"created_at",
		"updated_at",
	];
    const pre = 10;
	var table = $('#tableReportCenter').DataTable( {
		bInfo: false,
        processing: true,
        serverSide: true,
        searching: false,
        ordering: true,
        lengthChange: false,
        bDestroy: true,
        paging: true,
        ajax: {
        	url: url_get_data,
    		type: "GET",
    		draw: 1,
    		data: function(d) {  	
    			
    			var order = d.order[0];
    			var obj = {
    				orderBy: order.dir,
    				field: arrField[order.column],
    				page: (d.start / pre) + 1
    			};
    			return obj;
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
			{ data: "from", mRender: function(data, type, row) {
				return $.fn.formatDateTime(data);
			}},
	        { data: "to", mRender: function(data, type, row) {
	        	return $.fn.formatDateTime(data);
	        }},
	        { data: "time_zone"},
	        { data: "created_at", mRender: function(data, type, row) {
	        	return $.fn.formatDateTime(data);
	        }},
	        { data: "updated_at", mRender: function(data, type, row) {
	        	return $.fn.formatDateTime(data);
	        }},
	        { data: "status", mRender: function(data, type, row) {
	        	if(data == "PROCESSED") {
	        		return '<a href="' + row.url_download + '">Download</a>';
	        	}	
	        	return "Wait";
	        }}
        ],
        aaSorting: [[ 4, "desc" ]], // default sort
        columnDefs: [
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0, 1, 2, 3, 6]
        	},
        	{
    			className: "text-truncate", 
    			"targets": [ 1, 2 ] 
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
});