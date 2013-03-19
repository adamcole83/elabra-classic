// global variables
var gDuration = 400,
	aCurrentView,
	lastPos = null,
	lastQuery = null,
	marked = [],
	xhr_path = 'includes/xhr/',
	regex = {
		email: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
		phone: /^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?/
	},
	xhr = {
		access:		xhr_path + 'access.php',
		process:	xhr_path + 'process.php',
		deploy:		xhr_path + 'deploy.php',
		listuser:	xhr_path + 'listuser.php',
		package:	xhr_path + 'package.php',
		usravail:	xhr_path + 'useravailability.php',
		usrmeta:	xhr_path + 'usermeta.php',
		checkdb:	xhr_path + 'checkdb.php',
		message:	xhr_path + 'message.php',
		savesort:	xhr_path + 'savesort.php',
		meta:		xhr_path + 'meta.php'
	}
;

// document ready
$(function() {
	
	init();
	initforms();
	//initTable();

});

// initializer
function init()
{
	// Set active tab
	aCurrentView = gCurrentView.split(".");
	$('#menu ul li:not("#tab'+aCurrentView[0]+'")').addClass("off");
	$('#clearsearch').bind('click', ClearSearch());
}

function initforms()
{
	// validate input on blur
	var form = document.getElementById('editor');
	if( form ) {
		$(form.elements).each(function() {
			if( $(this).attr('type') !== 'button' ) {
				$(this).bind('blur', function() {
					if( $(this).val() !== '' )
						ValidateForm($(this));
				})
			}
		});
	}
	
	// capitalize shortcode
	$('input#shortcode').keyup(function() {
	    $(this).val($(this).val().toUpperCase());
	});
}

function PulseAnimation(pulse_object, loop, minOpacity, fadeOutDuration, fadeInDuration)
{
	var minOpacity = minOpacity || 0,
		fadeOutDuration = fadeOutDuration || 650,
		fadeInDuration = fadeInDuration || 650
	;
	
	pulse_object.animate({
		opacity: minOpacity
	}, fadeOutDuration, function() {
		pulse_object.animate({
			opacity: 1
		}, fadeInDuration, function() {
			if(loop)
			{
				PulseAnimation(pulse_object, loop, minOpacity, fadeOutDuration, fadeInDuration);
			}
		});
	});
}

function initTable()
{
	$('.scroll .tableFull').each(function() {
		var that = $(this),
			originalHeight = that.height(),
			toHeight = (originalHeight > 195) ? 195 : originalHeight
		;
		that.css({ height: toHeight });
		that.bind('click', function() {
			if( that.height() !== originalHeight ){
				that.animate({height: originalHeight});
			}else{
				that.animate({height: toHeight});
			}
		});
	});
}

function ClipBoard(obj)
{
	var txtArea = document.getElementById('holdtext');
	txtArea.innerHTML = obj.innerHTML;
	var doc = eval("document.readme.holdtext");
	cp = doc.createTextRange();
	doc.focus();
	doc.select();
	cp.execCommand("Copy");
}

// Show Tools
function ShowTools(id)
{
	var sToolID;
	if (arguments.length > 0) {
		sToolID = id;
	}
	if( $('#tools .tool:visible').size() > 0 ) {
		$('#tools .tool:visible').each(function() {
			if( $(this).attr('id') == sToolID ){
				$(this).stop().slideUp(gDuration);
			}else{
				$(this).stop().slideUp(gDuration, ShowTools);
			}
		});
	}else{
		$('#' + sToolID).stop().slideDown(gDuration);
	}
}

// Change Department
function ChangeDepartment(id, clickObject)
{
	$.ajax({
		type: "POST",
		url: "includes/xhr/access.php",
		data: { session: "department:"+id },
		success: function() {
			ShowTools();
			
			if(aCurrentView[1]==='edit') {
				setTimeout('window.location = "page.php"', 450);
			}else{
				setTimeout('window.location.reload()');
			}
		}
	});
}

// Save Entry
function Save(sData, obj)
{
	var aData = sData.split('.');
	if( ValidateForm() === true ) {
		$('#loader').show();
		aData.splice(1,0,'save');
		var formData = $.param({ form: aData.join('-') }) + '&' + $('.editor').serialize();
		$.post(xhr.process, formData, function(code) {
			
			if(inArray('page', aData) && inArray('save', aData))
			{
				if(code.match(/id/i))
				{
					var id = code.replace(/id/i, '');
					window.location = 'page.php?action=edit&id='+id;
				}
			}
			if( code === '200' ) {
				if(inArray('redirect',aData))
				{
					SetMessage(ucfirst(aData[0]) + " was successfully saved.");
					window.history.back();
				}
				else
				{
					$('#loader').hide();
					ShowDialog(ucfirst(aData[0]) + " was successfully saved.");
					var t = setTimeout("HideDialog()",3000);
				}
			}
			else if(code === '505')
			{
				$('#loader').hide();
				ShowDialog('Username exists.', 'error');
			}
			else
			{
				$('#loader').hide();
				ShowDialog('Nothing to save.', 'warning');
			}
		});
	}
}

function SaveSort(obj)
{
	var that = $(obj),
		sort = {}
	;
	for(var i=1; i<that.children().length + 1; i++) {
		var child =  that.children(':eq('+(i-1)+')');
		child.children('span').text(i);
		sort[i] = child.attr('id');
	}
	$.post(xhr.savesort, $.param(sort));
}

function SaveAll(sType)
{
	$('#loader').show();
	$('form.editor').each(function() {
		if( ValidateForm(this) === true ) {
			var id = $(this).attr('id'),
				formData = $.param({ form: sType + '-save-' + id }) + '&' + $(this).serialize();
			;
			$.post(xhr.process, formData);
			setTimeout(function() {
				if( SetMessage("Data was successfully updated.") ) {
					window.location = window.previousPage;
				}
			}, 2000);
		}
	});
}

// Relocate department
function Relocate(id, from)
{
	var to = $('.editor input#subdir').val();
	if(from == to)
	{
		FormShowError('subdir','Set new directory.');
		return false;
	}
	var r = confirm("WARNING: Relocating to '"+to+"' will overwrite any directories that exist by that name.\n\nIt is recommended you backup or rename any existing directories before relocation occurs.\n\nAre you sure you want to continue?");
	if(r == true)
	{
		$('#loader').show();
		var formData = $.param({ form: 'department-relocate-' + id, relocateFrom: from, relocateTo: to });
		$.post(xhr.process, formData, function(code) {
			if(code == 200)
			{
				SetMessage("Department was successfully relocated.");
				location.reload(true);
			}
			else
			{
				$('#loader').hide();
				ShowDialog('ERROR relocating department. Please contact support.', 'error');
			}
		});
	}
	else
	{
		return false;
	}
}

// Publish Entry
function Publish(sData, obj, invokeReturn)
{
	$('#loader').show();
	var aData = sData.split('.');
	aData.splice(1,0,'save');
	var formData = $.param({ form: aData.join('-') }) + '&' + $('.editor').serialize();
	$.post(xhr.process, formData, function(code) {
		if(invokeReturn == true)
		{
			return true;
		}
		$('#loader').hide();
		ShowDialog(ucfirst(aData[0]) + " was successfully saved and published.");
		var t = setTimeout("HideDialog()",3000);
	});
	
}

// Delete Entry
function Purge(sData, obj)
{
	var aData = sData.split('.');
	var r = confirm("Are you sure you want to delete this " + aData[0] + "?");
	if(r == true)
	{
		$('#loader').show();
		aData.splice(1,0,'delete');
		$.post(xhr.process, { form: aData.join('-') }, function(code) {
			if( code === '200' ) {
				if( SetMessage(ucfirst(aData[0]) + " was successfully deleted.") ) {
					if(inArray('redirect',aData))
					{
						window.history.back();
					}
					else
					{
						location.reload(true);
					}
				}
			}else{
				$('#loader').hide();
				ShowDialog('ERROR deleting ' + aData[0] + '. Please contact support.', 'error');
			}
		});
	}
}

// select all checkbox
function SelectAll(obj)
{
	var checked_status = $(obj).attr('checked');
	$('input[name="select"]:not(":disabled")').add('input[name="select_all"]').each(function() {
		if(checked_status === 'checked') {
			$(this).attr('checked', true);
		}else{
			$(this).attr('checked', false);
		}
	});
}

// call the function selected
function ApplySelectedTo(type)
{
	$('#loader').show();
	switch( $('#modifier').val() ) {
		case 'delete':
			RemoveSelected(type);
			break;
		case 'edit':
			ModifySelected(type);
			break;
		case 'publish':
			PublishSelected(type);
			break;
	}
}

// delete items checked
function RemoveSelected(sType)
{
	var s = (sType!=='media') ? 's' : '';
	var count = 0;
	if($('input[name="select"]:checked').length > 0) {
		var answer = confirm("Are you sure you want to delete the selected "+sType+s+"? This cannot be undone.");
		if(answer) {
			$('input[name="select"]:checked').each(function() {
				var id = $(this).val().replace('selected-', '');
				$.post(xhr.process, { form: sType+'-delete-'+id });
			});
			setTimeout(function() {
				if( SetMessage(ucfirst(sType) + s + " successfully removed.") ) {
					window.location.reload();
				}
			}, 2000);
		}
	}
}

function ModifySelected(sType)
{
	var q = []
		count = 0,
		checked = $('input[name="select"]:checked'),
		length = checked.length
	;
	if(length > 0 && length <= 30) {
		$('input[name="select"]:checked').each(function() {
			var id = $(this).val().replace('selected-', '');
			q.push('id[]=' + id);
		});
		window.location = sType + ".php?action=multiedit&" + q.join('&');
	}
	else if(length > 30) {
		alert('Please select 30 or less items to modify.');
	}
}

function PublishSelected(sType)
{
	var count = 0;
	if( $('input[name="select"]:checked').length > 0 ) {
		var answer = confirm("Are you sure you want to publish the selected "+sType+"s?");
		if(answer) {
			$('input[name="select"]:checked').each(function() {
				var id = $(this).val().replace('selected-', '');
				$.post(xhr.process, { form: sType+'-publish-'+id });
			});
			setTimeout(function() {
				if( SetMessage(ucfirst(sType) + s + " successfully published.") ) {
					window.location.reload();
				}
			}, 2000);
		}
	}
}

function AppendMenuItem(dialog,item)
{
	var title = $(dialog).find('#menu-title').val(),
		url = $(dialog).find('#menu-link').val(),
		order = $(item).children('span').text()
	;
	
	$.post(xhr.meta, {
		
		type: 'department',
		action: 'add',
		id: $('#department').val(),
		key: 'menu-item',
		value: order+';'+title+';'+url
		
	}, function() {
		$( item ).html(title + "<span>"+ order +"</span>").removeClass('empty');
		$( dialog ).dialog( "close" );
	});
}

// Form Validation
function ValidateForm(obj)
{
	var form = (typeof obj === 'undefined') ? $('.required') : $(obj),
		result = true
	;
	
	for( var i=0;i<form.length;i++ ) {
		var that = $('#'+form[i].name),
			id = form[i].name,
			value = form[i].value;
		;
		
		if( id === 'permission' ) {
			var perms = [];
			$('input[name=permission]:checked').each(function() {
				perms.push($(this).val());
			});
			$('input[name=permissions]').val(perms.join(':'));
		}
		
		if( id === 'group' ) {
			var groups = [];
			$('input[name=group]:checked').each(function() {
				groups.push($(this).val());
			});
			$('input[name=groups]').val(groups.join(':'));
		}
		
		if( value === '' && that.hasClass('required') ) {
			FormShowError(id, 'Required!');
			result = false;
		}
		
		else if( that.hasClass('required') ) {
			FormShowError(id,'<span>(required)</span>');
		}
		
		if( value !== '' && (that.hasClass('email') && !value.match(regex.email)) ) {
			FormShowError(id, 'Invalid email address.');
			result = false;
		}
		
		if( value !== '' && (that.hasClass('phone') && !value.match(regex.phone)) ) {
			FormShowError(id, 'Invalid phone number.');
			result = false;
		}
		
		if( value !== '' && (that.hasClass('password') && !value.match( $('#passwordconfirm').val())) ) {
			FormShowError(id, 'Passwords do not match');
			result = false;
		}
		
		if( value !== '' && (that.hasClass('password') && (value.length <= 6 || value.length >= 20)) ) {
			FormShowError(id, 'Password must be between 6 and 20 characters.');
			result = false;
		}
		
/*
		if( value !== '' && id === 'username' ) {
			if( User(value) !== 'false' ) {
				FormShowError(id, 'Username exists, try again.');
				result = false;
			}
		}
*/
		
		if( value !== '' && that.hasClass('noduplicate') ) {
			if( SearchForExisting($('#table').val(), value) !== 'false' ) {
				FormShowError(id, 'Entry exists, try again.');
				result = false;
			}
		}
		
	}
	
	return result;
}

function FormShowError(id,msg)
{
	$('label#'+id+'_error').html(msg).addClass('active');
}

// Show dialog box
function ShowDialog(msg,status)
{
	var sHTML = "<p>"+msg+" <span onclick=\"HideDialog()\">(hide)</span></p>";
	$('#alert-box')
		.addClass(status)
		.html(sHTML)
		.slideDown(300)
	;
}

// Hide dialog box
function HideDialog()
{
	$('#alert-box').slideUp(300, function() {
		$(this).removeClass().html('');
	});
}

// Set Session Message
function SetMessage(msg)
{
	var res = $.ajax({
		type: "POST",
		url: xhr.message, 
		data: { message: msg },
		dataType: 'json',
		async: false
	}).responseText;
	return (res === '200') ? true : false;
}

function deserialize(string)
{
	var object = {},
		aString = string.split('&')
	;
	for( var i=0;i<aString.length;i++ ) {
		var aStringSplit = aString[i].split('=');
		object[aStringSplit[0]] = aStringSplit[1];
	}
	return object;
}

function TogglePermission(chk, id)
{
	if(chk.checked == false) {
		var dataString = 'form=group-deletepermission-'+id+'&permission='+chk.value;
	}else{
		var dataString = 'form=group-addpermission-'+id+'&permission='+chk.value;
	};
	
	console.log(dataString);
	
	$.ajax({
		type: "POST",
		url: xhr.process,
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

// text editor
function LoadMCE(saveCallback)
{
	var options = {
		// Location of TinyMCE script
		script_url : 'js/tinymce/tiny_mce.js',
		mode : "exact",
		elements : "body",
		cleanup: false,
		height: "480",
		width: "100%",
		
		// General options
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		pagebreak_separator : "<!--more-->",
		plugins : "lists,spellchecker,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,save,tinyautosave",
		
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,anchor,|,image,media,pagebreak,|,template,|,formatselect,forecolor,|,pastetext,pasteword,|,charmap,|,outdent,indent,sub,sup,|,undo,redo,|,fullscreen,preview",
		theme_advanced_buttons2 : "insertlayer,moveforward,movebackward,absolute,|,tablecontrols,|,search,replace,|,styleprops,code,clean",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,span",
		gecko_spellcheck : true,
		convert_urls : false,
		
		save_enablewhendirty : true,
        save_onsavecallback : saveCallback,
		theme_advanced_buttons1_add : ",|,save,cancel,tinyautosave",
		
		content_css : "/css/mce.css",
		
		relative_urls : true,
		
		template_external_list_url : "js/tinymce/lists/template_list.js",
		external_image_list_url : "js/tinymce/lists/image_list.php",
		external_link_list_url : "js/tinymce/lists/link_list.php",
        media_external_list_url : "js/tinymce/lists/media_list.php",
        
	}
	
	var tinyMCE = $('#body').tinymce(options);
}


// list users in db
function ListUsers(sAction) {
	var aAction = sAction.split('.');
	$.ajax({
		type: "POST",
		url: xhr.listuser,
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

// assign user to department for access
function AssignUser(sAction)
{
	var aAction = sAction.split('.');
	$('#listserv').remove();
	var user = $.parseJSON(User(aAction[1]));
	if( $('#user-'+user.id).length === 0 ) {
		$.ajax({
			type: "POST",
			url: xhr.usrmeta,
			data: {
				action: 'add',
				user_id: user.id,
				meta_key: 'department',
				meta_value: aAction[0]
			},
			success: function(data) {
				if( data === '200' ) {
					$('<tr id="user-'+user.id+'"><td><a href="user.php?action=edit&id='+user.id+'">'+user.username+'</a></td>'+
						'<td><button type="button" style="padding:0;" onclick="RelinquishUser(\''+
						sAction+'\')">Relinquish</button></td></tr>').appendTo('.userlist');
					
				}
			}
		});
	}
}

// relinquish user in department
function RelinquishUser(sAction)
{
	var aAction = sAction.split('.');
	var user = $.parseJSON(User(aAction[1]));
	$.ajax({
		type: "POST",
		url: xhr.usrmeta,
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

// get user object
function User(id) {
	return $.ajax({
		type: "POST",
		url: xhr.listuser,
		data: {id:id},
		dataType: "json",
		async: false
	}).responseText;
}

// check if values exist
function SearchForExisting(table,string)
{
	return $.ajax({
		type: "POST",
		url: xhr.checkdb,
		data: { check: table, forthis: string },
		dataType: "json",
		async: false
	}).responseText;
}

// online user account
function UserCount(iNum) {
	$.ajax({
		type: "POST",
		url: xhr.usravail,
		data: {status:iNum}
	});
}

// department meta
function DepartmentMeta(key,value) {
	if( typeof value !== 'undefined' )
	{
		$.post(xhr.meta, { type: 'department', id: $('#department').val(), key: key, value: value });
	}
	else
	{
		
	}
}

// set clicked item as search
function SetSearch(obj)
{
	$('input#search_table').val($(obj).text()).trigger('keyup');
}

function ClearSearch()
{
	$('input#search_table').val('').trigger('keyup');
}


// pack application
function Pack()
{
	if(ValidateForm('package')){
		var posturl = xhr.package;
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

function LoadUploader()
{
	return false;
}

// pop up upload window
function Upload(oUpload)
{
	var url 	= xhr.upload;
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

// package new version of application
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

// search array for needle
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    };
    return false;
}

// Capitolize letter
function ucfirst(string)
{
    return string[0].toUpperCase() + string.slice(1);
}

function Update(field, sPage)
{
	var aPage = sPage.split(".");
	if(SearchForExisting('groups', field.value) !== 'false')
		if(field.value && field.value != aPage[3]) {
			var dataString = 'form='+aPage.join('-')+'&'+aPage[1]+'='+field.value;
			$.ajax({
				type: "POST",
				url: xhr.process,
				data: dataString,
				success: function(data) {
					if(data == 500) {
						ShowDialog('Failed to update.','error');
					}
				}
			});
		}
}

function Slug(string)
{
	var length = 50;
	string = string.replace(/\s+/g,'-').toLowerCase().substring(0, length);
	string = string.replace('&', 'and').replace(/[^a-zA-Z0-9\-]+/g,'');
	
	string = $.ajax({
		url: xhr.meta,
		type: "POST",
		data: { action: 'check-slug', slug: string },
		dataType: "HTML",
		async: false
	}).responseText;
	
	return string;
}

function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 8;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}

function MenuOrderInit()
{
	$('#menu-order:not(".empty")').sortable({
		placeholder: "ui-state-highlight",
		items: "li:not(.empty)",
		update: function(event, ui) {
			SaveSort(this);
		},
		stop: function(e,ui) {
			
		}
	});
	$('#remove-menu').droppable({
		hoverClass: "ui-state-active",
		accept: ".menu-item",
		drop: function(e,ui) {
			$.post(
				xhr.meta, 
				{
					type: 'menu-item',
					action: 'delete',
					umeta_id: ui.draggable.attr('id')
				},
				function() {
					ui.draggable.remove();
					var num = $('#menu-order:not(".empty")').children().length;
					$('<li class="empty">Add external link<span>'+num+'</span></li>')
						.appendTo('#menu-order')
						.bind('click', binddialog)
					;
					SaveSort('#menu-order:not(".empty")');
				}
			);
		}
	});
	$('li.empty').each(function() {
		$(this).bind('click', binddialog);
	});
	function binddialog() {
		var that = $(this);
		$('#dialog-form').dialog({
			autoOpen: true,
			width: 350,
			modal: true,
			buttons: {
				"Add Menu Item" : function() {
					AppendMenuItem(this,that);
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	}
}


function pickRandomProperty(obj) {
    var result;
    var count = 0;
    for (var prop in obj)
        if (Math.random() < 1/++count)
           result = prop;
    return result;
}



var menu = {
	
	settings : {
		
		department	: 0,
		template	: '#template',
		dropzone	: '#container',
		
		xhr : {
			address : 'includes/xhr/nav-menus.php',
			dataType : 'json'
		}
		
	},
	
	init : function(options) {
		menu.settings = $.extend( {}, menu.settings, options );
		menu.bind.click.addpage();
		menu.build.menu();
	},
	
	build : {
		
		menu : function() {
			var items = menu.data.all( menu.settings.department );
			
			if( items && ! items.error){
				
				for(var i=0; i<items.length; i++) {
					menu.build.item( items[i] );
				}
				
				$(menu.settings.dropzone).sortable({ items:"li" });
			}
		},
		
		item : function(obj) {
			var menuItem = $( menu.settings.template ).clone(true).data('menu', obj);
			
			console.log(obj);
			
			// Set ID
			menuItem.attr('id', menuItem.attr('id') + '-' + obj.id);
			
			// Set input name & values
			$('input', menuItem).each(function(){
				this.value = obj.menuitem[ this.name.replace('menu-item-','').replace('-','_') ];
				this.name = this.name + '[' + obj.id + ']';
			});
			
			// Set text
			$('.menu-item-bar .item-title', menuItem).text(obj.menuitem.title);
			$('.menu-item-bar .item-type', menuItem).text(obj.menuitem.type);
			$('.menu-item-settings .field-url input', menuItem).val(obj.menuitem.url);
			$('.menu-item-settings .field-title input', menuItem).val(obj.menuitem.title);
			
			if(obj.menuitem.object != 'custom') {
				$('.menu-item-settings .field-url', menuItem)
					.replaceWith('<div class="original"><em>Original:</em> <a href="'+obj.guid+'" target="_blank">'+obj.title+'</a></div>');
			}
			
			// Bind events
			$('.menu-item-bar a.item-edit, .menu-item-settings .cancel', menuItem).click(function(e) {
				e.preventDefault();
				$('.menu-item-settings', menuItem).slideToggle();
			});
			$('.menu-item-settings .delete', menuItem).click(function(e) {
				e.preventDefault();
				var r = confirm('Are you sure you want to delete "'+obj.menuitem.title+'"?');
				if(r == true) {
					menu.ajax.remove(obj.id);
				}
			});
			
			// Append to dropzone
			menuItem.appendTo( menu.settings.dropzone ).fadeIn('slow');
			
			$(menu.settings.dropzone).sortable( "refresh" );
		}
		
	},
	
	data : {
		
		all : function(id) {
			return $.parseJSON( menu.ajax.request({ action:'get-all-menus', department:id }) );
		},
		
		single : function(id) {
			return $.parseJSON( menu.ajax.request({ action:'get-single-menu', id:id }) );
		}
		
	},
	
	bind : {
		
		click : {
			
			addpage : function() {
				$('#add-page input.addto:checked').each(function() {
					var id = Number(this.name.replace('add-',''));
					this.checked = false;
					menu.ajax.create({ action:'add-page-menu', object_id:id });
				});
				return false;
			},
			
			addcustom : function() {
				var data = { action:'add-custom-menu', department:menu.settings.department }, post;
				$('#add-custom input').each(function() {
					data[this.name] = this.value;
					this.value = '';
				});
				menu.ajax.create(data);
				return false;
			},
			
			savemenu : function() {
				var data = { action:'update-menus' }, i = 1;
				$(menu.settings.dropzone + ' li.menu-item').each(function() {
					data[i] = $(this).data('menu');
					data[i].position = data[i].menuitem.position = i;
					
					var inputtitle = $('input[name="menu-item-title['+data[i].id+']"]', this).val();
					if(data[i].menuitem.title != inputtitle) {
						data[i].menuitem.title = inputtitle;
					}
					
					if(data[i].menuitem.object == 'custom') {
						var inputurl = $('input[name="menu-item-url['+data[i].id+']"]', this).val();
						if(data[i].menuitem.url != inputurl) {
							data[i].menuitem.url = data[i].guid = inputurl;
						}
					}
					i++;
				});
				menu.ajax.update(data);
			}
			
		}
			
	},
	
	ajax : {
		
		create : function(data) {
			var response = $.parseJSON( this.request(data) );
			menu.build.item(response);
		},
		
		update : function(data) {
			var response = $.parseJSON( this.request(data) );
			this.response(response.success);
		},
		
		remove : function(id) {
			var elem = $('li#menu-item-'+id),
				data = elem.data('menu')
			;
			elem.remove();
			data['action'] = 'remove-menu';
			var response = $.parseJSON( this.request(data) );
			this.response(response.success);
		},
		
		response : function(msg) {
			SetMessage(msg);
			location.reload(true);
		},
		
		request : function(data, callback, dataType) {
			return $.ajax({
				url : menu.settings.xhr.address,
				dataType : dataType || menu.settings.xhr.dataType,
				data : data,
				type: "POST",
				async: false,
				success : callback
			}).responseText;
		}
		
	}
		
};




