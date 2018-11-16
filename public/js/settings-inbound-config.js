$(document).ready(function() {
	
	var pre = 10;
	var arrField = [
		"number",
		"expiry_date",
		"user_id",
		"action",
	];
	
	var tableInboundConfig = $('#table-inbound-config').DataTable( {
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
        	url: window.settings.InboundConfig.getInboundConfigUrl,
    		type: "POST",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var obj = {
    				field: arrField[order.column],
    				orderBy: order.dir,
    				search: d.search.value,
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
            { data: "number"},
            { data: "expiry_date", mRender: function(data, type, row) {
            	return $.fn.formatDateTime(data, false);
            }},
            { data: "user_id"},
            { data: "",  mRender: function (data, type, row) {
            		var data = '';
            		if( row.user_id ) {
            			data = "<a class='btn btn-primary-action m-r-xs btnUnassign' title='__UNASSIGN_TITLE__' href='javascript:void(0)' data-id='__ID__' data-content='__UNASSIGN_CONFIRM__'><i class='fa fa-lock'></i></a>";
            		} else {
            			data = "<a class='btn btn-primary-action m-r-xs' title='__ASSIGN_TITLE__' href='__EDIT_URL__'><i class='fa fa-unlock'></i></a>";
            		}
	            		data = 	data.replace('__ASSIGN_TITLE__', window.settings.InboundConfig.assignTitle)
	            				.replace('__UNASSIGN_TITLE__', window.settings.InboundConfig.unassignTitle)
	            				.replace('__UNASSIGN_CONFIRM__', window.settings.InboundConfig.unassignConfirm)
	            				.replace('__EDIT_URL__', window.settings.InboundConfig.editURL)
	            				.replace(/__ID__/g, row.id);
	            	return data;
            }}
        ],
        aaSorting: [[ 1, "asc" ]], // default sort
        columnDefs: [
        	{ "number_pattern": "browser",  "targets": 1 },
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0]
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
        ]
    });
	
	$('#table-inbound-config tbody').off('click', 'td .btnUnassign').on('click', 'td .btnUnassign', function(){
		var title = $(this).attr("title");
		var content = $(this).data("content");
		var id = $(this).data('id');
		//
		$.fn.modalCustom(title, content, function(flag) {
    		$.ajax({
    			type: "POST",
                url: window.settings.InboundConfig.unassignURL,
                headers: {
        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	    },
                data: {
                		id : id,
                },
                success: function(success) {
            		reload(tableInboundConfig);
            		$.fn.showNotification("success", {content: success});
            		return;
                },
                error: function(error) {
                	$.fn.showNotification("error", {content: error.responseJSON.message});
            		return;
                }
            });
    		return true;
    	}, function (cancel) {
    		return false;
    	});
		return false;
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
	function reload(table) {
		table.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		table.search(value).draw();
	}
} );