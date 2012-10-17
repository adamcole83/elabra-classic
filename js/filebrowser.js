var root = '/var/www/html/';
var apps = root+'admin/applications/';
var type = '';

function Browse(t, sID)
{
	type = t;
	// display window
	$.ajax({
		url: 'layouts/browser.php?a=' + type,
		cache: false,
		success: function(html) {
			$('#content').append(html);
			if(type == 'extract') 
				PageLayout_Extract(sID);
			if(type == 'open')
				PageLayout_Open();
		}
	});
}

function PageLayout_Extract(sID)
{
	PageLayout();
	$('#filebrowser h1.title').text('Deploy: '+$('#title-'+sID).text());
	// okay buttons
	$('#d-extract').click(function() {
		// deploy application
		DeployApplication(sID);
	});
}

function PageLayout_Open()
{
	PageLayout();
	$('#filebrowser h1.title').text('Open');
	// okay buttons
	$('#d-open').click(function() {
		var text = GetLocation();
		$('input#directory').val(text);
		CloseWindow('#filebrowser');
	});
}

function PageLayout()
{
	// load php file browser - append to .browser
	var startdir = GetLocation();
	ShowBrowser(startdir);
	
	OpenWindow('#filebrowser');
	
	// set up buttons
	// cancel button
	$('#d-cancel').click(function() {
		CloseWindow('#filebrowser');
	});
	// new folder
	$('#d-new-folder').click(function() {
		
		// show new folder input
		OpenWindow('#folder-window');
		$('input#folder').focus();
		
		// set up buttons
		$(document).keypress(function(e) {
			switch( e.which ) {
				case 0:
					CloseNewFolder();
					break;
				case 13:
					Process_NewFolder();
					break;
			}
		});
		
		$('#f-cancel').click(function() {
			CloseNewFolder();
		});
		
		$('#f-create').click(function() {
			Process_NewFolder();
		});
	});
	$('select#location').change(function() {
		var selected = $('select#location option:selected').val();
		
		var loc = '';
		var stop = false;
		$(this).children('option').each(function() {
			if(!stop)
				loc += $(this).val();
			if(stop)
				$(this).remove();
			if($(this).val() == selected)
				stop = true;
		});
		ShowBrowser(loc);
	});
}

function ShowBrowser(dir)
{
	if(!dir)
		dir = '';
	
	dir = dir.replace(/\/var\/www\/html\/medicine\.missouri\.edu\/(.*)/, "$1");
	
	$("#filebrowser .browser").html('');
	
	$.ajax({
		url: 'includes/files.php?dir='+dir+'&type='+type,
		cache: false,
		success: function(html){
			$("#filebrowser .browser").html(html);
			NavigateBrowser();
		}
	});	
}

function NavigateBrowser()
{
	$('.view div').each(function(){
		
		// each folder onclick
		$(this).click(function() {
			$(this).parent().children('div').each(function() {
				$(this).children('span').removeClass('selected');
			});
			$(this).children('span').addClass('selected');
		});
		
		// each folder on dblclick
		$(this).dblclick(function() {
			var dir = $(this).children('span').text();
			loc = AddCrumb(dir);
			ShowBrowser(loc);
		});
	});
}


function Process_NewFolder(name, loc)
{
	// get contents of input and selected location
	var name = $('input#folder').val();
	if(name !== '') {
		var loc = GetLocation();
		$.ajax({
			url: 'includes/files.php?new='+ escape(loc+name+'/'),
			cache: false,
			success: function(data) {
				if(data == 1) {
					CloseNewFolder();
					AddCrumb(name);
					ShowBrowser(loc+name);
				}else{
					alert('Folder not created, please try again!');
				}
			}
		});
	}
}

function DeployApplication(sID)
{
	var dir = GetLocation();
	var appLoc = $('#dir-'+sID).text();
	var appFile = appLoc + $('span#filename-'+sID).text();
	
	$.post(
		'includes/deploy.php',
		{ appFile: appFile, extractLocation: dir },
		function(data) {
			if(data==1){
				alert('Application was successfully deployed.');
				CloseWindow('#filebrowser');
			}
		},
		'html'
	);
}

function CloseNewFolder()
{
	$('#folder-window').fadeOut('slow',function(){
		$('input#folder').val('');
	});
}

function AddCrumb(dir)
{
	$('select#location').append($('<option></option>').
		attr("value", dir+'/').
		text(dir+'/'));
	
	var loc = '';
	$('select#location').children('option').each(function() {
		$(this).attr('selected', '');
		loc += $(this).val();
	});
	$('select#location').children('option:last').attr('selected', 'selected');
	
	return loc;
}

function GetLocation()
{
	var loc = '';
	$('select#location').children('option').each(function() {
		loc += $(this).val();
	});
	return loc;
}

function OpenWindow(id)
{
	$(id).fadeIn('slow');
}

function CloseWindow(id)
{
	$(id).fadeOut('slow');
}