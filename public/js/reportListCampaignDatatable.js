$(document).ready(function() {
	const pre = 10;
	var reportListCampaign = $('#tableListReportCampaign').DataTable( {
		bInfo: false,
        processing: true,
        serverSide: true,
        searching: false, // hidden input search
        ordering: false, // show sort
        lengthChange: false,
        bDestroy: false,
        paging: true,
        ajax: {
        	url: window.location.origin + "/admin/apis/reports/campaigns",
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var page = parseInt(d.start / pre) + 1;
    			var search = $("#input-search").val();
    			return {
    				page: page,
    				search: search
    			};
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
    		{ data: "name", mRender: function(data, type, row) {
    			return '<a href="' + url_detail + row.user_id + '/' + row.id + '">' + data + '</a>';
    		}},
    		{ data: "name_of_user"},
            { data: "send_time",  mRender: function(data, type, row) {
            	return $.fn.formatDateTime(data);
            }},
            { data: "totals"},
            { data: "delivered_rate"}
        ],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
        	$("td:first", nRow).html(iDisplayIndex +1);
        	return nRow;
    	},
	});
	
	// search
	$("#input-search").on('keyup', function (e) {
	    if (e.keyCode == 13) {
	    	//search(this.value);
	    	reload();
	    }
	}).on('blur', function() {
		//search(this.value);
		reload();
	});
	
	// reload data table
	function reload(table) {
		reportListCampaign.ajax.reload( null, true );
	}
});