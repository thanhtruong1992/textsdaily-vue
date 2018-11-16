// FN Custom show error message for select2
function selec2Validate(ele) {
	var parEle = $(ele).closest('.justify-content-start');
	if ($(ele).hasClass('error')) {
		var errorEle = $('label.error', parEle);
		parEle.append(errorEle);
		$('.select2-selection--multiple, .select2-selection--single', parEle)
				.addClass('error');
	} else {
		$('label.error', parEle).remove();
		$('.select2-selection--multiple, .select2-selection--single', parEle)
				.removeClass('error');
	}
}

jQuery(function($) {

	// Get all sender option value select when form loaded
	var sender_list_option = [];
	$('#sender > option').each(function() {
		sender_list_option.push(this.value);
	});

	$('.select2-custom').on('change', function() {
		if ($(this).val()) {
			$(this).removeClass('error');
			selec2Validate($(this));
		}
	});

	$('input[name="schedule_type"]').on('change', function() {
		if ($(this).val() == 'FUTURE') {
			$('#inputDate').removeAttr('disabled');
			$('#send_timezone').removeAttr('disabled');
		} else {
			$('#inputDate').attr('disabled', 'disabled');
			$('#send_timezone').attr('disabled', 'disabled');
			$("#validDate").css("display", "none");
			$("#inputDate").removeClass("error");
		}
	});

	// detect time
	$("#inputDate").on('blur', function() {
		var retsult = $.fn.validationDatimeCampaign();

	});
	$("#send_timezone").on("change", function() {
		var retsult = $.fn.validationDatimeCampaign();
	});

	$('#addShortLink').click(function() {
		$(this).attr("disabled", true);
		var link = $("input[name='linkShort']").val();
		var invalid = $("input[name='linkShort']").attr('aria-invalid');
		if (link.length == 0 || invalid == 'true') {
			$("input[name='linkShort']").valid();
			$("#addShortLink").attr("disabled", false);
		} else {
			var data = {
				link : link,
				campaign_id : ''
			};
			// short link
			$.fn.shortLink(data, function(res) {
				// success
				$('#addUrlModal').modal('hide');
				var campaign_link_id = $("input[name='campaign_link_id']").val();
				campaign_link_id = campaign_link_id.length == 0 ? res.campaign_link_id : campaign_link_id + "," + res.campaign_link_id;
				$("input[name='campaign_link_id']").val(campaign_link_id);
				var message = $("#message").val();
				var position = $('#message').getCursorPosition();
				message = message.substring(0, position) + res.short_link + message.substring(position, message.length);
				$("#message").val(message);
				$("#message").trigger('onkeyup');
				$("#addLinkForm")[0].reset();
				$("#addShortLink").attr("disabled", false);
			}, function() {
				// error
				$("#addShortLink").attr("disabled", false);
			});
		}
	});

	$('#addPersonalizeShortLink').click(function() {
		$(this).attr("disabled", true);
		var link = $("textarea[name='personalizeLinkShort']").val();
		var invalid = $("input[name='personalizeLink']").attr('aria-invalid');
		if (link.length == 0 || invalid == 'true') {
			$("input[name='personalizeLink']").valid();
			$("#addPersonalizeShortLink").attr("disabled", false);
		} else {
			var data = {
				link : link,
				campaign_id : ''
			};
			// short link
			$.fn.shortLink(data, function(res) {
				// success
				$('#personalizeUrlModal').modal('hide');
				var campaign_link_id = $("input[name='campaign_link_id']").val();
				campaign_link_id = campaign_link_id.length == 0 ? res.campaign_link_id : campaign_link_id + "," + res.campaign_link_id;
				$("input[name='campaign_link_id']").val(campaign_link_id);
				var message = $("#message").val();
				var position = $('#message').getCursorPosition();
				message = message.substring(0, position) + res.short_link + message.substring(position, message.length);
				$("#message").val(message);
				$("#message").trigger('onkeyup');
				$("#addPersonalizeForm")[0].reset();
				$("#addPersonalizeShortLink").attr("disabled", false);
			}, function() {
				// error
				$("#addPersonalizeShortLink").attr("disabled", false);
			});
		}
	});


	$('#reset-campaign').on('click', function() {
		$('#campaignForm').trigger('reset');
		$('#message').keyup();
		$('#selectList > option').removeAttr('selected');
		$('#selectList').select2();
	});

	$('#create-campaign').on('click', function() {
		var validator = $('#campaignForm').validate();
		$('input[name="test_recipients"]').rules('remove', 'required');
		$('input[name="test_recipients"]').removeClass('error');
		validator.reset();
		$('#campaignForm').submit();
		// Custom show error message for select2
		$('.select2-custom').each(function() {
			selec2Validate($(this));
		});
	});

	$('.button-send').on('click', function() {
		$('#campaignForm').validate();
		var sender = $('.select-sender').val();
		var test_number = $('input[name="test_recipients"]').val();
		var message = $('textarea[name="message"]').val();
		var language = $('input[name="language"]:checked').val();
		var list_id = [];
		$.each($('#selectList').select2('data'), function(index, item){
			list_id.push(item.id);
		});
		var error_message = window.link.test_recipients_error_message;
		var buttonSend = $(this);
		$('input[name="test_recipients"]').rules("add", {
			required : true,
			messages : {
				required : error_message
			}
		});
		if ($(".select-sender").valid() && $('textarea[name="message"]').valid() && $("input[name='test_recipients']").valid()) {
			buttonSend.prop('disabled', true);
			$.ajax({
				type : "POST",
				url : window.link.api_test_send_sms,
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				data : {
					sender : sender,
					list_id : list_id,
					message : message,
					phone_number : test_number,
					language: language
				},
				success : function(success) {
					if (success["status"]) {
						showErrorModalPopup(
							true,
							success["phone_number_sent"],
							success["phone_number_invalid"],
							success["phone_number_pending"],
							success["phone_number_rejected"],
							success["phone_number_expired"]
						);
					} else {
						showErrorModalPopup(false, [], [], [], [], [],
								success["message"]);
					}
					buttonSend.prop('disabled', false);
					return;
				},
				error : function(error) {
					buttonSend.prop('disabled', false);
					return;
				}
			});
		}
	});

	function showErrorModalPopup(status, numberSuccess, numberInvalid, numberPending, numberReject, numberExpired, message = "") {
		var options = {
			"backdrop" : "static",
			"show" : true
		};

		if (numberSuccess.length <= 0) {
			$('#contentSMSSuccess').hide();
		} else {
			$('#contentSMSSuccess').show();
			$.each(numberSuccess, function(index, item) {
				item = index > 0 ? "; " + item   : item;
				$('#itemSuccessGroup').append(
						"<span class='item-phone-number-success'>" + item + "</span>");
			})
		}

		if (numberPending.length <= 0) {
			// $('#contentSMSPending').hide();
			$('#contentSMSPending').show();
			$('#messagePeddingSuccess').remove();
		} else {
			$('#contentSMSPending').show();
			$("#messagePeddingFail").remove();
			$.each(numberPending, function(index, item) {
				item = index > 0 ? "; " + item : item;
				$('#itemPendingGroup').append("<span class='item-phone-number-pending'>" + item + "</span>");
			})
		}
		if (numberReject.length <= 0) {
			$('#contentSMSRejected').hide();
		} else {
			$('#contentSMSRejected').show();
			$.each(numberReject, function(index, item) {
				item = index > 0 ? "; " + item : item;
				$('#itemRejectedGroup').append("<span class='item-phone-number-rejected'>" + item + "</span>");
			})
		}
		if (numberExpired.length <= 0) {
			$('#contentSMSExpired').hide();
		} else {
			$('#contentSMSExpired').show();
			$.each(numberExpired, function(index, item) {
				item = index > 0 ? "; " + item : item;
				$('#itemExpiredGroup').append("<span class='item-phone-number-expired'>" + item + "</span>");
			})
		}

		if (numberInvalid.length <= 0) {
			$('#contentSMSFailed').hide();
		} else {
			$('#contentSMSFailed').show();
			$.each(numberInvalid, function(index, item) {
				item = index > 0 ? "; " + item : item;
				$('#itemFailedGroup').append("<span class='item-phone-number-invalid'>" + item + "</span>");
			})
		}

		if (message != "") {
			$("#messageSMSReview").show().html(message);
		} else {
			$('#messageSMSReview').hide().empty();
		}

		$('#modalSMSReview').modal(options);

		$("#btnModalClose").off("click").on("click", function() {
			$('#modalSMSReview').modal('hide');
			$('#itemSuccessGroup').empty();
			$('#itemPendingGroup').empty();
			$('#itemUndeliveryGroup').empty();
			$('#itemRejectedGroup').empty();
			$('#itemExpiredGroup').empty();
			$('#itemFailedGroup').empty()
		});

		$(".close").off("click").on("click", function() {
			$('#modalSMSReview').modal('hide');
			$('#itemSuccessGroup').empty();
			$('#itemPendingGroup').empty();
			$('#itemUndeliveryGroup').empty();
			$('#itemRejectedGroup').empty();
			$('#itemExpiredGroup').empty();
			$('#itemFailedGroup').empty()
		});
	}

	// submit form add campaign
	$('#campaignForm').submit(function(e) {
		var flag = $(this).valid();
		if (!flag) {
			e.preventDefault();
		} else {
			var schedule_type = $('input[name="schedule_type"]:checked').val();
			var checkDateTime = false;
			
			if (schedule_type == 'FUTURE') {
				checkDateTime = $.fn.validationDatimeCampaign();
			}

			if (!!checkDateTime) {
				e.preventDefault();
			} else {
				var isSubmit = $("input[name='submit_form']").val();
				if (isSubmit == "false") {
					e.preventDefault();
					var list_id = $("#selectList").val();
					var total_sms = $("#totalMessage").attr("total");
					var message = $('#message').val();
					var personalize = 0;
				
					 // detect personalize
				    if(!!isPersonalize(message)) {
				    	personalize = 1;
				    }
					var params = {
						list_id : list_id.toString(),
						total_sms: parseInt(total_sms) + personalize
					};
					$.fn.summaryCampaign(params, function(res) {
						// success
						var schedule = $("input[name='schedule_type']:checked").val();
						var send_time = $("input[name='send_time']").val();
						var send_timezone = $("#send_timezone option:selected").html();
						var estimated_cost = parseFloat(res.total_price).toFixed(2);
						var billing_type = res.billing_type;
						var balance = (res.balance).toFixed(2);
						var data = {
							totalSubscriber : res.total_subscriber == "" ? 0 : res.total_subscriber,
							totalDuplicate : res.total_duplicate == "" ? 0 : res.total_duplicate,
							name : $("input[name='name']").val(),
							sender : $(".select-sender").val(),
							message : $("textarea[name='message']").val(),
							schedule : (schedule == "NOT_SCHEDULED") ? 'Draft' : (schedule == "IMMEDIATE") ? 'Now' : "time",
							number_of_sms : "",
							estimated_cost : parseFloat(res.total_price).toFixed(2) + " " + res.currency,
							to : res.subscrber_list_name,
							send_time : send_time,
							send_timezone : send_timezone,
							totalSMS: res.total_sms,
							data: res.data,
							currency: res.currency
						};
						
						var contentConfirm = "";
						if(billing_type == "UNLIMITED") {
							if(personalize == 1) {
								contentConfirm = enough_balance;
							}
						}else {
							if(schedule != "NOT_SCHEDULED") {
								if(estimated_cost > balance) {
									if(personalize == 1) {
										contentConfirm = not_enough_balance_with_personalize;
									}else {
										contentConfirm = not_enough_balance;
									}
								}else {
									if(personalize == 1) {
										contentConfirm = enough_balance;
									}
								}
							}
						}
						
						$.fn.modalConfirmCampaign(data, contentConfirm, function(data) {
							$.fn.openModalSummary(data, function() {
								if(estimated_cost > balance && schedule != "NOT_SCHEDULED" && billing_type != "UNLIMITED") {
			    					$("input[name='submit_form']").val(false);
			    					$.fn.showNotification("error", {content: limit_balance_when_create_campaign});
			    				}else {
			    					$('#summaryCampaignModal').modal('hide');
									// create campaign
									$("input[name='submit_form']").val(true);
									$('#campaignForm').submit();
			    				}
							}, function(err) {
								// edit
								// campaign
								$("input[name='submit_form']").val(false);
							});
						}, function(err) {
							$("input[name='submit_form']").val(false);
						});
					}, function(err) {
						// error
					});
	
					return false;
				}
			}
		}
	});

	// loadPersonalizeUrl
	$("#loadPersonalizeUrl").on('click', function() {
		if ($('.select-multiple').valid()) {
			// $('#personalizeUrlModal').modal({
			// 	"backdrop" : "static",
			// 	"show" : true
			// });
			loadPersonalizeUrlData($('.select-multiple').val());
			$('#frmModalCheckAll').prop('checked', false);
		} else {
			$('.select-multiple').each(function() {
				selec2Validate($(this));
			});
			return false;
		}
	});

	// Load personalize modal
	$('#loadPersonalize').on('click', function() {
		if ($('.select-multiple').valid()) {
			$('#personalizeModal').modal({
				"backdrop" : "static",
				"show" : true
			});
			loadPersonalizeData($('.select-multiple').val());
			$('#frmModalCheckAll').prop('checked', false);
		} else {
			$('.select-multiple').each(function() {
				selec2Validate($(this));
			});
			return false;
		}
	});

	// Personalize datatable
	var arrField = [ "id", "check" ];
	var tablePersonalize = null;
	function loadPersonalizeData(listId) {
		tablePersonalize = $('#table-personalize').DataTable({
			bInfo : false,
			processing : true,
			serverSide : true,
			searching : false,
			ordering : true,
			lengthChange : false,
			bDestroy : true,
			paging : false,
			ajax : {
				url : window.link.api_get_personalize + "?list_id=" + listId,
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
					};

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
			columns : [
				{ data : "field_name", mRender : function(data, type, row) {
							return row.field_name + (row.name ? ' (' + row.name + ')' : '');
				}},
				{data : "", mRender : function(data, type, row) {
					var field_name = "";
					switch (row.field_name) {
						case "Phone":
							field_name = "phone";
							break;
						case "First name":
							field_name = "firstname";
							break;
						case "Last name":
							field_name = "lastname";
							break;
						default:
							field_name = row.field_name
							break;
							
					}
					return '<label class="custom-control custom-checkbox">'
							+ '<input type="checkbox" class="custom-control-input frmModalCheck" value="'
							+ field_name
							+ '" name="Mark"/>'
							+ '<span class="custom-control-indicator"></span>'
							+ '</label>';
				}}, 
			],
			aaSorting : [ [ 0, "desc" ] ], // default sort
			columnDefs : [ {
				"name" : "browser",
				"targets" : 1
			},
			// hidden order column
			{
				orderable : false,
				targets : [ 0, 1 ]
			},
			// hidden search column
			{
				searchable : false,
				targets : []
			},
			// hidden column
			{
				visible : false,
				targets : []
			}, ],
			language : {
				paginate : {
					next : 'Next', // or '→'
					previous : 'Prev' // or '←'
				}
			}
		});
	}

	// Personalize Url datatable
	var arrField = [ "id", "check" ];
	var tablePersonalizeUrl = null;
	function loadPersonalizeUrlData(listId) {
		tablePersonalizeUrl = $('#table-personalize-url').DataTable({
			bInfo : false,
			processing : true,
			serverSide : true,
			searching : false,
			ordering : false,
			lengthChange : false,
			bDestroy : true,
			paging : false,
			ajax : {
				url : window.link.api_get_personalize + "?list_id=" + listId,
				type : "GET",
				draw : 1,
				data : function(d) {
					var obj = {
						page : (d.start / 10) + 1,
					};

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
			columns : [
				{ data : "field_name", mRender : function(data, type, row) {
					var field_name = "";
					switch (row.field_name) {
						case "Phone":
							field_name = "phone";
							break;
						case "First name":
							field_name = "firstname";
							break;
						case "Last name":
							field_name = "lastname";
							break;
						default:
							field_name = row.field_name
							break;
							
					}

					return "%%" + field_name + "%%";
				}},
				{data : "", mRender : function(data, type, row) {
					var field_name = "";
					switch (row.field_name) {
						case "Phone":
							field_name = "phone";
							break;
						case "First name":
							field_name = "firstname";
							break;
						case "Last name":
							field_name = "lastname";
							break;
						default:
							field_name = row.field_name
							break;
							
					}

					return '<input type="text" class="form-control input-personalize-url" readonly value="" name="' + field_name + '_url"/>';
				}},
				{data : "", mRender : function(data, type, row) {
					var field_name = "";
					switch (row.field_name) {
						case "Phone":
							field_name = "phone";
							break;
						case "First name":
							field_name = "firstname";
							break;
						case "Last name":
							field_name = "lastname";
							break;
						default:
							field_name = row.field_name
							break;
							
					}
					return '<label class="custom-control custom-checkbox">'
							+ '<input type="checkbox" class="custom-control-input frmModalCheckUrl" value="'
							+ field_name
							+ '" name="Mark"/>'
							+ '<span class="custom-control-indicator"></span>'
							+ '</label>';
				}},
			],
			aaSorting : [ [ 0, "desc" ] ], // default sort
			columnDefs : [ {
				"name" : "browser",
				"targets" : 1
			},
			// hidden order column
			{
				orderable : false,
				targets : [ 0, 1 ]
			},
			// hidden search column
			{
				searchable : false,
				targets : []
			},
			// hidden column
			{
				visible : false,
				targets : []
			}, ],
			language : {
				paginate : {
					next : 'Next', // or '→'
					previous : 'Prev' // or '←'
				}
			},
			drawCallback : function(){
				$("#table-personalize-url tbody").on('change', '.frmModalCheckUrl', function(e) {
					if($(this).is(':checked')) {
						$("input[name='" + $(this).val() + "_url']").removeAttr('readonly');
						handelPersonalizeUrl();
					}else {
						$("input[name='" + $(this).val() + "_url']").attr("readonly", "readonly");
						handelPersonalizeUrl();
					}
				});

				$(".input-personalize-url").on('blur', function(e) {
					if($(this).val() != "") {
						handelPersonalizeUrl();
					}
				});

				$(".personalize-url").on('blur', function(e) {
					handelPersonalizeUrl();
				});
			}
		});
	}

	// reload data table
	function reload(table) {
		table.ajax.reload(null, true);
	}

	$('#frmModalCheckAll').on('click', function() {
		$(':checkbox.frmModalCheck').prop('checked', this.checked);
	});

	$('#insertPersonalize').on('click', function() {
		var listChecked = $('.frmModalCheck:checked'); // list checked
		var textInput = ""
		var message = $('#message').val();
		$.each(listChecked, function(index, value) {
			if ($(this).val() == "checkAll") {
				return true;
			}
			var textInsert = "%%" + $(this).val() + "%%";
			textInput = textInput.length == 0 ? textInsert : textInput + " " + textInsert;
		});
		
		if(message.length > 0) {
			var position = $('#message').getCursorPosition();
			message = message.substring(0, position) + textInput + message.substring(position, message.length);
		}else {
			message = textInput;
		}
		
		$('#message').val(message);
		$('#personalizeModal').modal('hide');
		$('#message').keyup();
	});

	// Load template modal
	$('#loadTemplate').on('click', function() {
		$('#templateModal').modal({
			"backdrop" : "static",
			"show" : true
		});
		loadTemplateData();
	});

	// Personalize datatable
	var arrField2 = [ "name", "message"];
	var tableTemplate = null;
	function loadTemplateData() {
		tableTemplate = $('#table-template').DataTable({
			bInfo : false,
			processing : true,
			serverSide : true,
			searching : false,
			ordering : true,
			lengthChange : false,
			bDestroy : true,
			paging : false,
			ajax : {
				url : window.link.api_get_template,
				type : "GET",
				draw : 1,
				data : function(d) {
					var order = d.order[0];
					var filter = {};
					var flagFilter = $(".flag-filter").val();
					var obj = {
						field : arrField2[order.column],
						orderBy : order.dir,
						search : d.search.value,
						page : (d.start / 10) + 1,
					};

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
			columns : [
				{data : "name"},
				{data : "message"},
				{data : "", mRender : function(data, type, row) {
					return '<label class="custom-control custom-radio">'
							+ '<input type="radio" class="custom-control-input" value="'
							+ row.message
							+ '" name="template"/>'
							+ '<span class="custom-control-indicator"></span>'
							+ '</label>';
				}},
			],
			aaSorting : [ [ 0, "desc" ] ], // default sort
			columnDefs : [ 
				{
					"name" : "browser",
					"targets" : 1
				},
				// hidden order column
				{
					orderable : false,
					targets : [ 1, 2 ]
				},
				// hidden search column
				{
					searchable : false,
					targets : []
				},
				// hidden column
				{
					visible : false,
					targets : []
				},
			],
			language : {
				paginate : {
					next : 'Next', // or '→'
					previous : 'Prev' // or '←'
				}
			}
		});
	}
	
	$('#useTemplate').on('click', function() {
		if($('#table-template').DataTable().data().count() <= 0) {
			$('#templateModal').modal('hide');
			return false;
		}
		var message = $('input[name="template"]:checked').val();
		if (message == null) {
			$('#templateModal').modal('hide');
			return false;
		}
		$('#message').val(message);
		$('#message').keyup();
		$('#templateModal').modal('hide');
	});
	
	// add unsubscriber
	$("#unsubscribe").on('click', function() {
		var unsubscriber = "%%unsubscribe%%";
		var message = $("#message").val();
		var position = $('#message').getCursorPosition();
		message = message.substring(0, position) + unsubscriber + message.substring(position, message.length);
		$("#message").val(message);
		$("#message").trigger('onkeyup');
	});
});

function handelPersonalizeUrl() {
	var link = $("#contentLinkShort").val();
	var domain = $('.personalize-url').val();
	var query = "";
	$(".frmModalCheckUrl:checked").each(function(key, item) {
		var value = $('input[name="' + item.value + '_url"]').val();
		if(value.length > 0) {
			if(query.length == 0) {
				query += value + "=%%" + $(item).val() + "%%";
			}else {
				query += "&" + value + "=%%" + $(item).val() + "%%";
			}
		}
	});

	$("#contentLinkShort").val(domain.length > 0 ? domain + (query.length > 0 ? (domain.indexOf('?') == -1 ? "?" : "&") : "") + query : "");
}
