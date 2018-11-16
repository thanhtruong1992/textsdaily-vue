$(document).ready(function() {
	var pre = 10;
	var arrField = [
		"name",
		"position",
		"office",
		"extn",
		"start_date",
		"salary"
	];
	
	var table = $('#example').DataTable( {
		bInfo: false,
    		processing: true,
        serverSide: true,
        searching: true, // hidden input search
        ordering: true, // show sort
        lengthChange: false,
        bDestroy: true,
        paging: true,
        ajax: {
        	url: "apis/subscribers",
    		type: "GET",
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
            { data: "name",  class: "test"},
            { data: "position"},
            { data: "office", },
            { data: "extn", },
            { data: "start_date",},
            { data: "salary",},
            { data: "",  mRender: function (data, type, row) {                
                return "<button class='btn-action delete-custom' idData='" + row.id + "' title='Delete Campaign' content='Are you want delete campaign?'><i class='fa fa-trash'></i></button>"
            }}
        ],
        aaSorting: [[ 1, "desc" ]], // default sort
        columnDefs: [
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0,3,4,5,6]
        	},
        	// hidden search column
        	{ 
	        	searchable: false,
	    		targets: [0,1,2,3,4,5,6]
        	},
        	// hidden column
        	{ 
        		visible: false,
        		targets: []
        	}
        ]
    });
	
	// delete item
	$("#example tbody").on("click", ".delete-custom", function() {
    	var title = $(this).attr("title");
    	var content = $(this).attr("content");
    	var id = $(this).attr("idData");
    	$.fn.modalCustom(title, content, function(flag) {
    		// save modal
    		// call ajax delete item
    		// reload datatable
    		reload(table);
    	}, function (cancel) {
    		// cancel modal
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
		table.search(value).draw();
	}
} );