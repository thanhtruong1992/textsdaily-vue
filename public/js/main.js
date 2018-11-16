$('document').ready(function() {
	$.fn.showAlertLogout();
	
	$('#btnCheckAll').click(function() {
		$(':checkbox.frmCheck').prop('checked', true);
	});
	$('#btnCheckNone').click(function() {
		$(':checkbox.frmCheck').prop('checked', false);
	});

    $('#frmCheckAll').click(function () {
        $(':checkbox.frmCheck').prop('checked', this.checked);
    });
    
    $('#frmAllField').click(function () {
        $(':checkbox.frmField').prop('checked', this.checked);
    });
    
    $('#frmAllRule').click(function () {
        $(':checkbox.frmRule').prop('checked', this.checked);
    });
    
    $(document).ready(function() {
        $('.select-multiple').select2();
    });

    var options = {
        "backdrop" : "static",
        "show":true
    };

    $('#addGroup').on('click', function () {
        $('#addGroupModal').modal(options);
    });

    $('#selectGroup').on('click', function () {
        $('#selectGroupModal').modal(options);
    });

    $('#addUrl').on('click', function () {
        $('#addUrlModal').modal(options);
    });
    
    var allowTimes = [];
    for(i = 0; i <= 23; i++) {
        var hour = i.toString();
        hour = hour.length > 1 ? hour : '0' + hour;
        for(j = 0; j < 60; j+=5) {
            var minute = j.toString();
            minute = minute.length > 1 ? minute : '0' + minute;
            allowTimes.push(hour + ':' + minute);
        }
    }

    $("#inputDate").datetimepicker({
        minDate: new Date(),
        dayOfWeekStart: 1,
        timepicker: true,
        allowTimes: allowTimes
    });
    
    $(".delete-custom").click(function() {
    	var title = $(this).attr("title");
    	var content = $(this).attr("content");
    	modalCustom(title, content, function (save) {
    	}, function (cancel) {
    	});
    });
	
	
	// select field of add subscriber
	var $selectFields = $('.select-field');
	$selectFields.on('change', function() {
		  $('option').prop('disabled', false); //reset all the disabled options on every change event
		  $selectFields.each(function() { //loop through all the select elements
		    var val = this.value;
		    if(!!val) {
		    	$selectFields.not(this).find('option').filter(function() { //filter option elements having value as selected option
				      return this.value === val;
			    }).prop('disabled', true); //disable those option elements
		    }
		  });
	}).change();
});

function insertGroup(event) {
    event.preventDefault();
    var frmCheck = $(".frmCheck");
    var string = "";
    for(var i = 0; i < frmCheck.length; i++) {
        var value = frmCheck[i];
        if(value.defaultValue != 'checkAll' && !!value.checked) {
            string += '<option value="' + value.defaultValue +'">' + value.name +'</option>';
        }
    }

    $("#valueGroup").append(string);
    $('#selectGroupModal').modal('hide');
}

function removeGroup() {
    var val = $("#valueGroup option:selected").val();
    $("#valueGroup option[value='" + val + "']").remove();
}

// count string input message campaign
function calculateMessage(event) {
    var language = $("input[name=language]:checked").val();
    //var regex = /%%[^\s]+%%[\S?]/g;
    var regex = /%%[^\%%]+%%\s|%%[^\%%]+%%/g
    var message = $('#message').val();
    var value = message.replace(regex, '');
    var sms = 0;
    
    // detect personalize
    if(isPersonalize(message)) {
    	$("#isPersonalize").val("true");
    }else {
    	$("#isPersonalize").val("false");
    }

    // detect ASCII
    if(!isASCII(value)) {
		language = "UNICODE";
		if(event && event.type && event.type != 'change') {
			document.getElementById('unicode').checked = true;
		}
    }else {
		document.getElementById('ascii').checked = true;
	}

    switch (language) {
        case "ASCII":
        	var lengSMS = value.length;
        	sms = Math.ceil(lengSMS / (lengSMS > 160 ? 153 : 160));
            break;

        case "UNICODE":
        	var lengSMS = value.length;
            sms = Math.ceil(lengSMS / (lengSMS > 70 ? 67 : 70));
            break;
    }
    
    $("#lengthMessage").html(value.length + " Char(s)");
    $("#totalMessage").html(sms + " SMS");
    $("#totalMessage").attr("total", sms);
}

// detect string UNICODE of input message campaign
function isUNICODE(str) {
    return /^[\x00-\xFF]*$/.test(str);
}

// detect stringASCII of input message campaign
function isASCII(str) {
    return /^[\x00-\x7F]*$/.test(str);
}

// open modal add custom field
function openAddCustomFieldModal(event, key) {
	var options = {
        "backdrop" : "static",
        "show":true
    };
	$("#keySelect").val(key);
    $('#addCustomFieldModal').modal(options);
}

// add custom field
function addCustomField(event) {
	event.preventDefault();
	var key = event.target.key.value;
	
	var data = {
		field: event.target.field.value,
		list_id: event.target.list_id.value
	};
	var host = window.location.origin;
	
	$.ajax({
		type: "POST",
		url: host + "/admin/apis/custom-fields/add",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
	    data: data,
	    success: function(res) {
	    	var data = res.data;
	    	// show notification
	    	$.fn.showNotification('success', {content: res.message});
	    	// apent option to select field
	    	var $selectFields = $('.select-field');
	    	$selectFields.each(function() {
	    		$(this).append(new Option(data.field_name, "custom_field_" + data.id));
	    	});
	    	$("#select-" + key).val("custom_field_" + data.id).trigger('change');
	    	$('#addCustomFieldModal').modal('hide');
	    },
	    error: function(err) {
	    	$.fn.showNotification('error', {content: err.responseJSON.message});
	    }
	});
	
}

// export sunscirber
function exportSubscriners(list_id) {
	var url = window.location.origin + "/admin/apis/subscribers/" + list_id + "/export?enscrypted=false&filter=" + JSON.stringify({}) + "&flagFilter=";
	window.open(url, '_blank');
}

$.fn.shortLink = function(data, success, error) {
	var url = window.location.origin + "/admin/apis/campaigns/short-link";
	$.ajax({
		type: "POST",
		url: url,
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
		data: data,
		success: function(res) {
			$.fn.showNotification("success", {content: res.message});
			success({
				short_link: res.short_link,
				campaign_link_id: res.campaign_link_id
			});
		},
		error: function(err) {
			$.fn.showNotification("error", {content: err.responseText});
			error(false);
		}
	});
}

function getNowWithTimezone(offset) {

    // create Date object for current location
    var d = new Date();

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));

    // return time as a string
    return nd.toLocaleString();
}

$.fn.compareDateTime = function(dateTime, timezone) {
	// get now datetime of timezone
	var now = new Date().toLocaleString("en-US", {timeZone: timezone});
	
	// format datetime
	now = new Date(Date.parse(now));
	var date = new Date(Date.parse(dateTime));
	
	return date > now;
}


$.fn.summaryCampaign = function(data, success, error) {
	var url = window.location.origin + "/admin/apis/campaigns/summary";
	$.ajax({
		type: "GET",
		url: url,
		data: data,
		success: function(res) {
			success(res);
		},
		error: function(err) {
			$.fn.showNotification('error', {content: err.responseJSON.message});
		}
	});
}

$.fn.openModalSummary = function(data, done, change) {
	var options = {
	        "backdrop" : "static",
	        "show":true
    };
	$("#campaign_name").html(data.name);
	$("#to").html(data.to);
	$("#sender").html(data.sender);
	$("#message_text").text(data.message).html();
	$("#schedule").html(data.schedule != 'time' ? data.schedule : data.send_time + " " + data.send_timezone);
	$("#number_of_subscriber").html(data.totalSubscriber);
	$("#duplicated_number").html(data.totalDuplicate);
	$("#number_of_sms").html(data.totalSMS);
	$("#estimated_cost").html(data.estimated_cost);
	$('#summaryCampaignModal').modal(options);
	
	$( "#summaryCampaignModal" ).on('shown.bs.modal', function(e){
		// show data table camppaign summary
		var tableCampaignSummary = $('#tableCampaignSummary').DataTable( {
			bInfo: false,
	        processing: false,
	        serverSide: false,
	        searching: false,
	        ordering: false,
	        lengthChange: false,
	        bDestroy: false,
	        paging: false,
	        scrollY: "200px",
	        scrollCollapse: true,
	        data: data.data,
	        columns: [
				{ data: "country"},
		        { data: "network", },
		        { data: "price", mRender: function (data, type, row) {
		        	return parseFloat(data).toFixed(2) + " " + currency;
		        }},
		        { data: "TotalRecipients"},
		        { data: "TotalSMS"},
			],
	        /*fnRowCallback: function(nRow, aData, iDisplayIndex){
	        	$("td:first", nRow).html(iDisplayIndex +1);
	        	return nRow;
	    	},*/
	    });
		// desotry table 
		tableCampaignSummary.destroy();
	});
	
	var currency = data.currency;
	
    $("#createCampaign").off("click").click(function() {
    	return done(true);
    });
    $("#changeCampaign").off("click").on("click",function() {
    	$('#summaryCampaignModal').modal('hide');
    	return change(true);
    });
    
    return;
}

$.fn.modalConfirmCampaign = function(data, contentConfirm, accept, cancel) {
	if(contentConfirm == ""){
		return accept(data);
	}
	
	var options = {
	        "backdrop" : "static",
	        "show":true
    };
	$("#contentConfirm").html(contentConfirm);
	$('#modalConfirmCampaign').modal(options);
	
	/*if(data == false) {
		$("#acceptConfirm").css("display", "none");
	}else {
		$("#acceptConfirm").css("display", "block");
		// accept modal confirm
	    $("#acceptConfirm").off("click").on("click",function() {
	    	$('#modalConfirmCampaign').modal('hide');
	    	return accept(data);
	    });
	}*/
	
	// accept modal confirm
    $("#acceptConfirm").off("click").on("click",function() {
    	$('#modalConfirmCampaign').modal('hide');
    	return accept(data);
    });
	
	// cancel modal confirm
    $("#cancelConfirm").off("click").click(function() {
    	$('#modalConfirmCampaign').modal('hide');
    	return cancel();
    });
}

$.fn.validationDatimeCampaign = function() {
	var dateTime = $("#inputDate").val();
	var timezone = $("#send_timezone").val();
	var result = $.fn.compareDateTime(dateTime, timezone);
	if(!result) {
		$("#validDate").css("display", "block");
		$("input[name='send_time']").addClass('error');
		
		return true;
	}else {
		$("#validDate").css("display", "none");
		$("input[name='send_time']").removeClass('error');
		
		return false;
	}
}

$.fn.formatDateTime = function(datetime, showTime) {
	showTime = showTime == null ? true : showTime;
	if(datetime != null) {
		var mydate = new Date(datetime.replace(/\-/g, "/"));
		var minute = mydate.getMinutes().toString();
		minute = minute.length == 1 ? "0" + minute : minute;
		var hours = mydate.getHours().toString();
		hours = hours.length == 1 ? "0" + hours : hours;
		var month = ["Jan", "Feb", "Mar", "Apr", "May", "June",
			"July", "Aug", "Sep", "Oct", "Nov", "Dec"][mydate.getMonth()];
		var str = mydate.getDate() + '-' + month + '-' + mydate.getFullYear();
		if( showTime ) {
			str  += " " + hours + ":" +minute;
		}
		
		return str;
	}
	
	return datetime;
}

$.fn.showAlertLogout = function() {
	let countTime = 0;
	let handle = setInterval(function() {
		countTime += 1;
		if(countTime == 55*60){
			alert("Session timeout within next 3 minutes, if you do not do anything, our system will redirect to the login page.");
		}else if (countTime > 60*60) {
			alert("You have been logout. Please login again.");
			location.reload();
		}
	}, 1000);
}

// function detect personalize
function isPersonalize(message) {
	return /%%[^\s]+%%/.test(message);
}

// fn get cursor position in textarea
$.fn.getCursorPosition = function() {
    var el = $(this).get(0);
    var pos = 0;
    if('selectionStart' in el) {
        pos = el.selectionStart;
    } else if('selection' in document) {
        el.focus();
        var Sel = document.selection.createRange();
        var SelLength = document.selection.createRange().text.length;
        Sel.moveStart('character', -el.value.length);
        pos = Sel.text.length - SelLength;
    }
    return pos;
}
