<?
	$content = new Content();
	$content->id = $_GET['id'];
	$post = $content->get('attachment');
	
	$datatype = ext2type(mime2ext($post->post_mime_type));
	$size = ($datatype=='image') ? getimagesize(DOMAIN.$post->url):null;
	
	$width = ($size[0] > 480) ? 480 : $size[0];
	$height = ($size[1]> 320) ? 320 : $size[1];
	
	$banner = ($size[0] == 550 && $size[1] == 200) ? true : false;
?>
<h2 class="tab-media">Media Library</h2>
<div id="alert-box">&nbsp;</div>

<form class="editor">
	<div id="window-editor-preview" style="width:<? echo $width; ?>px;">
		<? if($datatype == 'image'): ?>
		<p class="thumb"><img src="<? echo $post->url; ?>" width="<? echo $width ?>px" height="<? echo $height ?>px" alt="<? echo (empty($post->body))?$post->title:$post->body; ?>" /></p>
		<p class="textCenter"><? echo _tw($post->title,$width); ?></p>
		<? else: ?>
		<img class="icon" src="images/mediaicons/<? echo $datatype; ?>_large.png" width="256" height="256" alt="<? echo ucfirst($datatype); ?>" />
		<? endif; ?>
	</div><!-- #preview -->
	<table class="form">
		<tr>
			<th><label for="filetype">Filetype</label></th>
			<td><p><? echo ucfirst(mime2type($post->post_mime_type)) ." - ". mime2ext($post->post_mime_type) ?></p></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="date">Upload Date</label></th>
			<td><p><? echo time_to_text($post->created); ?></p></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="title">Title</label></th>
			<td><input type="text" id="title" name="title" class="required" value="<? echo $post->title; ?>" /></td>
			<td><label for="title" class="error" id="title_error"><span>(required)</span></label></td>
		</tr>
		<tr>			
			<th><label for="body">Caption/Alternative Text</label></th>
			<td><input type="text" id="body" name="body" value="<? echo $post->body; ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<? if($banner): ?>
			<th><label for="description">Banner Link</label></th>
			<td><input type="text" id="description" name="description" value="<? echo $post->description; ?>" /></td>
			<td><label for="description" class="error" id="description_error"><span>(required)</span></label></td>
			<? else: ?>
			<th><label for="description">Description</label></th>
			<td><textarea id="description" name="description"><? echo $post->description; ?></textarea></td>
			<td></td>
			<? endif; ?>
		</tr>
		<tr>
			<th><label for="guid">Permalink</label></th>
			<td><input type="text" id="guid" name="guid" disabled="disabled" value="<? echo $post->guid; ?>" /></td>
			<td></td>
		</tr>
<!--
		<tr>
			<th><label for="file">Upload New Version</label></th>
			<td><input type="file" id="file" name="file" value="" /></td>
			<td></td>
		</tr>
-->
		<input type="hidden" name="url" value="<? echo $post->url ?>" />
		<input type="hidden" name="department" value="<? echo $post->department ?>" />
		<input type="hidden" name="post_type" value="attachment" />
	</table>
</form>

<div class="controls" style="float:left;">
	<a class="button inactive" href="javascript:void(0);" onclick="Delete('media.<? echo $content->id; ?>')">Delete</a>
</div>
<div class="controls">
	<a class="button inactive escape" href="media.php">Cancel</a>
	<a class="button enter" href="javascript:void(0);" onclick="Save('media.<? echo $content->id; ?>')">Save</a>
</div>
