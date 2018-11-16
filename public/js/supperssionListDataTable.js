$(document).ready(function() {
	var pre = 10;
	var arrField = [ "check", "id", "phone", "campaign_id", "unsubscription_date"];

	var arrColumn = [
	{
		data : "",
		mRender : function(data, type, row) {
			return '<label class="custom-control custom-checkbox">'
					+ '<input type="checkbox" class="custom-control-input frmCheck" value="'
					+ row.id
					+ '" name="Mark"/>'
					+ '<span class="custom-control-indicator"></span>'
					+ '</label>';
		}
	}, {
		data : "id", mRender: function(data, type, row) {
			return "";
		}
	}, {
		data : "phone_encrypted",
	}, {
		data : "campaign_name", mRender: function(data, type, row) {
			if(data != null) {
				var updateUrl = window.link.updateCampaignLink.replace("__id", row.campaign_id);
				return '<a href="' + updateUrl + '">' + data + '</a>';
			}
			
			return "";
		}
	}, {
		data: "unsubscription_date", mRender: function(data, type, row) {
			return $.fn.formatDateTime(data);
		}
	}];

	var arrHiddenField = [];

	var host = window.location.origin;
	var list_id = $("input[name=list_id]").val();
	var tableSubscriber = $('#table-subscriber').DataTable({
		bInfo : false,
		processing : true,
		serverSide : true,
		searching : false,
		ordering : true,
		lengthChange : false,
		bDestroy : true,
		paging : true,
		ajax : {
			url : host + "/admin/apis/subscribers/" + list_id,
			type : "GET",
			draw : 1,
			data : function(d) {
				var order = d.order[0];
				var filter = {};
				var flagFilter = $(".flag-filter").val();
				var obj = {
					field : arrField[order.column],
					orderBy : order.dir,
					search : d.search.value,
					page : (d.start / 10) + 1,
					flagFilter : flagFilter,
				};

				filter = getFilter();
				obj.filter = JSON.stringify(filter);
				return obj;
			},
			error : function() {
				return {
					recordsTotal : 0,
					recordsFiltered : 0,
					data : []
				}
			}
		},
		columns : arrColumn,
		aaSorting : [ [ 2, "desc" ] ], // default sort
		columnDefs : [
		// hidden order column
		{
			orderable : false,
			targets : [ 0,2,3 ]
		},
		// hidden search column
		{
			searchable : false,
			targets : []
		},
		// hidden column
		{
			visible : false,
			targets : arrHiddenField
		}, ],
		fnRowCallback: function(nRow, aData, iDisplayIndex){
        		$("td:eq(1)", nRow).html(iDisplayIndex +1);
		},
		language : {
			paginate : {
				next : 'Next', // or '→'
				previous : 'Prev' // or '←'
			}
		},
		initComplete : function(settings, json) {
			if (!tableSubscriber.data().any()) {
				var total = arrHiddenField.length + 6;
				$("td:first").attr("colspan", total);
			}
		},
	});

	// delete item
	$("#table-subscriber tbody").on("click","td .delete-custom",function() {
		var title = $(this).attr("title");
		var content = $(this).attr("content");
		var id = $(this).attr("idData");

		$.fn.modalCustom(title, content, function(flag) {
			$.ajax({
				type : "POST",
				url : "apis/subscribers/{id}/delete",
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				data : {
					list_id : id,
				},
				success : function(success) {
					reload(tableSubscriber);
					return;
				},
				fail : function(error) {
					return;
				}
			});
			return true;
		}, function(cancel) {
			return false;
		});

		return;
	});

	// search
	$("#input-search").on('keyup', function(e) {
		if (e.keyCode == 13) {
			search(this.value);
		}
	}).on('blur', function() {
		search(this.value);
	});

	$("#applyFilter").click(function() {
		tableSubscriber.ajax.reload(null, true);
	});

	// show colum hidden
	$('.show-column').click(function() {
		if ($(this).is(':checked')) {
			var index = arrField.indexOf($(this).val());
			tableSubscriber.columns([ index ]).visible(true);
		} else {
			var index = arrField.indexOf($(this).val());
			tableSubscriber.columns([ index ]).visible(false);
		}
	});

	// detele subscriber
	$('.delete-subscriber').click(function() {
		var listID = [];
		$(".frmCheck:checked").each(function() {
			if ($(this).val() != "checkAll") {
				listID.push($(this).val());
			}
		});

		if (listID.length) {
			deleteSubscribers(listID, function success() {
				$("#frmCheckAll").prop('checked', false);
				tableSubscriber.ajax.reload(null, true);
			}, function error() {

			});
		}
	});

	// reload data table
	function reload(table) {
		table.ajax.reload(null, true);
	}

	// fn search data
	function search(value) {
		tableSubscriber.search(value).draw();
	}

	$("#resetFormFilter").click(function() {
		$("#formFilter")[0].reset();
		tableSubscriber.ajax.reload(null, true);
	});

	// close input filter subscriber
	$(".close-filter").on("click", function() {
		var id = $(this).attr("id");
		$("input[name='" + id + "']").prop("checked", false);
		$("#" + id).css("display", "none");
	});

	// show filter
	$('.showRule').on('click', function() {
		var className = $(this).val();
		if ($(this).is(':checked')) {
			$("#" + className).css("display", "flex");
		} else {
			$("#" + className).css("display", "none");
		}
	});
});

function exportSubscribers(enscrypted) {
	var filter = getFilter();
	var list_id = $("input[name=list_id]").val();
	var flagFilter = $(".flag-filter").val();
	var enscrypted = enscrypted ? true : false;
	var data = {
		enscrypted : enscrypted,
		filter : JSON.stringify(filter),
		flagFilter : flagFilter
	};
	var url = window.location.origin + "/admin/apis/subscribers/" + list_id
			+ '/export?enscrypted=' + enscrypted + "&filter="
			+ JSON.stringify(filter) + "&flagFilter=" + flagFilter;
	window.open(url, '_blank');
}

function deleteSubscribers(listID, success, error) {
	var list_id = $("input[name=list_id]").val();
	var title = "Delete Subscriber";
	var content = "Are you want delete subscribers?";
	$.fn.modalCustom(title, content, function(flag) {
		var url = window.location.origin + "/admin/apis/subscribers/"
				+ list_id;
		$.ajax({
			type : "DELETE",
			headers : {
				'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr(
						'content')
			},
			url : url,
			data : {
				ids : listID
			},
			success : function(res) {
				$.fn.showNotification("success", {
					content : res.message
				});
				success();
			},
			error : function(err) {
				$.fn.showNotification("error", {
					content : res.message
				});
				error();
			}
		});
	}, function(cancel) {
		return false;
	});
}

function getFilter() {
	var filter = {};
	if (!!$('#subscriber_status').is(':visible')) {
		var val = $(".subscriber_status").val();
		if (val) {
			var field = $(".subscriber_status").attr("name");
			var flag = $(".subscriber_status_flag").val();
			filter.status = {
				field : field,
				flag : flag,
				val : val
			};
		}
	}
	if (!!$('#mobile_number').is(':visible')) {
		var val = $(".mobile_number").val();
		if (val) {
			var field = $(".mobile_number").attr("name");
			var flag = $(".mobile_number_flag").val();
			filter.phone = {
				field : field,
				flag : flag,
				val : val
			};
		}
	}
	if (!!$('#first_name').is(':visible')) {
		var val = $(".first_name").val();
		if (val) {
			var field = $(".first_name").attr("name");
			var flag = $(".first_name_flag").val();
			filter.first_name = {
				field : field,
				flag : flag,
				val : val
			};
		}
	}
	if (!!$('#last_name').is(':visible')) {
		var val = $(".last_name").val();
		if (val) {
			var field = $(".last_name").attr("name");
			var flag = $(".last_name_flag").val();
			filter.last_name = {
				field : field,
				flag : flag,
				val : val
			};
		}
	}

	return filter;
}