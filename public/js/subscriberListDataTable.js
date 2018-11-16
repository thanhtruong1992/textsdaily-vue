$(document).ready(function() {
	var pre = 10;
	var arrField = [
		"id",
		"name",
		"updated_at",
		"active_subscribers",
		"inactive_subscribers",
		"canBeDeleted",
	];
	
	var tableSubscribers = $('#table-subscribers').DataTable( {
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
        	url: "apis/subscribers-list",
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var search = $("#input-search").val();
    			var obj = {
    				field: arrField[order.column],
    				orderBy: order.dir,
    				//search: d.search.value,
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
    		{ data: "", mRender: function() {
    			return "";
    		}},
            { data: "name", mRender: function (data, type, row) {
            		return "<a class='row-title' href='subscriber-lists/" + row.id + "'>" + row.name + "</a>"
            }},
            { data: "updated_at"},
            { data: "active_subscribers"},
            { data: "inactive_subscribers"},
            { data: "",  mRender: function (data, type, row) {
            		var data = "<a class='btn btn-primary-action m-r-xs' title='View Subscriber List' href='subscriber-lists/" + row.id + "'><i class='fa fa-eye'></i></a>";
            		if (window.link.group != "GROUP4") {
		            	if (row.canBeDeleted && row.is_global == false) {
		            		data = data + "<button class='btn btn-primary-action delete-custom m-r-xs' idData='" + row.id + "' title='Delete Subscriber List' content='Are you want delete this subscriber list?' ><i class='fa fa-trash'></i></button>";			
		            	} 
            		}
            		var dropdownAction = `<button class="btn btn-primary-action" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">`;
            		dropdownAction += `<i class="fa fa-cogs" aria-hidden="true"></i>`;
            		dropdownAction += `</button>`;
            		dropdownAction += `<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
            		dropdownAction += `<a class="dropdown-item" href="`+ window.subscriber.detailUrl +`">`+ window.subscriber.browse_subscribers +`</a>`;
            		if (window.link.group == "GROUP3" && row.is_invalid == false) {
            			dropdownAction += `<a class="dropdown-item" href="`+ window.subscriber.viewAddUrl +`">`+ window.subscriber.add_subscribers +`</a>`;
            			dropdownAction += `<a class="dropdown-item" href="`+ window.subscriber.viewUpdateUrl +`">`+ window.subscriber.update_subscribers_status +`</a>`;
            			dropdownAction += `<a class="dropdown-item" href="`+ window.subscriber.viewRemoveUrl +`">`+ window.subscriber.remove_subscribers +`</a>`;
            		}
            		dropdownAction += `<a class="dropdown-item" href="`+ window.subscriber.exportSubscriberUrl +`">`+ window.subscriber.export_subscribers +`</a>`;
            		dropdownAction += `</div>`;
            		dropdownAction = dropdownAction.replace(/__ID__/g, row.id);
	            	
	            	return data + dropdownAction;
            }}
        ],
        columnDefs: [
        	{ "name": "browser",  "targets": 1 },
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0, 3, 4, 5]
        	},
        	// hidden search column
        	{ 
	        	searchable: false,
	    		targets: [0,1,2,3]
        	},
        	// hidden column
        	{ 
        		visible: false,
        		targets: []
        	}
		],
		aaSorting: [[0, 'desc']],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
        	$("td:first", nRow).html(iDisplayIndex +1);
        		return nRow;
        },
    });
	
	// delete item
	$("#table-subscribers tbody").on("click", "td .delete-custom", function() {
    	var title = $(this).attr("title");
    	var content = $(this).attr("content");
    	var id = $(this).attr("idData");
    	
    	$.fn.modalCustom(title, content, function(flag) {
    		$.ajax({
    			type: "POST",
                url: "apis/subscribers-list/delete",
                headers: {
        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	    },
                data: {
                		list_id : id,
                },
                success: function(success) {
            		reload(tableSubscribers);
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
    	
    	return;
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
	
	// delete item
	function deleteItemTable(id, table) {
		$.ajax({
			type: "POST",
            url: "apis/subscribers-list/delete",
            headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	    },
            data: {
            		list_id : id,
            },
            success: function(success) {
        		reload(table);
            },
            fail: function(error) {
        		console.log(error);
            }
        });
	}
	
	// reload data table
	function reload(table) {
		tableSubscribers.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		tableSubscribers.search(value).draw();
	}
} );