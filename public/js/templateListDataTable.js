$(document).ready(function() {
	var pre = 10;
	var arrField = [
		"id",
		"name",
		"created_at",
		"updated_at"
	];
	
	var tableCampaign = $('#table-campaign').DataTable( {
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
        	url: window.link.api_get_list_template,
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var search = $("#input-search").val();
    			var obj = {
    				field: arrField[order.column],
    				orderBy: order.dir,
    				search: search,
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
    		{ data: "id"},
            { data: "name", mRender: function (data, type, row) {
            		var updateUrl = window.link.updateTemplateLink.replace("__id", row.id)
	        		return "<a class='row-title' href='"+ updateUrl + "'>" + row.name + "</a>"

            }},
            { data: "created_at"},
            { data: "updated_at"},
            { data: "", mRender: function (data, type, row) {
            		var updateUrl = window.link.updateTemplateLink.replace("__id", row.id);
            		var data = "<a class='btn btn-primary-action m-r-xs' title='Edit Template' href='" + updateUrl + "'><i class='fa fa-edit'></i></a>";
            		data = data + "<button class='btn btn-primary-action delete-custom' href='#/' idData='" + row.id + "' title='Delete Template' content='Do you want to delete this template?' ><i class='fa fa-trash'></i></button>";
            		return data;
            }}
        ],
        aaSorting: [[ 0, "desc" ]], // default sort
        columnDefs: [
        	{ "name": "browser",  "targets": 1 },
        	// custom button action
        	/*
			 * { targets: -1, data: null, defaultContent: "<button
			 * class='btn-action delete-custom' title='Delete Campaign'
			 * content='Are you want delete campaign?'><i class='fa fa-trash'></i></button>" },
			 */
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0, 4]
        	},
        	// hidden search column
        	{ 
	        	searchable: false,
	    		targets: [0,1]
        	},
        	// hidden column
        	{ 
        		visible: false,
        		targets: []
        	}
        ]
    });
	
	// delete item
	$("#table-campaign tbody").on("click", ".delete-custom", function(e) {
	    	var title = $(this).attr("title");
	    	var content = $(this).attr("content");
	    	var id = $(this).attr("idData");
	    	$.fn.modalCustom(title, content, function(flag) {
	    		$.ajax({
	    			type: "POST",
	                url: window.link.deleteTemplateLink,
	                headers: {
	        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        	    },
	                data: {
	                		template_id : id,
	                },
	                success: function(success) {
	                	$.fn.showNotification('success', {content: success.message});
	            		reload(tableCampaign);
	            		return;
	                },
	                fail: function(error) {
	            		console.log(error);
	            		$.fn.showNotification('error', {content: success.message});
	            		return;
	                }
	            });
	    		return true;
	    	}, function (cancel) {
	    		return false;
	    	});
    });
	
	// search
	$("#input-search").on('keyup', function (e) {
	    if (e.keyCode == 13) {
	    	search(this.value);
	    }
	}).on('blur', function() {
		search(this.value);
	});
	
	
	// show colum hidden
	$('.show-column').click(function() {
		if ($(this).is(':checked')) {
			var index = arrField.indexOf($(this).val());
			table.columns([index]).visible(true);
		}else {
			var index = arrField.indexOf($(this).val());
			table.columns([index]).visible(false);
		}
	});
	
	// reload data table
	function reload(table) {
		table.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		tableCampaign.search(value).draw();
	}
} );