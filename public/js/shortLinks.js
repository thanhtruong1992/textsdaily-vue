
$(document).ready(function() {
	var arr = window.location.pathname.split("/");
	var data = [];
	var tableShortLink = $('#tableShortLink').DataTable( {
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
        	url: window.api_get_short_links,
			type: "GET",
			cache: true,
			headers: {
				"Authorization": "Bearer " + window.dct_token,
				"Accept": "application/json",
				"Content-Type": "application/json",
			},
    		draw: 1,
    		data: function(d) {
				var order = d.order[0];
    			var obj = {
					user_id: userID,
					campaign_id: window.campaign_id,
					campaign_link_id: window.campaign_link_id,
    				orderBy: order.dir,
    				limit: 10
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
    		{ data: "", mRender: function(data, type, row) {
				return "";
			}},
            { data: "long_url", class: "short-link", mRender: function (data, type, row) {
				return row.long_url;
			}},
            { data: "total_clicks", class: "total-click", mRender: function (data, type, row) {
				return row.total_clicks;
			}}
        ],
        columnDefs: [
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0,2]
        	},
        	// hidden search column
        	{ 
	        	searchable: false,
	    		targets: [0,1,2]
        	},
        	// hidden column
        	{ 
        		visible: false,
        		targets: []
        	}
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
				$("td:first", nRow).html(iDisplayIndex +1);	
				return nRow;
		},
	});
	
	var options = {
        "backdrop" : "static",
        "show":true
	};

	function convertToCSV(objArray) {
		var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
		var str = '';
	
		for (var i = 0; i < array.length; i++) {
			var line = '';
			for (var index in array[i]) {
				if (line != '') line += ','
	
				line += array[i][index];
			}
	
			str += line + '\r\n';
		}
	
		return str;
	}
	
	function exportCSVFile(headers, items, fileTitle) {
		if (headers) {
			items.unshift(headers);
		}
	
		// Convert Object to JSON
		var jsonObject = JSON.stringify(items);
	
		var csv = convertToCSV(jsonObject);
	
		var exportedFilenmae = fileTitle + '.csv' || 'export.csv';
	
		var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		if (navigator.msSaveBlob) { // IE 10+
			navigator.msSaveBlob(blob, exportedFilenmae);
		} else {
			var link = document.createElement("a");
			if (link.download !== undefined) { // feature detection
				// Browsers that support HTML5 download attribute
				var url = URL.createObjectURL(blob);
				link.setAttribute("href", url);
				link.setAttribute("download", exportedFilenmae);
				link.style.visibility = 'hidden';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			}
		}
	}
	
	var headers = {
		long_url: "URL",
		total_clicks: "Total Clicks",
		email: "Email",
		created_at: "Created Date"
	};

	var getUrlParameter = function getUrlParameter(longUrl,sParam) {
        var sPageURL = longUrl,
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
    };
	
	$('#exportCSV').click(function() {
		$.ajax({
			url: window.api_export_csv,
			type: "GET",
			async: false,
			cache: true,
			headers: {
				"Authorization": "Bearer " + window.dct_token,
				"Accept": "application/json",
				"Access-Control-Allow-Origin": "*",
				"Content-Type": "application/json"
			},
    		data: {
				user_id: userID,
				campaign_id: window.campaign_id,
				campaign_link_id: window.campaign_link_id,
				time_zone: window.time_zone_user
			},
			success: function(res) {
				window.open(window.dtc_download_csv + "/" + res.data, '_blank');
			}
		});
		
	});

	
});
