jQuery(function($) {
	$('#frmClientCheckAll').on('click', function() {
		$(':checkbox.frmClientCheck').prop('checked', this.checked);
	});
	// Handler delete client
	$('#deleteClient').on('click', function() {
		var listClientIDSelected = getClientWasChecked();
		if (listClientIDSelected.length > 0) {
			var title = "Delete Client";
			var content = "Are you sure to delete the selected clients?";
			$.fn.modalCustom(title, content, function(flag) {
				// call ajax delete item
				$.ajax({
					type: "POST",
					url: window.link.api_delete_client,
					headers: {
						'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
					},
					data: {
						list_id: listClientIDSelected
					},
					success: function(success) {
						location.reload();
						return;
					},
					fail: function(error) {
						return;
					}
				});
	    	}, function (cancel) {
	    		return false;
	    	});
		} else {
			$.fn.showNotification("error", {content: window.link.no_item_delete});
		}
	});

	// Handler enable client
	$('#enableClient').on('click', function() {
		console.log('enable client');
		updateStatusClient(true);
	});
	// Handler disable client
	$('#disableClient').on('click', function() {
		console.log('disable client');
		updateStatusClient(false);
	});

	function updateStatusClient(status) {
		if (status == null) {
			return false;
		}
		var listClientIDSelected = getClientWasChecked();
		if (listClientIDSelected.length <= 0) {
			$.fn.showNotification("error", {content: status ? window.link.no_item_enable : window.link.no_item_disable});
			return false;
		}
		$.ajax({
			type : "POST",
			url : window.link.api_update_status_client,
			headers : {
				'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
			},
			data : {
				list_id : listClientIDSelected,
				status : status
			},
			success : function(success) {
				console.log(success);
				if (success['status']) {
					location.reload();
				}
				return;
			},
			fail : function(error) {
				return;
			}
		});
	}

	function getClientWasChecked() {
		var listChecked = $('.frmClientCheck:checked'); // list checked
		var listClientIDSelected = [];
		$.each(listChecked, function(index, value) {
			if ($(this).val() == "checkAll") {
				return true; // continue
			}
			console.log($(this).val());
			listClientIDSelected.push($(this).val());
		});
		return listClientIDSelected;
	}
});
