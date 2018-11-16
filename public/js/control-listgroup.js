(function($) {
	/**
	 * JQuery Plugin Custom List Group
	 */
	$.fn.customListGroup = function(options) {
		//
		var self = $(this);
		var listGroupELe = self.find('.listControl');
		var searchEle = self.find('.searchControl');

		// Default settings
		var settings = $.extend({
			data : {},
			onClick: function() {}
		}, options);
		
		// Search data
		self.off('keyup', searchEle).on('keyup', searchEle, function() {
			$('li', listGroupELe).filter(function(){
				var searchKeywords = searchEle.val().toUpperCase();
				if (
						$(this).text().toUpperCase().indexOf(searchKeywords) > -1
						|| $(this).data('value').toUpperCase().indexOf(searchKeywords) > -1
				) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});
		
		// Trigger button search
		self.off('click', '.btnSearch').on('click', '.btnSearch', function() {
			searchEle.trigger('keyup');
		});

		// Add click event for li element
		self.off('click', 'li').on('click', 'li', function() {
			$('li', listGroupELe).removeClass('active');
			$(this).addClass('active');
			settings.onClick( $(this) );
		});
		
		// Load data for list group
		$('li', listGroupELe).remove();
		$.each( settings.data, function( key, value ) {
			var newItem = $('<li class="list-group-item">').data('value', key).html( value );
			listGroupELe.append( newItem );
		} );
		
		//
		return this;
	};
})(jQuery);