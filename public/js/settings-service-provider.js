jQuery(function($) {
	var serviceProviderData = window.settings.serviceProvider.allData;
	var allCountries = window.settings.allCountries;
	
	// Get countries of service provider data
	var listCountries = {};
	$.each( Object.keys(serviceProviderData), function( key, value ) {
		var countryCode = value.toUpperCase();
		listCountries[value] = allCountries[ countryCode ];
	});
	
	//
	var countryListEle = $('#countryList');
	var networkListEle = $('#networkList');
	var serviceProviderControlEle = $('#serviceProviderControl');
	//
	countryListEle.customListGroup({
		data: listCountries,
		onClick: function( element ) {
			var curValue = element.data('value');
			var networkData = serviceProviderData[curValue];
			// Reset data preffered service provider
			serviceProviderControlEle.find('option[value=""]').prop('selected', true);
			//
			var listNetworks = {};
			$.each( networkData, function( key, value ) {
				listNetworks[key] = value;
			});
			networkListEle.customListGroup({
				data: listNetworks,
				onClick: function( element ) {
					var countryVal = $('li.active', countryListEle).data('value');
					var networkVal = $('li.active', networkListEle).text();
					$.ajax({
						headers : {
							'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
						},
						url: window.settings.serviceProvider.ajaxGetServiceProviderUrl,
						type: 'POST',
						data: {
							'country': countryVal,
							'network': networkVal
						},
						dataType: 'json',
						success: function( result ) {
							if( result.status  ) {
								if( result.data && result.data.service_provider ) {
									serviceProviderControlEle.find('option[value="' + result.data.service_provider + '"]').prop('selected', true);
								} else {
									serviceProviderControlEle.find('option[value=""]').prop('selected', true);
								}
							} else {
								$.fn.showNotification('error', {'content': result.msg});
							}
						},
						fail : function( error ) {
							console.log(error);
						}
					});
				}
			});
		}
	});
	countryListEle.find('li:first-child').trigger('click');
	
	// Save data
	$('#btnSave').off('click').on('click', function() {
		var countryVal = $('li.active', countryListEle).data('value');
		var networkVal = $('li.active', networkListEle).text();
		var serviceProviderVal = serviceProviderControlEle.val();
		// Validation
		if ( countryVal == '' ) {
			$.fn.showNotification('error', {'content': window.settings.serviceProvider.requiredCountryError});
			return false;
		}
		if ( networkVal == '' ) {
			$.fn.showNotification('error', {'content': window.settings.serviceProvider.requiredNetworkError});
			return false;
		}
		if ( serviceProviderVal == '' ) {
			$.fn.showNotification('error', {'content': window.settings.serviceProvider.requiredServiceProviderError});
			return false;
		}
		// Save
		$.ajax({
			headers : {
				'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
			},
			url: window.settings.serviceProvider.ajaxSaveServiceProviderUrl,
			type: 'POST',
			data: {
				'country': countryVal,
				'network': networkVal,
				'service_provider': serviceProviderVal
			},
			dataType: 'json',
			success: function( result ) {
				if( result.status  ) {
					$.fn.showNotification('success', {'content': result.msg});
				} else {
					$.fn.showNotification('error', {'content': result.msg});
				}
			},
			fail : function( error ) {
				console.log(error);
			}
		});
	});
	
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
				url: window.settings.serviceProvider.ajaxUploadServiceProviderUrl,
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
});
