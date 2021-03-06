$(function () {
	// show notifcation
	var alert = document.getElementById("alert");
	if(alert.childElementCount > 0) {
		for(var i = 0; i < alert.childElementCount; i++) {
			var notify = alert.children[0];
			$.fn.showNotification(notify.className, {content: notify.textContent});
		}
	}
});

$.fn.showNotification = function(key, data, timeOut) {
	timeOut = !!timeOut ? timeOut : 3000;
	data.content = data.content ? data.content : "";
	toastr.options.closeButton = true;
	toastr.options.timeOut = timeOut;
	toastr.options.extendedTimeOut = timeOut;
	toastr.options.positionClass = 'mid-center';
	toastr.options.preventDuplicates = true;
	
	switch(key) {
		case "success":
			toastr.success(data.content, "Done!");
			break;
		
		case "info": 
			toastr.info(data.content);
			break;
		
		case "warring":
			toastr.warning(data.content, data.title ? data.title : "Warring!");
			break;
			
		case "error":
			toastr.error(data.content, data.title ? data.title : "Error!");
			break;
	}
	
	return true;
}
