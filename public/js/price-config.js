jQuery(function($) {
	var countryNetworkData = JSON.parse(window.client.priceConfiguration.allData);
	var allCountries = JSON.parse(window.client.allCountries);
	
	showDataCountry(countryNetworkData, allCountries);
	
	$('input[name="show_country"]').on("click", function(e) {
		var value = e.target.value;
		if(value == 'enabled') {
			countryNetworkData = JSON.parse(window.client.priceConfiguration.dataEnabled);
		}else if(value == 'disabled') {
			countryNetworkData = JSON.parse(window.client.priceConfiguration.dataDisabled);
		}else {
			countryNetworkData = JSON.parse(window.client.priceConfiguration.allData);
		}
		showDataCountry(countryNetworkData, allCountries);
	});
	
	function showDataCountry (countryNetworkData, allCountries) {
		// Get countries of service provider data
		var listCountries = {};
		$.each( Object.keys(countryNetworkData), function( key, value ) {
			var countryCode = value.toUpperCase();
			listCountries[value] = allCountries[ countryCode ];
		});
		
		//
		var countryListEle = $('#countryList');
		var networkListEle = $('#networkList');
		var priceCountryEle = $('#priceCountry');
		var priceNetworkGroupEle = $('#priceNetworkGroup');
		var priceNetworkEle = $('#priceNetwork');
		var disableEle = $('#disablePriceCheckbox');
		
		function callAjaxGetPriceConfiguration() {
			//
			var countryVal = $('li.active', countryListEle).data('value');
			var networkVal = $('li.active', networkListEle).text();
			//
			$.ajax({
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				url: window.client.priceConfiguration.ajaxGetPriceConfigurationUrl,
				type: 'POST',
				data: {
					'client_id': window.client.clientID,
					'country': countryVal,
					'network': networkVal
				},
				dataType: 'json',
				success: function( result ) {
					if( result.status  ) {
						if( result.data ) {
							if ( networkVal == '' ) {
								disableEle.prop('checked', !!parseInt(result.data.disabled)).trigger('change');
								priceCountryEle.val(result.data.price);
							} else {
								priceNetworkEle.val(result.data.price);
							}
						} else {
							if ( networkVal == '' ) {
								priceCountryEle.val('');
								disableEle.prop('checked', true).trigger('change');
							} else {
								priceNetworkEle.val('');
							}
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
		
		function callAjaxSavePriceConfiguration( countryVal, networkVal, priceVal, disableVal = 0 ) {
			$.ajax({
				headers : {
					'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
				},
				url: window.client.priceConfiguration.ajaxSavePriceConfigurationUrl,
				type: 'POST',
				data: {
					'client_id': window.client.clientID,
					'country': countryVal,
					'network': networkVal,
					'price': priceVal,
					'disable': disableVal
				},
				dataType: 'json',
				success: function( result ) {
					// update data
					var dataEnabled = JSON.parse(window.client.priceConfiguration.dataEnabled);
					var dataDisabled = JSON.parse(window.client.priceConfiguration.dataDisabled);
					var temp = "";
					if($('input[name="show_country"]:checked').val() != 'all') {
						$("#countryList .list-group-item.active").remove();
						var listLi = $("#countryList .list-group-item");
						// active li first
						if(listLi.length > 0) {
							$(listLi[0]).trigger('click');
						}
					}
					if(disableVal == 1) {
						temp = dataEnabled[countryVal];
						delete dataEnabled[countryVal];
						dataDisabled[countryVal] = temp;
						
					}else {
						temp = dataDisabled[countryVal];
						delete dataDisabled[countryVal];
						dataEnabled[countryVal] = temp;
					}
					window.client.priceConfiguration.dataEnabled = JSON.stringify(dataEnabled);
					window.client.priceConfiguration.dataDisabled = JSON.stringify(dataDisabled);
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
		}
		
		//
		countryListEle.customListGroup({
			data: listCountries,
			onClick: function( element ) {
				var curValue = element.data('value');
				var networkData = countryNetworkData[curValue];
				// Reset data price
				priceNetworkEle.val('');
				//
				var listNetworks = {};
				$.each( networkData, function( key, value ) {
					listNetworks[key] = value;
				});
				networkListEle.customListGroup({
					data: listNetworks,
					onClick: function( element ) {
						callAjaxGetPriceConfiguration();
						priceNetworkGroupEle.removeClass('disableContent noselect');
					}
				});
				//
				callAjaxGetPriceConfiguration();
				priceNetworkGroupEle.addClass('disableContent noselect');
			}
		});
		countryListEle.find('li:first-child').trigger('click');
		
		// Save price for network
		$('#btnNetworkSave').off('click').on('click', function() {
			var countryVal = $('li.active', countryListEle).data('value');
			var networkVal = $('li.active', networkListEle).text();
			var priceVal = priceNetworkEle.val();
			// Validation
			if ( countryVal == '' ) {
				$.fn.showNotification('error', {'content': window.client.priceConfiguration.requiredCountryError});
				return false;
			}
			if ( networkVal == '' ) {
				$.fn.showNotification('error', {'content': window.client.priceConfiguration.requiredNetworkError});
				return false;
			}
			if ( priceVal == '' ) {
				$.fn.showNotification('error', {'content': window.client.priceConfiguration.requiredPriceError});
				return false;
			}
			// Save
			callAjaxSavePriceConfiguration( countryVal, networkVal, priceVal );
		});
		
		// Save price for country
		$('#btnCountrySave').off('click').on('click', function() {
			var countryVal = $('li.active', countryListEle).data('value');
			var priceVal = priceCountryEle.val();
			var disableVal = 0;
			if ( disableEle.is(':checked') ) {
				disableVal = 1;
			}
			// Validation
			if ( countryVal == '' ) {
				$.fn.showNotification('error', {'content': window.client.priceConfiguration.requiredCountryError});
				return false;
			}
			// Save
			callAjaxSavePriceConfiguration( countryVal, null, priceVal, disableVal );
		});
		
		// Disable price
		disableEle.off('change').on('change', function() {
			if( $(this).is(':checked') ) {
				networkListEle.addClass('disableContent noselect');
				networkListEle.find('.list-group-item.active').removeClass('active');
				priceNetworkGroupEle.addClass('disableContent noselect');
				priceCountryEle.attr('disabled', true).val(0);
			} else {
				networkListEle.removeClass('disableContent noselect');
				priceCountryEle.attr('disabled', false).val('');
			}
		});
		
		//
		priceNetworkGroupEle.addClass('disableContent noselect');
		
		//
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
					url: window.client.priceConfiguration.ajaxUploadPriceConfigurationUrl,
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
	}
});
