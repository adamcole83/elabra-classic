(function($) {
	
	$.fn.auth = function(opts) {
		
		// declare variables
		var that = $(this),
			options = $.extend({}, $.fn.auth.defaults, opts)
		;
		
		var reset = function() {
			if( $(options.usernameID).val() === '' )
				$(options.usernameID).focus();
			else{
				$(options.passwordID).focus().val('');
			}
		};
		
		// showDialog
		var showDialog = function(msg) {
			$(options.dialogBox)
				.html(	'<' + options.errorElement + ' class="' + options.errorClass + '">' +
							msg +
						'</' + options.errorElement + '>'
				)
				.show()
			;
			that.addClass('errChange');
			reset();
		};
		
		// wiggle animation
		var wiggle = function(elem) {
			var originalLeft = $(elem).position().left;
			$(elem)
				.addClass('errChange')
				.animate({ left: '-=10' }, 50, 'easeOutBounce')
				.animate({ left: '+=20' }, 50, 'easeOutBounce')
				.animate({ left: '-=10' }, 50, 'easeOutBounce')
				.animate({ left: '+=20' }, 50, 'easeOutBounce')
				.animate({ left: 0 }, 50, 'easeOutBounce')
			;
			reset();
		};
				
		// focus username input
		$(options.usernameID).focus();		
				
		// form on submit function
		that.submit(function() {
			// retrieve input values
			var username = that.find(options.usernameID).val(),
				password = that.find(options.passwordID).val()
			;
			
			// validate form
			if( username === '' || password === '' )
			{
				showDialog(options.errorText);
				if( options.wiggleDialog === true ) {
					wiggle(options.dialogBox);
				}
				if( options.wiggleForm === true ) {
					wiggle(that);
				}
			}
			
			// AJAX call
			else
			{
				$(options.dialogBox).hide();
				
				var data = that.serializeArray();
				
				if(options.bypass !== false)
				{
					data.push({name: 'bypass', value: options.bypass});
				}
				
				$.ajax({
					type	: 'POST',
					url		: options.xhrLDAP,
					dataType: 'json',
					data	: $.param(data),
					success	: function(response) {

						// if user not found throw error
						if( response === null )
						{
							showDialog('Username/password combination failed.');
							if( options.wiggleDialog === true ) {
								wiggle(options.dialogBox);
							}
							if( options.wiggleForm === true ) {
								wiggle(that);
							}
						}
						
						// if error returned, throw it
						else if( typeof response.error !== 'undefined' )
						{
							showDialog('Error: '+response.error);
						}
						
						// welcome user and redirect
						else
						{
							that.fadeOut(500, function() {
								// set identity
								var firstName = response.first_name,
									lastName = response.last_name
								;
								that.empty();
								$(	'<' + options.welcomeElement + ' class="' + options.welcomeClass + '">' +
										'Welcome ' + firstName + ' ' + lastName +
										'<span>Redirecting...<span>' +
									'</' + options.welcomeElement + '>' 
								).appendTo(that);
								that.fadeIn(500);
								setTimeout(function() {
									window.location = options.redirect;
								}, 1000);
							}).removeClass('errChange');
						}
					}
				});
			}
			
			return false;
		});
		
		that.find(options.forgotAnchor).bind({
			click: function() {
				var msg = options.useLDAP === true ? options.passwdLdap : options.passwdDb;
				
				$(options.bubbleDrop)
					.stop(true)
					.html( '<span>' + msg + '</span>' )
					.slideDown(400, 'easeOutElastic')
					.delay(5000)
					.slideUp(400, 'easeInElastic')
				;
			}
		});
		
	};
	
	// plugin defaults, see Documentation
	$.fn.auth.defaults = {
		ldap			: true,
		xhrLDAP			: '/admin/includes/xhr/login.php',
		redirect		: 'index.php',
		usernameID		: '#username',
		passwordID		: '#password',
		useLDAP			: true,
		bypass			: false,
		
		dialogBox		: '#dialog',
		dialogTop		: 40,
		dialogLeft		: '+=0',
		errorClass		: 'error',
		errorElement	: 'p',
		errorText		: 'Authentication required.',
		
		welcomeElement	: 'p',
		welcomeClass	: 'welcome',
		
		boxReturnLeft	: 0,
		wiggleDialog	: true,
		wiggleForm		: true,
		
		forgotAnchor	: '#retrievepwd',
		bubbleDrop		: '#bubbledrop',
		
		passwdLdap		: 'Please contact the University of Missouri <a href="http://www.umsystem.edu/ums/departments/is/helpdesks.shtml">help desk</a> for password retrieval.',
		passwdDb		: 'Please contact the <a href="mailto:jenkinsac@health.missouri.edu">Office of Communications</a> for password retrieval.'
	};
	
})( jQuery );


/*
Documentation:
----------------------------

Options:
	ldap: (boolean) default: true
		Disables (false) or enables (true) ldap authentication. Manual user entry required if disabled.
	
	xhr: (string) default: 'xhr/usersearch.php'
		Relative location of PHP/AJAX server-side script for AJAX calls.
	
	redirect: (string) default: 'index.php'
		Redirect url after successful authentication. Relative to login file.
	
	usernameID: (element) default: '#username'
		The ID of the username text field
	
	passwordID: (element) default: '#password'
		The ID of the password text field
	
	dialogBox: (element) default: '#dialog'
		The div ID for the dialog box placed in html.  Used to produce error messages.
	
	errorClass: (string) default: 'error'
		The class name for the error element placed in the dialog box.
	
	errorElement: (string) default: 'p'
		The element you want to wrap the error text in.
	
	errorText: (string) default: 'Authentication required.'
		The text to be displayed if user does not input required fields.
	
	dialogTop: (int) default: 40
		Allows you to set the animation distance for the dialog box
	
	welcomeElement: (element) default: 'p'
		The element you want to wrap the welcome message in
	
	welcomeClass: (string) default: 'welcome'
		The class name for the welcome element.
	

*/