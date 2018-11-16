$('document').ready(function() {
	jQuery.validator.addMethod("hourMinute", function(value, element) {
		  return this.optional(element) || /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value);
	}, "Please enter a valid time!");
	
	jQuery.validator.addMethod("emailRegex", function(value, element) {
		  return this.optional(element) || /[a-z0-9._%+-]+[^\.]@[a-z0-9.-]+\.[a-z]{2,4}$/.test(value);
	}, "Please enter a valid email address!");
	
	jQuery.validator.addMethod("emailMultipleRegex", function(value, element) {
		var arrEmail = value.split(";");
		for(var i = 0; i < arrEmail.length; i++) {
			var string = arrEmail[i];
			if(string.length > 0 && !/[a-z0-9._%+-]+[^\.]@[a-z0-9.-]+\.[a-z]{2,4}$/.test(string)) {
				return false;
			}
		}
		
		return true;
	}, "Please enter a valid email address!");
	
	jQuery.validator.addMethod("username", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9\-\_\@]+$/.test(value);
	}, "Please enter a valid username!");
	
	jQuery.validator.addMethod("passwordRegex", function(value, element) {
		  return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_=\[\]{};':"\\|,.<>\/?+-])[0-9a-zA-Z!@#$%^&*()_=\[\]{};':"\\|,.<>\/?+-]{8,}$/.test(value);
	}, "Password must be at least 8 characters in length containing Uppercase, Lowercase, Numerics & Symbols.(E.g. Aa@123456)");
	
    // validate register form on keyup and submit
    $("#registerForm").validate();

    // validate form add subscriber
    $('#subscriberForm').validate();

    // validate create new subscriber list
    $('#newSubscriberListForm').validate();
    
    // validate form upload csv
    $("#uploadCSVForm").validate();
    
    $("#copyPasteForm").validate();
    
    $("#addCustomFieldForm").validate();
    
    $("#addLinkForm").validate();
    
    // Init validation form
    $("#accountForm").validate();
    $("#templateForm").validate();
    
    $("#withdrawCreditForm").validate();
    $("#addCreditForm").validate();
    $("#increaseCreditForm").validate();
    $("#descreaseCreditForm").validate();
    $("#resetPassword").validate();
    $("#formNotificationSetting").validate();
    $("#unsubscribeForm").validate();
});