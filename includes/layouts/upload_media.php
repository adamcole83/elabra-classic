<?php
    require_once('../initialize.php');
    require_once(CORE_PATH.DS.'UploadHandler.php');
    $department = new Department();
    $department->id = $_SESSION['department'];
    $dept = $department->get();
    $media_dir = 'uploads';
    $path_to_uploads = PUBLIC_ROOT.DS.$dept->subdir.DS.$media_dir.DS;

    if( ! file_exists( PUBLIC_ROOT.$path_to_uploads )) {
        if( ! @mkdir( PUBLIC_ROOT.$path_to_uploads, 0775, true )) {
            echo "<div id=\"alert-box\" class=\"error\" style=\"display:block;\">Directory <strong>".$path_to_uploads."</strong> is not writeable. Make sure department directory has permissions of <code>0775</code> or <code>drwxr-xr-x</code>.</div>";
        }
    }
?>

<div class="modal-container">
    <form id="uploadify" action="" method="post">
        <h2 class="tab-media">
            Upload Media
            <input id="file_upload" name="file_upload" class="button" type="file" multiple="true" />
        </h2>
        
        <div class="clear"></div>
        <div id="queue"></div>
    </form>
    <div class="controls">
        <a class="button" id="done" href="media.php?action=multiedit" onclick="CloseModal();return false;">Done</a>
    </div>
</div>

<script type="text/javascript">
    <?php $timestamp = time(); ?>
    $(function () {
        $('#file_upload').uploadifive({
            'auto'              : true,
            'checkScript'       : 'includes/xhr/check-exists.php',
            'formData'          : {
                'uploadpath': '<?php echo $path_to_uploads; ?>',
                'user_id'   : '<?php echo $session->user_id; ?>',
                'timestamp' : '<?php echo $timestamp; ?>',
                'token'     : '<?php echo md5("unique_salt".$timestamp); ?>'
            },
            'queueID'           : 'queue',
            'uploadScript'      : 'includes/xhr/uploadifive.php',
            'onUploadComplete'  : function (file, data) {
                var $done = $("#done");
                $done.attr('onclick', '');
                $done.attr('href', $done.attr('href') + '&id[]=' + data);
            }
        });
    });
</script>
