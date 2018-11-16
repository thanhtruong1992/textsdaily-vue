jQuery(function($) {
	$('input[name="name"]').change(function(){
		$('input[name="name"]').removeClass('error');
		$('#name-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="email"]').change(function(){
		$('input[name="email"]').removeClass('error');
		$('#email-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="password"]').change(function(){
		$('input[name="password"]').removeClass('error');
		$('#password-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="host_name"]').change(function(){
		$('input[name="host_name"]').removeClass('error');
		$('#host_name-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('input[name="avatar"]').change(function(){
		$('select[name="avatar"]').removeClass('error');
		$('#avatar-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="country"]').change(function(){
		$('select[name="country"]').removeClass('error');
		$('#country-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="time_zone"]').change(function(){
		$('select[name="timezone"]').removeClass('error');
		$('#timezone-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});
	$('select[name="default_service_provider"]').change(function(){
		$('select[name="default_service_provider"]').removeClass('error');
		$('#default_service_provider-error').empty();
		$('#addClient').removeAttr('disabled'); // enable save button
	});

	// Prevent multiple submit account form
	$('#accountForm').on('submit', function(){
		var el = $('#addClient');
        el.prop('disabled', true);
	});
	
	// Prevent multiple submit account form
	$('#whiteLabelForm').on('submit', function(){
		var el = $('#addClient');
		el.prop('disabled', true);
	});
});
