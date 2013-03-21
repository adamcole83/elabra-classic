<?

	$department = new Department();
	$department->id = $_SESSION['department'];
	$dept = $department->get();
	$media_dir = 'uploads';
	$path_to_uploads = DS.$dept->subdir.DS.$media_dir.DS;

	if(!file_exists(PUBLIC_ROOT.$path_to_uploads)) {
		if(!@mkdir(PUBLIC_ROOT.$path_to_uploads, 0775, true)) {
			echo "<div id=\"alert-box\" class=\"error\" style=\"display:block;\">Directory <strong>".$path_to_uploads."</strong> is not writeable. Make sure department directory has permissions of <code>0775</code> or <code>drwxr-xr-x</code>.</div>";
		}
	}

?>

<script type="text/javascript">
	$(function() {
		var settings = {
			flash_url : "js/swfupload/swfupload.swf",
			upload_url : "includes/xhr/upload.php",
			file_size_limit : "100 MB",
			file_types : "<? echo "*.".join(";*.", array_keys(get_allowed_mime_types())); ?>",
			file_types_description: "Media Files",
			file_size_limit: 524288000,
			file_upload_limit : 30,
			file_queue_limit : 30,
			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel",
				directory: "<? echo $path_to_uploads; ?>"
			},
			post_params: {
				directory: "<? echo $path_to_uploads; ?>",
				user_id: "<? echo $_SESSION['user_id']; ?>"
			},
			button_placeholder_id : "spanButtonPlaceholder",
			button_width: 88,
			button_height: 24,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,

			// The event handler functions are defined in handlers.js
			swfupload_preload_handler : swfUploadPreLoad,
			swfupload_load_failed_handler : swfUploadLoadFailed,
			swfupload_loaded_handler : swfUploadLoaded,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete,	// Queue plugin event,
			minimum_flash_version : "9.0.28",
		};
		var swfu = new SWFUpload(settings);
	});
</script>
<h2 class="tab-media">Upload New Media</h2>

<form id="media-upload" action="" method="post">
	<span id="spanButtonPlaceholder"></span>
	<a class="button" id="btnUpload">Select Files</a>
	<a class="button inactive" id="btnCancel">Cancel Uploads</a>
	<a id="edit" class="button inactive" href="media.php?action=multiedit">Edit All Uploads</a>
</form>
<div class="clear"></div>
<div id="fsUploadProgress">
	<h3>Upload Queue</h3>
</div>

<div id="divLoadingContent" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
	SWFUpload is loading. Please wait a moment...
</div>
<div id="divLongLoading" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
	SWFUpload is taking a long time to load or the load has failed.  Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.
</div>
<div id="divAlternateContent" class="content" style="background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;">
	We're sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player.
	Visit the <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe website</a> to get the Flash Player.
</div>
