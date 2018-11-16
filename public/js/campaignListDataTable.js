$(document).ready(function() {
	var pre = 10;
	var arrField = [
		"id",
		"name",
		"send_time",
		"subscriber_list",
		"status",
		"time_count_down"
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
        	url: "apis/campaigns",
    		type: "GET",
    		draw: 1,
    		data: function(d) {
    			var order = d.order[0];
    			var search = $("#input-search").val();
    			var obj = {
    				field: arrField[order.column],
    				orderBy: order.dir,
    				// search: d.search.value,
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
            		var updateUrl = window.link.updateCampaignLink.replace("__id", row.id)
	        		return "<a class='row-title' href='"+ updateUrl + "'>" + row.name + "</a>"

            }},
            { data: "", mRender: function (data, type, row) {
            		var date = new Date(row.send_time);
            		var data = "";
            		if (row.time_count_down > 0) {
            			data = data + 
			            			"<div class ='row clickToAmend'>" +
									"<div class='col-md-3 text-center'>" +
										"<strong>Timer:</strong> <br />" +
										"<span class='timer-countdown' data-time='" + row.time_count_down + "' data-id='" + row.id + "' onload='loadCountDown'></span>" +
									"</div>" +
								"</div>";
            		} 
            		
            		return data;
            }},
            { data: "send_time"},
            { data: "subscriber_list"},
            { data: "status", class: "text-capitalize", mRender: function (data, type, row) {
            		if (row.time_count_down <= 0) {
            			return "<span class='text-capitalize text-size-14" + (row.status == 'sent' ? ' badge badge-success' : row.status == 'draft' ? ' badge badge-danger' : ' badge badge-secondary') +"'>" + row.status + "</span>";
            		} else {
            			return 	"<div class ='row'>" +
            						"<div class='col-md-3'>" +
            							"<span class='text-capitalize text-size-14" + (row.status == 'sent' ? ' badge badge-success' : row.status == 'draft' ? ' badge badge-danger' : ' badge badge-secondary') +"'>" + row.status + "</span>" +
            						"</div>" +
            						"<div class='col-md-3 clickToAmend'>" +
            							"<a class='amend-action' href='javascript:void(0);' idData='" + row.id + "' title='Amend Campaign' content='Are you want amend this campaign?'>Amend</a>"
        							"</div>" +
            					"</div>"
            		}
            }},
            { data: "",  mRender: function (data, type, row) {
            	var iconEdit = 'eye';
            	var iconTitle = "View Campaign";
            	var isDelete = false;
            	if (window.link.group != "GROUP4" && (row.status == "draft" || row.status == "scheduled")) {
            		iconEdit = 'edit';
            		iconTitle = "Edit Campaign";
            		isDelete = true;
            	}
            	var updateUrl = window.link.updateCampaignLink.replace("__id", row.id)
            	var data = "<a class='btn btn-primary-action m-r-xs' title='" + iconTitle + "' href='" + updateUrl + "'><i class='fa fa-" + iconEdit + "'></i></a>";
            	if (window.link.group != "GROUP4") {
            		data += "<button class='btn btn-primary-action clone-campaign m-r-xs' href='#/' idData='" + row.id + "' title='Duplicate Campaign' content='Do you want to duplicate this campaign?' ><i class='fa fa-files-o'></i></button>";
            		
            		if (isDelete) {
                		data = data + "<button class='btn btn-primary-action delete-custom' href='#/' idData='" + row.id + "' title='Delete Campaign' content='Do you want to delete this campaign?' ><i class='fa fa-trash'></i></button>";				
    	            }
            	}
            	
            	return data;
            }}
        ],
        columnDefs: [
        	// hidden order column
        	{ 
	        	orderable: false, 
	        	targets: [0,2,3,4,5,6]
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
		],
		order: [[ 0, "desc" ]],
        fnRowCallback: function(nRow, aData, iDisplayIndex){
        	$("td:first", nRow).html(iDisplayIndex +1);
        		return nRow;
        },
    });
	
	// delete item
	$("#table-campaign tbody").on("click", ".delete-custom", function(e) {
	    	var title = $(this).attr("title");
	    	var content = $(this).attr("content");
	    	var id = $(this).attr("idData");
	    	$.fn.modalCustom(title, content, function(flag) {
	    		$.ajax({
	    			type: "POST",
	                url: "apis/campaigns/delete",
	                headers: {
	        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        	    },
	                data: {
	                		campaign_id : id,
	                },
	                success: function(success) {
	                	$.fn.showNotification('success', {content: success.message});
	            		reload(tableCampaign);
	            		return;
	                },
	                fail: function(error) {
	            		$.fn.showNotification('error', {content: success.message});
	            		return;
	                }
	            });
	    		return true;
	    	}, function (cancel) {
	    		return false;
	    	});
    });
	
	// clicked to amend campaign
	$("#table-campaign tbody").on("click", ".amend-action", function(e) {
	    	var title = $(this).attr("title");
	    	var content = $(this).attr("content");
	    	var id = $(this).attr("idData");
	    	$.fn.modalCustom(title, content, function(flag) {
	    		$.ajax({
	    			type: "POST",
	                url: "apis/campaigns/amend",
	                headers: {
	        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        	    },
	                data: {
	                		campaign_id : id,
	                },
	                success: function(success) {
	            		reload(tableCampaign);
	            		return;
	                },
	                fail: function(error) {
	            		return;
	                }
	            });
	    		return true;
	    	}, function (cancel) {
	    		return false;
	    	});
    });
	
	// clone campaign
	$("#table-campaign tbody").on("click", ".clone-campaign", function(e) {
    	var title = $(this).attr("title");
    	var content = $(this).attr("content");
    	var id = $(this).attr("idData");
    	$.fn.modalCustom(title, content, function(flag) {
    		$.ajax({
    			type: "POST",
                url: "apis/campaigns/clone",
                headers: {
        			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	    },
                data: {
                		id : id,
                },
                success: function(success) {
                	$.fn.showNotification('success', {content: success.message});
                	if(success.warring != "") {
                		$.fn.showNotification('warring', {content: success.warring});
                	}
            		reload(tableCampaign);
            		return;
                },
                error: function(error) {
                	$.fn.showNotification('error', {content: error.message});
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
	    	// search(this.value);
	    	reload();
	    }
	}).on('blur', function() {
		// search(this.value);
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
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

		$.ajax({
			type: "POST",
            url: "apis/campaigns/delete",
            data: {
            		campaign_id : id,
            },
            dataType: "json",
            success: function(success) {
            		reload(table);
            },
            fail: function(error) {
            }
        });
	}
	
	// reload data table
	function reload(table) {
		tableCampaign.ajax.reload( null, true );
	}
	
	// fn search data
	function search(value) {
		tableCampaign.search(value).draw();
	}
	
	function loadCountDown() {
		var currentDate = new Date();
	    var timer = $('.timer-countdown');
	    $.each(timer, function(index, value){
    		var time_count_down = parseInt(value.getAttribute("data-time"));
    		var id = value.getAttribute("data-id");
		    var self = $(value);
		    self.countdown({until: time_count_down, format: 'MS', compact: true, onExpiry: function() {
					var tdPar = self.closest('tr');
					$('.clickToAmend', tdPar).remove();
				}
			});

	    })
	}
	
	$('#table-campaign').on( 'draw.dt', loadCountDown);
	
} );
