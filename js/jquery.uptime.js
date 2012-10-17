(function($) {
	
	$.fn.serverUptime = function(options) {
		
		options = $.extend({
			upSeconds: 0
		}, options);
				
		var that = $(this)
			upSeconds = options.upSeconds
		;
		
		var doUptime = function () {
			var uptimeString = "Server Uptime: ",
				secs = parseInt(upSeconds % 60),
				mins = parseInt(upSeconds / 60 % 60),
				hours = parseInt(upSeconds / 3600 % 24),
				days = parseInt(upSeconds / 86400)
			;
			
			uptimeString += days;
			uptimeString += ((days === 1) ? " day" : " days");

			uptimeString += ((days > 0) ? ", " : "") + hours;
			uptimeString += ((hours === 1) ? " hour" : " hours");

			uptimeString += ((days > 0 || hours > 0) ? ", " : "") + mins;
			uptimeString += ((mins === 1) ? " minute" : " minutes");

			uptimeString += ((days > 0 || hours > 0 || mins > 0) ? ", " : "") + secs;
			uptimeString += ((secs === 1) ? " second" : " seconds");
			
			that.text(uptimeString);
			upSeconds++;
			setTimeout(doUptime, 1000);
		};
		
		doUptime();
	};
		
})(jQuery);