$(document).ready(function() {
	
	// Upload file
	$('#btnUpload').off('click').on('click', function() {
		var formEle = $('#settingsUploadForm');
		var fileEle = $("input[name='fileUpload']", formEle);
		if ( fileEle.valid() ) {
			var data = new FormData();
			$.each(fileEle[0].files, function(key, value)
		    {
		        data.append('fileUpload', value);
		    });
			//
			$.ajax({
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				url: window.settings.MobilePattern.ajaxUploadMobilePatternUrl,
		        type: 'POST',
		        data: data,
		        cache: false,
		        dataType: 'json',
		        processData: false, // Don't process the files
		        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		        success: function(result, textStatus, jqXHR)
		        {
		        	if( result.status ) {
		        		$.fn.showNotification('success', {'content': result.msg});
		        		$('#settingsUploadModel').modal('hide');
		        		window.setTimeout(function(){location.reload()},1000)
		        	} else {
		        		$.fn.showNotification('error', {'content': result.msg});
		        	}
		        },
		        error: function(jqXHR, textStatus, errorThrown)
		        {
		            // Handle errors here
		            console.log('ERRORS: ' + textStatus);
		            // STOP LOADING SPINNER
		        }
		    });
		}
	});
	
	var pre = 10;
	var arrField = [
		"number_pattern",
		"country",
		"network",
		"action",
	];
	
	var tableMobilePattern = $('#table-mobile-pattern').DataTable( {
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
        	url: window.settings.MobilePattern.getMobilePatternUrl,
    		type: "POST",
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
            { data: "number_pattern"},
            { data: "country"},
            { data: "network"},
            { data: "",  mRender: function (data, type, row) {
            		var data = "<a class='btn btn-primary-action m-r-xs' title='__EDIT_TITLE__' href='__EDIT_URL__'><i class='fa fa-edit'></i></a>"
	            			 + "<button class='btn btn-primary-action delete-custom' idData='__ID__' title='__DELETE_TITLE__' content='__DELETE_CONFIRM__' ><i class='fa fa-trash'></i></button>";
	            		data = 	data.replace('__EDIT_TITLE__', window.settings.MobilePattern.editTitle)
	            				.replace('__EDIT_URL__', window.settings.MobilePattern.editURL)
	            				.replace('__DELETE_TITLE__', window.settings.MobilePattern.deleteTitle)
	            				.replace('__DELETE_CONFIRM__', window.settings.MobilePattern.deleteConfirm)
	            				.replace(/__ID__/g, row.id);
	            	return data;
            }}
        ],
        aaSorting: [[ 1, "asc" ]], // default sort
        columnDefs: [
        	{ "class": "text-center",  "targets": 3 },
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0,3]
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
	
	// delete item
	$("#table-mobile-pattern tbody").on("click", "td .delete-custom", function() {
    	var title = $(this).attr("title");
    	var content = $(this).attr("content");
    	var id = $(this).attr("idData");
    	
    	$.fn.modalCustom(title, content, function(flag) {
    		$.ajax({
    			type: "POST",
                url: window.settings.MobilePattern.deleteURL,
                headers: {
        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	    },
                data: {
                		id : id,
                },
                success: function(success) {
            		reload(tableMobilePattern);
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
	
	// reload data table
	function reload(table) {
		tableMobilePattern.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		tableMobilePattern.search(value).draw();
	}
} );