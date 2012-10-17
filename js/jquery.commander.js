(function($) {
	
	$.fn.commander = function(options) {
		
		options = $.extend({
			url				: 'includes/xhr/commandcenter.php',
			highlightColor	: '#EBC248'
		}, options);
				
		var that = $(this),
			maxHeight = 0,
			maxHeader = 0
			timeout = 0;
		;
				
		// update function		
		var update = function(init) {
			// show loading
			$('#loader').fadeIn();

			// query function
			var oDepartments = $.parseJSON($.ajax({
				type: "POST",
				url: options.url,
				dataType: 'json',
				async: false
			}).responseText);
			
			that.children().each(function() {
				var that = $(this),
					oDepo = oDepartments[that.attr('id').split('-')[2]],
					comm = {
						title:	that.find('.commander-title'),
						dir:	that.find('.commander-dir'),
						files:	that.find('.commander-files'),
						size:	that.find('.commander-size'),
						pages:	that.find('.commander-pages'),
						draft:	that.find('.commander-draft'),
						news:	that.find('.commander-news'),
						cal:	that.find('.commander-cal')
					}
				;
				
				for( var elem in comm ) {
					if( comm[elem].text() !== oDepo[elem] ) {
						var originalColor = comm[elem].css('color');
						comm[elem].text( oDepo[elem] );
						
						if(init !== true)
							comm[elem].highlightFade({ start: options.highlightColor, speed: 1000, attr: 'color' });
					}
				}
				
				// set max container height
				maxHeight = Math.max( that.height(), maxHeight );
			}).height(maxHeight);
			
			// set timeout
			timeout++;		
			if(timeout === 90) {
				var answer = confirm('The dashboard is about to timeout, would you like to continue updating?');
				if(answer) {
					timeout = 0;
				}
				else{
					clearInterval(autoDashboard);
				}
			}

			
			// hide loading
			setTimeout('$("#loader").fadeOut()', 2000);
			that.addClass('running');
			
		};
		
		update(true);
		autoDashboard = setInterval(update, 10000);
		
		// set max header height
		that.find('h4').each(function() {
			maxHeader = Math.max( $(this).height(), maxHeader );
		}).height(maxHeader);
		
	};
		
})(jQuery);