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
    
    $('#queryClientTransaction').on('click', function() {
    		reload(tableTransactions);
    });
	
    var arrField = [
    		"",
		"created_at",
		"description",
		"type",
		"name",
		"billing_type",
		"credits",
		"currency",
	];
	var tableTransactions = $('#table-transaction-histories-client').DataTable( {
		bInfo: false,
        processing: true,
        serverSide: true,
        searching: false, // hidden input search
        ordering: true, // show sort
        lengthChange: false,
        bDestroy: true,
        paging: true,
        ajax: {
        	url: window.link.api_get_client_transaction,
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var obj = {
    				field: arrField[order.column],
        			orderBy: order.dir,
    				from: $("#inputStartDate").val(),
    				to: $("#inputEndDate").val(),
    				timezone: $("select[name='timezone']").val(),
    				page: (d.start / 10) + 1
    			};
    			return obj;
    		},
			error: function() {
				return {
					recordsTotal: 0,
					recordsFiltered: 0,
					data: []
				}
			}
        },
        columns: [
			{ data: "", mRender: function() {
	        		return "";
	        }},
			{ data: "created_at"},
	        { data: "description"},
	        { data: "type"},
	        { data: "name"},
	        { data: "billing_type"},
	        { data: "credits", mRender: function(data, type, row) {
	        	return parseFloat(data).toFixed(2);
	        }},
	        { data: "currency"}
        ], 
        aaSorting: [[ 1, "desc" ]], // default sort
        columnDefs: [
        	{ "name": "browser",  "targets": 1 },
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0, 2, 3, 4, 5, 6, 7]
        	},
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
        	$("td:first", nRow).html(iDisplayIndex +1);
        		return nRow;
        },
    });
	
	// reload data table
	function reload(table) {
		table.ajax.reload( null, true );
	}
	
	$("#exportTransactionClient").on("click", function() {
		var data = $("#formFilterClient").serialize();
		window.open(window.link.api_export_client_transaction + "?" + data, "_blank");
	});
});