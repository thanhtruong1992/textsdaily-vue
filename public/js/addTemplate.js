// FN Custom show error message for select2
function selec2Validate(ele) {
	var parEle = $(ele).closest('.justify-content-start');
	if ($(ele).hasClass('error')) {
		var errorEle = $('label.error', parEle);
		parEle.append(errorEle);
		$('.select2-selection--multiple, .select2-selection--single', parEle)
				.addClass('error');
	} else {
		$('label.error', parEle).remove();
		$('.select2-selection--multiple, .select2-selection--single', parEle)
				.removeClass('error');
	}
}

(function ($, undefined) {
    $.fn.getCursorPosition = function () {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        } else if ('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
})(jQuery);

jQuery(function($) {

	// Trigger count message
	$('#message').keyup();
	
	$('#reset-template').on('click', function() {
		$('#templateForm').trigger('reset');
		$('#message').keyup();
	});

	$('#create-template').on('click', function() {
		var validator = $('#templateForm').validate();
		$('#templateForm').submit();
	});

	// Load personalize modal
	$('#loadPersonalize').on('click', function() {
		loadPersonalizeData();
		$('#frmModalCheckAll').prop('checked', false);
	});

	// Personalize datatable
	var arrField = [ "id", "check" ];
	var tablePersonalize = null;
	function loadPersonalizeData() {
		tablePersonalize = $('#table-personalize')
				.DataTable(
						{
							bInfo : false,
							processing : true,
							serverSide : true,
							searching : false,
							ordering : true,
							lengthChange : false,
							bDestroy : true,
							paging : false,
							ajax : {
								url : window.link.api_get_personalize
										+ "?list_id=" + "",
								type : "GET",
								draw : 1,
								data : function(d) {
									var order = d.order[0];
									var filter = {};
									var flagFilter = $(".flag-filter").val();
									var obj = {
										field : arrField[order.column],
										orderBy : order.dir,
										search : d.search.value,
										page : (d.start / 10) + 1,
									};

									return obj;
								},
								error : function() {
									return {
										recordsTotal : 0,
										recordsFiltered : 0,
										data : []
									}
								}
							},
							columns : [
									{
										data : "field_name",
										mRender : function(data, type, row) {
											return row.field_name + (row.name ? ' (' + row.name + ')' : '');
										}
									},
									{
										data : "",
										mRender : function(data, type, row) {
											var field_name = "";
											switch (row.field_name) {
												case "Phone":
													field_name = "phone";
													break;
												case "First name":
													field_name = "firstname";
													break;
												case "Last name":
													field_name = "lastname";
													break;
												default:
													field_name = row.field_name
													break;
													
											}
											return '<label class="custom-control custom-checkbox">'
													+ '<input type="checkbox" class="custom-control-input frmModalCheck" value="'
													+ field_name
													+ '" name="Mark"/>'
													+ '<span class="custom-control-indicator"></span>'
													+ '</label>';
										}
									}, ],
							aaSorting : [ [ 0, "desc" ] ], // default sort
							columnDefs : [ {
								"name" : "browser",
								"targets" : 1
							},
							// hidden order column
							{
								orderable : false,
								targets : [ 0, 1 ]
							},
							// hidden search column
							{
								searchable : false,
								targets : []
							},
							// hidden column
							{
								visible : false,
								targets : []
							}, ],
							language : {
								paginate : {
									next : 'Next', // or '→'
									previous : 'Prev' // or '←'
								}
							}
						});
	}

	// reload data table
	function reload(table) {
		table.ajax.reload(null, true);
	}

	$('#frmModalCheckAll').on('click', function() {
		$(':checkbox.frmModalCheck').prop('checked', this.checked);
	});

	$('#insertPersonalize').on('click', function() {
		var listChecked = $('.frmModalCheck:checked'); // list checked
		var textInput = ""
		var message = $('#message').val();
		$.each(listChecked, function(index, value) {
			if ($(this).val() == "checkAll") {
				return true;
			}
			var textInsert = " %%" + $(this).val() + "%%";
			textInput += textInsert;
		});
		var position = $("#message").getCursorPosition();
		var newMessage = message.substr(0, position) + textInput + message.substr(position);
		$('#message').val(newMessage);
		$('#personalizeModal').modal('hide');
		$('#message').keyup();
	});

	// Load template modal
	$('#loadTemplate').on('click', function() {
		$('#templateModal').modal({
			"backdrop" : "static",
			"show" : true
		});
		loadTemplateData();
	});

	// Personalize datatable
	var arrField2 = [ "name", "message"];
	var tableTemplate = null;
	function loadTemplateData() {
		tableTemplate = $('#table-template')
				.DataTable(
						{
							bInfo : false,
							processing : true,
							serverSide : true,
							searching : false,
							ordering : true,
							lengthChange : false,
							bDestroy : true,
							paging : false,
							ajax : {
								url : window.link.api_get_template,
								type : "GET",
								draw : 1,
								data : function(d) {
									var order = d.order[0];
									var filter = {};
									var flagFilter = $(".flag-filter").val();
									var obj = {
										field : arrField2[order.column],
										orderBy : order.dir,
										search : d.search.value,
										page : (d.start / 10) + 1,
									};

									return obj;
								},
								error : function() {
									return {
										recordsTotal : 0,
										recordsFiltered : 0,
										data : []
									}
								}
							},
							columns : [
									{
										data : "name"
									},
									{
										data : "message"
									},
									{
										data : "",
										mRender : function(data, type, row) {
											return '<label class="custom-control custom-radio">'
													+ '<input type="radio" class="custom-control-input" value="'
													+ row.message
													+ '" name="template"/>'
													+ '<span class="custom-control-indicator"></span>'
													+ '</label>';
										}
									}, ],
							aaSorting : [ [ 0, "desc" ] ], // default sort
							columnDefs : [ {
								"name" : "browser",
								"targets" : 1
							},
							// hidden order column
							{
								orderable : false,
								targets : [ 2 ]
							},
							// hidden search column
							{
								searchable : false,
								targets : []
							},
							// hidden column
							{
								visible : false,
								targets : []
							}, ],
							language : {
								paginate : {
									next : 'Next', // or '→'
									previous : 'Prev' // or '←'
								}
							}
						});
	}
	
	$('#useTemplate').on('click', function() {
		if($('#table-template').DataTable().data().count() <= 0) {
			$('#templateModal').modal('hide');
			return false;
		}
		var message = $('input[name="template"]:checked').val();
		if (message == null) {
			$('#templateModal').modal('hide');
			return false;
		}
		$('#message').val(message);
		$('#message').keyup();
		$('#templateModal').modal('hide');
	});
});
