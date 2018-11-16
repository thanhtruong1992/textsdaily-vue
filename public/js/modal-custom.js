$.fn.modalCustom = function(title, content, save, cancel) {
	var options = {
        "backdrop" : "static",
        "show":true
    };

	$("#titleModalConfirm").html(title);
	$("#contentModalConfirm").html(content);

    $('#modalConfirm').modal(options);
    
    $("#btnModalSave").off("click").click(function() {
    	$('#modalConfirm').modal('hide');
    	return save(true);
    });
    $("#btnModalClose").off("click").on("click",function() {
    	$('#modalConfirm').modal('hide');
    	return cancel(false);
    });
    
    return;
}