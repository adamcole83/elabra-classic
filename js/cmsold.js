var emailRegex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
var sContent = "layouts/";
var autoDashboard;
var usersOnline;
var oSubMenu = {};
oSubMenu.Page		= {	"Add a New Page"			: "page.add" };
                        
oSubMenu.Department = { "Add a New Department"		: "department.add" };
                        
oSubMenu.User		= { "Add User"					: "user.add" };

var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0,
			c1 = 0,
			c2 = 0,
			c = 0
		;
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
};

$(document).ready(function() {
	var aCurrentView = g_sCurrentView.split(".");
	var codeMirrorEditor;
	$("#menu").children("ul:eq(0)").children("li").addClass("off");
	$("#tab" + aCurrentView[0]).removeClass("off");
	
	//ChangeDepartment(g_sDepartment, true);
	//DrawPage(g_sCurrentView);
	
	$('#reload').click(function(){
		location.reload(true);
	});
	$('#logout').click(function(){
		UserCount(0);
		$(window.location).attr('href', 'login.php?do=logout');
	})
	$(window).bind({
		load: function() {
			UserCount(1);
		},
		unload: function() {
			UserCount(0);
		}
	});
});


// DEPRECATED
function ChangeTab(oSpan, sPage, callback) {
	$('#loader').show();
	$("#menu").children("ul:eq(0)").children("li").addClass("off");
	$(oSpan).parent().removeClass("off");
	//DrawPage(sPage, callback);
}

function ChangeDepartment(sDept, init)
{
	$('#toolDepartment span').each(function(){
		$(this).removeClass('bold');
	});
	$.ajax({
		type: "POST",
		url: "includes/access.php",
		data: "session=department:"+sDept,
		success: function(data) {
			$('span#dept').html(data);
			$('span#dept-select-'+sDept).addClass('bold');
			if(!init)
				window.location = 'pages.php';
		}
	});
}

function ShowTools(sID, auto) {
	if(auto !== true)
		auto = false;
		
	if (arguments.length > 0) {
		sToolID = sID;
	}
	// loop through items
	if($("#tools .tool:visible").size() > 0) {
		$("#tools .tool:visible").each(function() {
			if($(this).attr("id") == sToolID) {
				$(this).stop().slideUp(350, function() {
				});
			} else {
				$(this).stop().slideUp(350, ShowTools);
			}
		});
	} else {
		if(sToolID == "toolMessages" && auto) {
			$("#" + sToolID).stop().slideDown(350).delay(2000).slideUp(350);
		} else { 
			$("#" + sToolID).stop().slideDown(350);
		}
	}
}

// DEPRECATED
/*
function DrawPage(sPage, callback) {
	
	var aPage = sPage.split(".");
	$('#content').slideUp(g_iFadeSpeed, function(){ $(this).html(''); });
	
	// if leaving Page Edit and user has not saved, ask to save
	if( $('#content').data('savefile') === true ) {
		var answer = confirm('Do you want to save the changes you made?');
		if( answer ) {
			$('#content').data('savefile',false);
			eval( $('button.save').attr('onclick') );
			return false;
		}else{
			$('#content').data('savefile',false);
		}
	};
	
	// clear dashboard auto refresh
	clearInterval(autoDashboard);
	
	$.ajax({
		type: "POST",
		url: "includes/access.php",
		data: "timeout=1",
		success: function(data) {
			if(data == 200)
			{
				$("#content").fadeOut(g_iFadeSpeed, function() {
					g_sCurrentView = sPage;
					
					switch (aPage[0]) {
						case "page":
							PageLayout_Page(aPage[1], aPage[2], callback);
							break;
						case "media":
							PageLayout_Media(aPage[1], aPage[2], callback);
							break;
						case "department":
							PageLayout_Department(aPage[1], aPage[2], callback);
							break;
						case "user":
							PageLayout_User(aPage[1], aPage[2], callback);
							break;
						case "group":
							PageLayout_Group(aPage[1], aPage[2], callback);
							break;
						case "permission":
							PageLayout_Permission(aPage[1], aPage[2], callback);
							break;
						case "dashboard":
							PageLayout_Dashboard(callback);
							break;
						case "deploy":
							PageLayout_DeployApplication(callback);
							break;
						case "pack":
							PageLayout_PackageApplication(aPage[1], callback);
							break;
						case "help":
							PageLayout_Help(callback);
							break;
						case "news":
							PageLayout_NewsEvents(callback);
					}
				});
			}
			else
			{
				UserCount(0);
				$(window.location).attr('href', 'login.php?do=logout');
			}
		}
	});
	$('#content').data('page', sPage);
	$('#loader').show().delay(1000).hide('normal');
}

function PageLayout_Page(sPage, id, callback)
{	
	id = (id) ? "?id=" + id : ''; 
	$.ajax({
		url: sContent + sPage + "_page.php" + id,
		cache: false,
		success: function(data){
			$("#content").html(data);
			if(sPage !== 'select'){
				$('#content input:eq(0)').focus();
			}
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, function() {
		if(typeof callback === 'function') callback();
		$('#content tr').bind({
			mouseenter : function() {
				$(this).find('td .action .tool').fadeIn(g_iFadeSpeed);
			},
			mouseleave : function() {
				$(this).find('.tool').hide();
			}
		});
	});
}

function PageLayout_Department(sPage, id, callback)
{	
	id = (id) ? "?id=" + id : '';
	$.ajax({
		url: sContent + sPage + "_department.php" + id,
		cache: false,
		success: function(data){
			$("#content").html(data);
			if(sPage !== 'select'){
				$('#content input:eq(0)').focus();
			}
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_User(sPage, id, callback)
{
	id = (id) ? "?id=" + id : '';
	$.ajax({
		url: sContent + sPage + "_user.php" + id,
		cache: false,
		success: function(data){
			$("#content").html(data);
			if(sPage !== 'select'){
				$('#content input:eq(0)').focus();
			}
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}


function PageLayout_Group(sPage, id, callback)
{
	id = (id) ? "?id=" + id : '';
	$.ajax({
		url: sContent + sPage + "_group.php" + id,
		cache: false,
		success: function(data){
			$("#content").html(data);
			if(sPage !== 'select'){
				$('#content input:eq(0)').focus();
			}
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_Permission(sPage, id, callback)
{
	id = (id) ? "?id=" + id : '';
	$.ajax({
		url: sContent + sPage + "_permission.php" + id,
		cache: false,
		success: function(data){
			$("#content").html(data);
			if(sPage !== 'select'){
				$('#content input:eq(0)').focus();
			}
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_Media(sPage, id, callback)
{
	id = (id) ? "?id=" + id : '';
	$.ajax({
		url: sContent + sPage + "_media.php" + id,
		cache: false,
		success: function(html){
			$("#content").html(html);
			$('#content input:eq(0)').focus();
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_Dashboard(callback)
{
	$.ajax({
		url: sContent + "dashboard.php",
		cache: false,
		success: function(html){
			$("#content").html(html);
			$('#content input:eq(0)').focus();
		}
	});
	$("#content").fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_DeployApplication(callback)
{
	$.ajax({
		url: sContent + "deploy_application.php",
		cache: false,
		success: function(html){
			$("#content").html(html);
		}
	});
	$('#content').fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_PackageApplication(sPost, callback)
{
	if(!sPost)
		sPost = '';
	
	$.ajax({
		url: sContent + "package_application.php"+sPost,
		cache: false,
		success: function(html){
			$("#content").html(html);
			$('#content input:eq(0)').focus();
		}
	});
	$('#content').fadeIn(g_iFadeSpeed, callback);
}

function PageLayout_Help(callback)
{
	$.ajax({
		url: sContent + "help.php",
		cache: false,
		success: function(html){
			$("#content").html(html);
			$('#content input:eq(0)').focus();
		}
	});
	$('#content').fadeIn(g_iFadeSpeed,callback);
}

function PageLayout_NewsEvents(callback)
{
	$.ajax({
		url: sContent + "news.php",
		cache: false,
		success: function(html){
			$("#content").html(html);
			$('#content input:eq(0)').focus();
		}
	});
	$('#content').fadeIn(g_iFadeSpeed, callback);
}
*/


/* Form Handling */
function SubmitForm(sAction, bSkip)
{
	if( bSkip !== true ) {
		bSkip === false;
	}
	var aPage = sAction.split(".");
	switch(aPage[1]) {
		case "save":
			$('#content').data('savefile',false);
			if(ValidateForm(aPage[0]))
				AJAXProcess(sAction);
			break;
		case "savepublish":
			$('#content').data('savefile',false);
			$('#content').data('savepublish', true);
			if(ValidateForm(aPage[0]))
				AJAXProcess(aPage[0]+".save."+aPage[2]);
			break;
		case "delete":
			if( bSkip !== true ) {
				var answer = confirm("Are you sure you want to delete this "+aPage[0]+"?");
			}else{
				answer = bSkip;
			}
			if( answer ) {
				AJAXProcess(sAction);
			}
			break;
		default:
			AJAXProcess(sAction);
			break;
	};
}

function ValidateForm(sPage, sAction)
{
	$('.error').hide();
	
	switch (sPage) {
		case "page":
			return FormValidation_Page();
			break;
		case "department":
			return FormValidation_Department();
			break;
		case "user":
			return FormValidation_User(sAction);
			break;
		case "group":
			return FormValidation_Group();
			break;
		case "permission":
			return FormValidation_Permission();
			break;
		case "package":
			return FormValidation_Package();
			break;
		case "media":
			return FormValidation_Media();
			break;
		default:
			return false;
	};
}

function AJAXProcess(sAction)
{
	var aPage = sAction.split(".");
	var posturl = 'includes/process.php';
	var postData = 'html';
	var formData = aPage.join('-');
	var formBody;
	
	switch (aPage[1]) {
		case "save":
			switch (aPage[0]) {
				case "page":
					$.post(
						posturl, {
							form: 			formData,
							title: 			$('input#title').val(),
							description: 	$('textarea#description').val(),
							url: 			$('input#url').val(),
							department: 	$('select#department option:selected').val(),
							sidebar:		$('select#sidebar option:selected').val(),
							index:			$('select#index option:selected').val(),
							user:			$('input#user').val(),
							body:			codeMirrorEditor.getValue()
						}, 
						function(data) {
							AJAXProcess_Alert(sAction, data);
						},
						postData
					);
					break;
				case "media":
					$.post(
						posturl, {
							form:			formData,
							title:			$('input#title').val(),
							caption:		$('input#caption').val(),
							description:	$('textarea#description').val()
						},
						function(data) {
							AJAXProcess_Alert(sAction, data);
						},
						postData
					);
					break;
				case "department":
					$.post(
						posturl, {
							form:			formData,
							name:			$('input#name').val(),
							code:			$('input#code').val(),
							subdir:			$('input#subdirectory').val()
						},
						function(data) {
							AJAXProcess_Alert(sAction, data);	
						},
						postData
					);
					break;
				case "user":
					$.post(
						posturl, {
							form:			formData,
							username:		$('input#username').val(),
							first_name:		$('input#first_name').val(),
							last_name:		$('input#last_name').val(),
							email:			$('input#email').val(),
							phone_number:	$('input#phone').val(),
							dept:			$('select#department option:selected').val(),
							group:			$('select#group option:selected').val(),
							passwd:			$('input#password').val(),
							sendto:			$('input:radio[name=sendto]:checked').val()
						},
						function(data) {
							AJAXProcess_Alert(sAction, data);	
						},
						postData
					);
					break;
				case "group":
					var perms = [];
					$('input[name=permission]:checked').each(function() {
						perms.push($(this).val());
					});
					$.post(
						posturl, {
							form:			formData,
							name:			$('input#name').val(),
							permissions:	perms.join(':')
						},
						function(data) {
							AJAXProcess_Alert(sAction, data);	
						},
						postData
					);
					break;
				case "permission":
					var groups = [];
					$('input[name=group]:checked').each(function() {
						groups.push($(this).val());
					});
					$.post(
						posturl, {
							form:			formData,
							name:			$('input#name').val(),
							groups:			groups.join(':')
						},
						function(data) {
							AJAXProcess_Alert(sAction, data);	
						},
						postData
					);
					break;
			}
			break;
		default:
			$.post( 
				posturl,
				{ form: formData },
				function(data) {
					AJAXProcess_Alert(sAction, data);	
				},
				postData
			);
			break;
	};
}

function AJAXProcess_Alert(sPage, code)
{
	var aPage = sPage.split('.');
	var redirect = '';
	switch (code) {
		case '200':
			
			if( $('#content').data('savepublish') === true ) {
				$('#content').data('savepublish',false);
				SubmitForm(aPage[0]+".publish."+aPage[2]);
				return false;
			}
			
			var status = "success";
			var page = aPage[0].charAt(0).toUpperCase() + aPage[0].slice(1);
			var sHTML = page+' has been successfully updated!';
			
			if(aPage[0] === 'permission' || aPage[0] === 'group')
			{
				redirect = "user.select";
			}
			else if( (aPage[0] === 'page' && aPage[1] === 'save' && !aPage[2]) || (aPage[1] == 'save' && aPage[0] !== 'page') )
			{
				redirect = aPage[0]+".select";
			}
			else if( aPage[0] === 'media' )
			{
				redirect = "media.select";
			}
			else
			{
				redirect = $('#content').data('page');
			}
			break;
		
		default:
			var status = "error";
			var sHTML = 'There was a system error during '+aPage.join('-')+'!';
			redirect = $('#content').data('page');
			break;
	};
	ShowAlert(sHTML, status, redirect);
}

function ShowAlert(msg, status, redirect)
{	
	DrawPage(redirect, function() {
		var sHTML = "<p>"+msg+" <span onclick=\"HideAlert()\">(hide)</span></p>";
		$('#alert-box').addClass(status);
		$('#alert-box').html(sHTML);
		$('#alert-box').slideDown(300);
	});
}
function HideAlert()
{
	$('#alert-box').slideUp(300, function() {
		$('#alert-box').removeClass();
		$('#alert-box').html('');
	});
}

function FormValidation_Page()
{
	$('.error').hide();
	
	var title = $('input#title').val();
	if(title == "") {
		$('label#title_error').show();
		$('input#title').focus();
		return false;
	}else if(title.length > 69) {
		$('label#title_error').show();
		$('label#title_error').html('Title cannot be more than 69 characters, you have '+title.length+' characters.');
		$('input#title').focus();
		return false;
	};
	
	var description = $('textarea#description').val();
	if(description.length > 170) {
		$('label#description_error').show();
		$('label#description_error').html('Description cannot be more than 156 characters, you have '+description.length+' characters.');
		$('input#description').focus();
		return false;
	};
	
	var url = $('input#url').val();
	if(url == "") {
		$('label#url_error').show();
		$('input#url').focus();
		return false;
	};
	
	var department = $('select#department option:selected').val();
	if(department == '') {
		$('label#department_error').show();
		$('select#department').focus();
		return false;
	};
		
	var index = $('select#index option:selected').val();
	if(index == '') {
		$('label#index_error').show();
		$('select#index').focus();
		return false;
	};
	
	return true;
}

function FormValidation_Media()
{
	$('.error').hide();
	
	var title = $('input#title').val();
	if(title == '') {
		$('label#title_error').show();
		$('input#title').focus();
		return false;
	}
	
	return true;
}

function FormValidation_Department()
{
	$('.error').hide();
	
	var name = $('input#name').val();
	if(name == '') {
		$('label#name_error').show();
		$('input#name').focus();
		return false;
	};
	
	var code = $('input#code').val();
	if(code == '') {
		$('label#code_error').show();
		$('input#code').focus();
		return false;
	};
	
	var subdir = $('input#subdir').val();
	if(subdir == '') {
		$('label#subdir_error').show();
		$('input#subdir').focus();
		return false;
	};
	
	return true;
}

function FormValidation_User(sAction)
{
	$('.error').hide();
	
	
	var email = $('input#email').val();
	if(email == '') {
		$('label#email_error').show();
		$('input#email').focus();
		return false;
	};
	if(!email.match(emailRegex)) {
		$('label#email_error').show();
		$('label#email_error').html('Invalid email address');
		$('input#email').focus();
		return false;
	};
	
	var username = $('input#username').val();
	if(username == '') {
		$('label#username_error').show();
		$('input#username').focus();
		return false;
	};
	
	var first_name = $('input#first_name').val();
	if(first_name == '') {
		$('label#first_name_error').show();
		$('input#first_name').focus();
		return false;
	};
	
	var last_name = $('input#last_name').val();
	if(last_name == '') {
		$('label#last_name_error').show();
		$('input#last_name').focus();
		return false;
	};
	
	var phone = $('input#phone').val();
	if(phone == '') {
		$('label#phone_error').show();
		$('input#phone').focus();
		return false;
	};
	
	var department = $('select#department option:selected').val();
	if(department == '') {
		$('label#department_error').show();
		$('select#department').focus();
		return false;
	};
	
	var group = $('select#group option:selected').val();
	if(group == '') {
		$('label#group_error').show();
		$('select#group').focus();
		return false;
	};
		
	var passwd = $('input#password').val();
	var passwd2 = $('input#passwordconfirm').val();
	if(passwd != passwd2) {
		$('label#password_error').show();
		$('label#password_error').html("Passwords do not match.");
		$('input#password').focus();
		return false;
	};
	if(passwd == '' && sAction == 'add') {
		$('label#password_error').show();
		$('input#password').focus();
		return false;
	};
	if(passwd != '' && (passwd.length < 6 || passwd.length > 20)) {
		$('label#password_error').show();
		$('label#password_error').html('Password must be between 6 and 20 characters.');
		return false;
	};
	
	var mailto = $('input:radio[name=sendto]:checked').val();
	if(sAction == 'add' && mailto == '') {
		$('label#sendto_error').show();
		return false;
	};
	
	return true;
}

function FormValidation_Group()
{
	$('.error').hide();
	
	var name = $('input#name').val();
	if(name == '') {
		$('label#name_error').show();
		$('input#name').focus();
		return false;
	};
	
	var perms = [];
	$('input[name=permission]:checked').each(function() {
		perms.push($(this).val());
	});
	if(perms.length == 0) {
		$('label#permission_error').show();
		return false;
	};
	
	return true;
}

function FormValidation_Permission()
{
	$('.error').hide();
	
	var name = $('input#name').val();
	if(name == '') {
		$('label#name_error').show();
		$('input#name').focus()
		return false;
	};
	
	return true;
}

function FormValidation_Package()
{
	$('.error').hide();
	
	var name = $('input#name').val();
	if(name == '') {
		$('label#name_error').show();
		$('input#name').focus();
		return false;
	};
	
	var version = $('input#version').val();
	if(version == '') {
		$('label#version_error').show();
		$('input#version').focus();
		return false;
	};
	
	var description = $('textarea#description').val();
	if(description == '') {
		$('label#description_error').show();
		$('textarea#description').focus();
		return false;
	};
	
	var author = $('input#author').val();
	if(author == '') {
		$('label#author_error').show();
		$('input#author').focus();
		return false;
	};
	
	var email = $('input#email').val();
	if(email == '') {
		$('label#email_error').show();
		$('input#email').focus();
		return false;
	};
	if(!email.match(emailRegex)) {
		$('label#email_error').show();
		$('label#email_error').html('Invalid email address');
		$('input#email').focus();
		return false;
	};
	
	var screen = $('input#screen').val();
	if(screen == '') {
		$('label#screen_error').show();
		$('input#screen').focus();
		return false;
	};
	
	var directory = $('input#directory').val();
	if(directory == '') {
		$('label#directory_error').show();
		$('input#directory').focus();
		return false;
	};
	
	return true;
}

function TogglePermission(chk, id)
{
	if(chk.checked == false) {
		var dataString = 'form=group-deletepermission-'+id+'&permission='+chk.value;
	}else{
		var dataString = 'form=group-addpermission-'+id+'&permission='+chk.value;
	};
	
	$.ajax({
		type: "POST",
		url: "includes/process.php",
		data: dataString,
		success: function(data) {
			if(data == 500) {
				var dataString = '<p><strong>[Error: code 500]</strong> There was a system error during '+aPage.join('-')+'!</p>';
				ShowTools('toolMessages');
				$('#alert-msg').html(dataString);
			}
		}
	});
}

function Update(field, sPage)
{
	var aPage = sPage.split(".");
	if(field.value && field.value != aPage[3]) {
		var dataString = 'form='+aPage.join('-')+'&'+aPage[1]+'='+field.value;
		$.ajax({
			type: "POST",
			url: "includes/process.php",
			data: dataString,
			success: function(data) {
				if(data == 500) {
					var dataString = '<p><strong>[Error: code 500]</strong> There was a system error during '+aPage.join('-')+'!</p>';
					ShowTools('toolMessages');
					$('#alert-msg').html(dataString);
				}
			}
		});
	};
}

function UserHasAccess(sMenu)
{
	aMenu = sMenu.split('.');	
	if(aMenu[1] == 'select')
		aMenu[1] = 'view';
	
	if(aMenu[1] !== 'publish')
		aMenu[0] += 's';
	
	var dataString = 'action='+aMenu[1]+'_'+aMenu[0];
	var data = $.ajax({
		type: "POST",
		url: "includes/access.php",
		data: dataString,
		async: false
	}).responseText;
	
	if(data == 200)
		return true;
	else
		return false;
}

function Pack()
{
	if(ValidateForm('package')){
		var posturl = 'includes/package.php';
		var postData = 'html';
		
		$.post(
			posturl, {
				name:			$('input#name').val(),
				version:		$('input#version').val(),
				description:	$('textarea#description').val(),
				author:			$('input#author').val(),
				email:			$('input#email').val(),
				screenshot:		$('input#screen').val(),
				directory:		$('input#directory').val()
			},
			function(data) {
				//AJAXProcess_Alert('pack.packed', data);
				alert(data);
			},
			postData
		);
	};
}

function Upload(oUpload)
{
	var url 	= 'http://medicine.missouri.edu/admin/includes/upload.php';
	var width	= 300;
	var height 	= 100;
	var left 	= (screen.availWidth - width)/2;
	var top 	= (screen.availHeight - height)/2;
	var params	= 'width='+width+', height='+height;
	//params	+= 	'top='+top+', left='+left;
	params	+= 	', directories=no';
	params	+=	', location=no';
	params	+=	', menubar=no';
	params	+=	', resizable=no';
	params 	+=	', scrollbars=no';
	params	+=	', status=no';
	params	+=	', toolbar=no';
	
	uploader = window.open(url, 'uploader', params);
	if(window.focus) { uploader.focus(); };
	return false;
}


function NewPackage(sID)
{
	$('#version_select').show();
	
	var aVersion = $('span#version-'+sID).text().replace(/\n/, '').split(".");
	var seq1 = parseInt(aVersion[0]);
	var seq2 = parseInt(aVersion[1]);
	var seq3 = parseInt(aVersion[2]);
	var version;
	
	$('.vbtn').click(function() {
		$('#version_select').hide();
		var type = $(this).text().toLowerCase();
		switch (type)
		{
			case 'major':
				seq1 += 1;
				version = seq1+".0.0";
				break;
			case 'minor':
				seq2 += 1;
				version = seq1+"."+seq2+".0";
				break;
			case 'debugged':
				seq3 += 1;
				version = seq1+"."+seq2+"."+seq3;
		};
		
		var title		= $('h3#title-'+sID).text().replace(/\n/, '');
		var author 		= $('a#author-'+sID).text().replace(/\n/, '');
		var email 		= $('a#author-'+sID).attr('href').replace(/mailto:/, '').replace(/\n/, '');
		var description	= $('p#description-'+sID).text().replace(/\n/, '');
		
		var dataString = Base64.encode("name="+title+"&description="+description+"&author="+author+"&email="+email+"&version="+version);
		var deploy = $('#tabpack').children('span');
		
		ChangeTab(deploy,'pack.?q='+dataString);
	});
	
	return false;
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    };
    return false;
}
function LoadEditor()
{
	codeMirrorEditor = CodeMirror.fromTextArea(document.getElementById("code"), {
		mode: "text/html",
		lineNumbers: true,
		onKeyEvent: function(i, e) {
			if( (e.ctrlKey == true && e.keyCode == 70) && e.type == 'keydown' ) {
				e.stop();
				return toggleFullscreenEditing();
			};
			
			if( e.keyCode == 27 && e.type == 'keydown' && $('.CodeMirror-scroll').hasClass('fullscreen') ) {
				var tb = $('#toolbar');
				if( tb.is(':visible') ){
					tb.slideUp('normal', function(){
						tb.removeClass('fs-top');
					});
				}else{
					tb
						.addClass('fs-top')
						.children(':last')
						.removeClass('cancel')
						.end()
						.slideDown()
					;
				};
			};
			
			if($('#content').data('savefile') !== true) {
				$('#content').data('savefile', true);
				$('.save').removeClass('cancel');
			};
		},
		onCursorActivity: function() {
			codeMirrorEditor.setLineClass(hlLine, null);
			hlLine = codeMirrorEditor.setLineClass(codeMirrorEditor.getCursor().line, "activeline");
		}
	});
	var hlLine = codeMirrorEditor.setLineClass(0, "activeline");	
}

var lastPos = null, lastQuery = null, marked = [];
function unmark() {
	for (var i = 0; i < marked.length; ++i) marked[i]();
	marked.length = 0;
}

function search() {
	unmark();                     
	var text = document.getElementById("query").value;
	if (!text) return;
	for (var cursor = codeMirrorEditor.getSearchCursor(text); cursor.findNext();)
		marked.push(codeMirrorEditor.markText(cursor.from(), cursor.to(), "searched"));
	
	if (lastQuery != text) lastPos = null;
	var cursor = codeMirrorEditor.getSearchCursor(text, lastPos || codeMirrorEditor.getCursor());
	if (!cursor.findNext()) {
		cursor = codeMirrorEditor.getSearchCursor(text);
		if (!cursor.findNext()) return;
	}
	codeMirrorEditor.setSelection(cursor.from(), cursor.to());
	lastQuery = text; lastPos = cursor.to();
}

function replace() {
	unmark();
	var text = document.getElementById("query").value,
		replace = document.getElementById("replace").value;
	if (!text) return;
	for (var cursor = codeMirrorEditor.getSearchCursor(text); cursor.findNext();)
		codeMirrorEditor.replace(replace);
}

function toggleFullscreenEditing() {
	var editorDiv = $('.CodeMirror-scroll');
	if( !editorDiv.hasClass('fullscreen') ) {
		toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() };
		editorDiv.addClass('fullscreen');
		editorDiv.height('100%');
		editorDiv.width('100%');
		codeMirrorEditor.refresh();
	}
	else {
		$('#toolbar').removeClass('fs-top').children(':last').addClass('cancel');
		editorDiv.removeClass('fullscreen');
		editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
		editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
		codeMirrorEditor.refresh();
	};
}


function Toolbar(id, tool) {
	var that = $(id)
		selection = codeMirrorEditor.getSelection()
	;

	function insertText(txt) {
		codeMirrorEditor.replaceSelection(txt);
	};
	
	function Edit(html) {
		if( selection !== '' ) {
			codeMirrorEditor.replaceSelection("<"+html+">"+selection+"</"+html+">");
		}else{
			if( that.hasClass('cancel') ) {
				insertText("<"+html+">");
			}else{
				insertText("</"+html+">");
			}
			that.toggleClass('cancel');
		}
	};
	
	switch( tool )
	{
		case 'bold':
			Edit('strong');
			break;
		
		case 'em':
			Edit('em');
			break;
		
		case 'ul':
			Edit('ul');
			break;
		
		case 'ol':
			Edit('ol');
			break;
		
		case 'li':
			Edit('li');
			break;
		
		case 'link':
			
			break;
		
		case 'img':
			
			break;
		
		case 'undo':
			codeMirrorEditor.undo();
			break;
		
		case 'redo':
			codeMirrorEditor.redo();
			break;
		
		case 'fs':
			toggleFullscreenEditing();
			break;
	};
}


function ListUsers(sAction) {
	var aAction = sAction.split('.');
	$.ajax({
		type: "POST",
		url: "/admin/xhr/listuser.php",
		dataType: "json",
		success: function(data) {
			var html = '';
			for( var i=0;i<data.length;i++ ) {
				html += '<li id="user-'+data[i]['id']+'" onclick="'+aAction[0]+'(\''+aAction[1]+'.'+data[i]['id']+'\')">'+data[i]['fullname']+'<span>'+data[i]['department']+'</span></li>';
			}
			$('<div id="listserv"><ul><li><h2>Select User</h2></li>'+html+'</ul></div>').appendTo('#content');
		}
	});
}

function AssignUser(sAction)
{
	var aAction = sAction.split('.');
	$('#listserv').remove();
	var user = $.parseJSON(User(aAction[1]));
	$.ajax({
		type: "POST",
		url: "/admin/xhr/usermeta.php",
		data: {
			action: 'add',
			user_id: user.id,
			meta_key: 'department',
			meta_value: aAction[0]
		},
		success: function(data) {
			if( data === '200' ) {
				$('<tr id="user-'+user.id+'"><td colspan="2">'+user.username+
					'<button type="button" class="small floatRight" onclick="RelinquishUser(\''+
					sAction+'\')">Relinquish</button></td></tr>').appendTo('.selector');
			}
		}
	});
}

function RelinquishUser(sAction)
{
	var aAction = sAction.split('.');
	var user = $.parseJSON(User(aAction[1]));
	$.ajax({
		type: "POST",
		url: "/admin/xhr/usermeta.php",
		data: {
			action: 'delete',
			user_id: user.id,
			meta_key: 'department',
			meta_value: aAction[0]
		},
		success: function(data) {
			$('#user-'+aAction[1]).remove();
		}
	});
}

function User(id) {
	return $.ajax({
		type: "POST",
		url: "/admin/xhr/listuser.php",
		data: {id:id},
		dataType: "json",
		async: false
	}).responseText;
}

function UserCount(iNum) {
	$.ajax({
		type: "POST",
		url: "/admin/xhr/useravailability.php",
		data: {status:iNum}
	});
}

function SetSearch(keyword)
{
	$('input#search_table').val(keyword).trigger('keyup');
}

function SelectAll(obj)
{
	var checked_status = $(obj).attr('checked');
	$('input[name="select"]').each(function() {
		if(checked_status === 'checked') {
			$(this).attr('checked', true);
		}else{
			$(this).attr('checked', false);
		}
	});
}

function RemoveSelected(sType)
{
	var s = (sType!=='media') ? 's' : '';
	var count = 0;
	if($('input[name="select"]:checked').length > 0) {
		var answer = confirm("Are you sure you want to delete the selected "+sType+s+"? This cannot be undone.");
		if(answer) {
			$('input[name="select"]:checked').each(function() {
				var id = $(this).val().replace('selected-', '');
				$.post('includes/process.php', { form: sType+'-delete-'+id });
				
				capType = sType.charAt(0).toUpperCase() + sType.slice(1);
				ShowAlert(capType+s+" successfuly deleted.", "success", sType+".select");
			});
		}
	}
}