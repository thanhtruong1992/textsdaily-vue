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
    
	var pre = 10;
	var arrField = [
		"created_at",
		"to",
		"from",
		"message",
	];
	
	var tableInboundMessages = $('#table-inbound-messages').DataTable( {
		bInfo: false,
        processing: true,
        serverSide: true,
        searching: false, // hidden input search
        ordering: true, // show sort
        lengthChange: false,
        bDestroy: true,
        paging: true,
        /* data: dataSet, */
        ajax: {
        	headers : {
				'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
			},
        	url: window.settings.InboundMessages.getInboundMessagesUrl,
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var obj = {
    				field: arrField[order.column],
    				orderBy: order.dir,
    				page: (d.start / 10) + 1,
    				from: $("#inputStartDate").val(),
    				to: $("#inputEndDate").val(),
    				timezone: $("#timezone").val(),
    				subscriber_number: $("#subscriber_number").val(),
    				hosted_number: $("#hosted_number").val()
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
            { data: "created_at", mRender: function(data, type, row) {
            	return $.fn.formatDateTime(data);
            }},
            { data: "to"},
            { data: "from"},
            { data: "message"}
        ],
        aaSorting: [[ 0, "desc" ]], // default sort
        columnDefs: [
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [3]
        	},
        ]
    });
	
	$("#request").on("click", function(e) {
		reload();
	});
	
	$("#export").on("click", function(e) {
		var from = $("#inputStartDate").val();
		var to = $("#inputEndDate").val();
		var timezone = $("#timezone").val();
		var subscriber_number = $("#subscriber_number").val();
		var hosted_number = $("#hosted_number").val();		
		window.open(window.settings.InboundMessages.exportInboundMessage + "?from=" + from + "&to=" + to + "&timezone" + timezone + "&subscriber_number=" + subscriber_number + "&hosted_number=" + hosted_number, '_blank');
	});
	
	// search
	$("#input-search").on('keyup', function (e) {
	    if (e.keyCode == 13) {
	    	search(this.value);
	    }
	}).on('blur', function() {
		search(this.value);
	});
	
	// reload data table
	function reload() {
		tableInboundMessages.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		tableInboundMessages.search(value).draw();
	}
} );