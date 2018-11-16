// FN Custom show error message for select2
function selec2Validate(ele) {
	var parEle = $(ele).closest('.justify-content-start');
	if ($(ele).hasClass('error')) {
		var errorEle = $('label.error', parEle);
		parEle.append(errorEle);
		$('.select2-selection--multiple, .select2-selection--single', parEle).addClass('error');
	} else {
		$('label.error', parEle).remove();
		$('.select2-selection--multiple, .select2-selection--single', parEle).removeClass('error');
	}
}

jQuery(function($) {
	// Handle change data  of all element field of form input
	$('input[name="name"]').keyup(function(){
		$('input[name="name"]').removeClass('error');
		$('#name-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="email"]').keyup(function(){
		$('input[name="email"]').removeClass('error');
		$('#email-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="password"]').keyup(function(){
		$('input[name="password"]').removeClass('error');
		$('#password-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="password_confirmation"]').keyup(function(){
		$('input[name="password_confirmation"]').removeClass('error');
		$('#password_confirmation-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="default_price_sms"]').keyup(function(){
		$('input[name="default_price_sms"]').removeClass('error');
		$('#default_price_sms-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="is_tracking_link"]').change(function(){
		if ($('input[name="is_tracking_link"]').is(":checked")) {
			$('input[name="is_tracking_link"]').val(1);
		} else {
			$('input[name="is_tracking_link"]').val(0);
		}
		$('input[name="is_tracking_link"]').removeClass('error');
		$('#is_tracking_link-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="encrypted"]').change(function(){
		if ($('input[name="encrypted"]').is(":checked")) {
			$('input[name="encrypted"]').val(1);
		} else {
			$('input[name="encrypted"]').val(0);
		}
		$('input[name="encrypted"]').removeClass('error');
		$('#encrypted-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="country"]').change(function(){
		$('select[name="country"]').removeClass('error');
		$('#country-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="reader_id"]').change(function(){
		$('select[name="reader_id"]').removeClass('error');
		$('#reader_id-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="time_zone"]').change(function(){
		$('select[name="timezone"]').removeClass('error');
		$('#timezone-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="currency"]').change(function(){
		$('select[name="currency"]').removeClass('error');
		$('#currency-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="billing_type"]').change(function(){
		$('select[name="billing_type"]').removeClass('error');
		$('#billing_type-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('#sender').select2().on('change', function(){
		$('select[name="sender"]').removeClass('error');
		$('#sender-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	
	$('.select-sender').select2({
		tags: true
	});
	
	// Get all sender option value select when form loaded
	var sender_list_option = [];
	$('#sender > option').each(function() {
		sender_list_option.push(this.value);
	});

	$('.select-sender').on('select2:select', function(e) {
		var data = e.params.data;
		
		if(data.id.length > 11) {
			sender_list_option.unshift(data.id);
			var dataOption = $('#sender > option:checked');
			$('#sender > option:checked').each(function(key, item) {
				if(item.innerText == data.id) {
					item.remove();
				}
			});
			$(".send-select").append('<label id="sender-error" class="error" for="sender">Please enter no more than 11 characters!</label>');
			return;
		}else if (sender_list_option.length >= 0 && $.inArray(data.id, sender_list_option) == -1) {
			// call ajax update
			$.ajax({
				type : "POST",
				url : window.link.api_add_sender,
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				data : { 
					new_sender : data.id,
				},
				success : function(success) {
					if (success["status"]) {
						sender_list_option.unshift(success["sender_name"]);
						var sender_checked_option = [];
						$('#sender > option:checked').each(function() {
							sender_checked_option.push(this.value);
						});
						$('.select-sender').empty();
						$('.select-sender').select2({
							tags : true
						});
						$.each(sender_list_option, function(index,
								value) {
							if($.inArray(value, sender_checked_option) !== -1) {
								var newOption = new Option(value, value, false, true);
							} else {
								var newOption = new Option(value, value, false, false);
							}
							$('.select-sender').append(newOption).trigger('change');
						});
					}
					return;
				},
				fail : function(error) {
					return;
				}
			});
		}
		
		$(".send-select").remove("sender-error");
	});

	$('.select2-custom').on('change', function() {
		if ($(this).val()) {
			$(this).removeClass('error');
			selec2Validate($(this));
		}
	});
	
	// Prevent multiple submit account form
	$('#accountForm').on('submit', function(){
		var el = $('#addClient');
        el.prop('disabled', true);
	});
	
	// Handle call modal add credit
	$('#add_credit').on('click', function() {
		$('#modalAddCredit').modal({
			backdrop: "static",
			show: true
		});
	});
	
	// Handle call modal increase credit limit
	$('#increase_credit_limit').on('click', function() {
		$('#modalIncreaseCreditLimit').modal({
			backdrop: "static",
			show: true
		});
	});
	// Handle call modal descrease credit limit
	$('#descrease_credit_limit').on('click', function() {
		$('#modalDescreaseCreditLimit').modal({
			backdrop: "static",
			show: true
		});
	});

	$('#withdraw_credit').on('click', function() {
		$('#modalWithdrawCredit').modal({
			backdrop: "static",
			show: true
		});
	});
	
	// click button submit form
	$("#addClient").on("click", function() {
		// submit form
		$("#accountForm").submit();
		$('.select2-custom').each(function() {
			selec2Validate($(this));
		});
	});
	
	$('#btnModalClose').on('click', function() {
		$('#modalAddCredit').modal('hide');
	});
	$('#btnModalWithdrawClose').on('click', function() {
		$('#modalWithdrawCredit').modal('hide');
	});
	$('#btnModalIncreaseClose').on('click', function() {
		$('#modalIncreaseCreditLimit').modal('hide');
	});
	$('#btnModalDescreaseClose').on('click', function() {
		$('#modalDescreaseCreditLimit').modal('hide');
	});

	$('#btnModalSave').on('click', function() {
		var valid = $("#addCreditForm").valid();
		if(valid == true) {
			var credit = $("input[name='credit']").val();
			var description = $("input[name='description']").val();		
			var invalid = $("input[name='credit']").attr('aria-invalid');
			if (window.link.client_id.trim().length == 0) {
				window.location.href = window.link.create_client_url
				return false;
			}
			
			if (credit.length == 0 || invalid == 'true') {
				$("input[name='credit']").valid();
				$("#btnModalSave").attr("disabled", false);
			} else {
				$("#btnModalSave").attr("disabled", true);
				callAddOrWithdrawAjax(window.link.api_add_credit, credit, description);
			}
		}
	});
	
	$('#btnModalWithdrawSave').on('click', function() {
		var credit = $("input[name='withdraw_credit']").val();
		var description = $("input[name='withdraw_description']").val();
		var invalid = $("input[name='withdraw_credit']").attr('aria-invalid');
		if (window.link.client_id.trim().length == 0) {
			window.location.href = window.link.create_client_url
			return false;
		}
		
		if (credit.length == 0 || invalid == 'true') {
			$("input[name='withdraw_credit']").valid();
			$("#btnModalWithdrawSave").attr("disabled", false);
		} else {
			$("#btnModalWithdrawSave").attr("disabled", true);
			callAddOrWithdrawAjax(window.link.api_withdraw_credit, credit, description);
		}
	});
	
	$('#btnModalIncreaseSave').on('click', function() {
		var credit = $("input[name='increaseNumberLimit']").val();
		var description = $("input[name='increaseNumberLimitDescription']").val();		
		var invalid = $("input[name='increaseNumberLimit']").attr('aria-invalid');
		if (window.link.client_id.trim().length == 0) {
			window.location.href = window.link.create_client_url
			return false;
		}
		
		if (credit.length == 0 || invalid == 'true') {
			$("input[name='increaseNumberLimit']").valid();
			$("#btnModalIncreaseSave").attr("disabled", false);
		} else {
			$("#btnModalIncreaseSave").attr("disabled", true);
			callAddOrWithdrawAjax(window.link.api_increase_credit_limit, credit, description);
		}
	});
	
	$('#btnModalDescreaseSave').on('click', function() {
		var credit = $("input[name='descreaseNumberLimit']").val();
		var description = $("input[name='descreaseNumberLimitDescription']").val();		
		var invalid = $("input[name='descreaseNumberLimit']").attr('aria-invalid');
		if (window.link.client_id.trim().length == 0) {
			window.location.href = window.link.create_client_url
			return false;
		}
		
		if (credit.length == 0 || invalid == 'true') {
			$("input[name='descreaseNumberLimit']").valid();
			$("#btnModalDescreaseSave").attr("disabled", false);
		} else {
			$("#btnModalDescreaseSave").attr("disabled", true);
			callAddOrWithdrawAjax(window.link.api_descrease_credit_limit, credit, description);
		}
	});
	
	// check username already exist
	$("#username").on('blur', function(e) {
		var value = e.target.value;
		if(value.length >= 8) {
			$.ajax({
				type: "POST",
				url: window.link.api_check_username,
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					"username": value
				},
				success: function(res) {
					$("#inputUsername > label").remove();
				},
				error: function(err) {
					$("#inputUsername").append('<label id="username-error" class="error" for="username">' + err.responseJSON + '</label>');
				}
			});
		}
	});
	
	function callAddOrWithdrawAjax(api_url, credit, description) {
		$.ajax({
			type : "POST",
			url : api_url,
			headers : {
				'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
			},
			data : {
				client_id : window.link.client_id,
				credit: credit,
				description: description
			},
			success : function(success) {
				if (success["status"]) {
					location.reload();
				} else {
					
				}
				return;
			},
			fail : function(error) {
				return;
			}
		});
	}
});