$(document).ready(function() {
	$( document ).ajaxStart(function() {
		$("#loader").css("display", "block");
	});
	
	$( document ).ajaxComplete(function() {
		$("#loader").css("display", "none");
	});
});