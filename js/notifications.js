// Safari Notifications
var systemTitle = "School of Medicine CMS";

var notify = function(title, options) {

	// Check the notification compatibility
	if (window.Notification) {
		// Log the current permission level
		console.log(Notification.permission);
		// if the user has not been asked to grant or deny notifications from this domain
		if (Notification.permission === 'default') {
			Notification.requestPermission(function() {
				// callback this function once a permission level has been set
				notify(title, options);
			});
		}
		// if the user has granted permission for this domain to send notifications
		else if (Notification.permission === 'granted') {
			var n = new Notification(title, options);
			// remove the noficiation from the Notification Center when it is clicked
			n.onclick = function() {
				this.close();
			};
			// callback function when the notification is closed
			n.onclock = function() {
				console.log('Notification closed');
			}
		}
		// if the user does not want notifications to come from this domain
		else if (Notification.permission === 'denied') {
			// be silent
			return;
		}
	}
};