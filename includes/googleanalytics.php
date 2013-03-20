<script type="text/javascript">
	
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-31341113-3']);
	_gaq.push(['_trackPageview']);
	
	// User information
	_gaq.push(['_setCustomVar', 1, 'User ID', <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] :null; ?>, 2]);
	_gaq.push(['_setCustomVar', 2, 'Department ID', <?php echo isset($_SESSION['department']) ? $_SESSION['department'] :null; ?>, 2]);
	
	// Pages Data
	//_gaq.push(['_trackEvent', 'Media', 'Uploaded', 'This is the media title.']);
	
	// Users Data
	
	
	// Media Data
	
	
	// News Data
	
	
	// Help Data
	
	
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	
</script>