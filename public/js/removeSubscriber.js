$(document).ready(function() {
	$("#select-subscriber").on("change", function(e) {
		var value = e.target.value;
		$("#checkbox-remove").css("display", "block");
		$("#checkbox-remove input").prop('checked', false);
		$("#text-phone").css("display", "none");
		$("#text-phone textarea").val("");
		
		if(value == "SUPPERSSED") {
			$("#checkbox-remove").css("display", "none");
		} else if(value == "MOBILE") {
			$("#text-phone").css("display", "block");
		}
	});
});