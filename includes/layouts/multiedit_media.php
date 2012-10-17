<h2 class="tab-media">Edit Media</h2>
<div id="alert-box">&nbsp;</div>
<? $count = 1; ?>
<? foreach( $_GET['id'] as $key => $id ): ?>
<?
	$content = new Content();
	$content->id = $id;
	$post = $content->get('attachment');
	
	$datatype = ext2type(mime2ext($post->post_mime_type));
	$dimensions = ($datatype=='image') ? getimagesize(DOMAIN.$post->url):null;
	
	$width = ($dimensions[0] > 480 || $dimensions[1] > 320) ? '480px' : $dimensions[0]. "px";
	$height = ($dimensions[0] > 480 || $dimensions[1] > 320) ? '320px' : $dimensions[1]. "px";
	
	$banner = ($dimensions[0] == 550 && ($dimensions[1] == 200 || $dimensions[1] == 196 )) ? true : false;
?>
<form class="editor" id="<? echo $post->id; ?>">
	<table class="form">
		<tr>
			<td></td><td colspan="2"><h3><? echo ($banner) ? "Rotating Banner Detected" : _t($post->title, 30); ?></h3></td>
		</tr>
		<? if(ext2type(file_extension($post->url)) == 'image'): ?>
		<tr>
			<th><label for="thumb">Preview</label></th>
			<td>
				<p class="media-image" style="margin:0 10px 0 0;">
					<? if($banner): ?>
					<a href="media.php?action=edit&id=<? $post->id; ?>"></a><img src="includes/thumb.php?f=<? echo urlencode(PUBLIC_ROOT.$post->url) ?>&width=275&height=100" title="<? echo $post->title ?>" /></a>
					<? else: ?>
					<a href="media.php?action=edit&id=<? $post->id; ?>"></a><img src="includes/thumb.php?f=<? echo urlencode(PUBLIC_ROOT.$post->url) ?>&width=60&height=60" title="<? echo $post->title ?>" /></a>
					<? endif; ?>
				</p>
			</td>
			<td></td>
		</tr>
		<? endif; ?>
		<tr>
			<th><label for="mime-type">Filetype</label></th>
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
			<td><input type="text" id="title" name="title" class="required" value="<? echo $post->title; ?><? echo ($banner)?$count:null; ?>" /></td>
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
			<th><label for="url">URL</label></th>
			<td><input type="text" id="url" name="url" disabled="disabled" value="<? echo $post->url; ?>" /></td>
			<td></td>
		</tr>
		<input type="hidden" name="department" value="<? echo $post->department; ?>" />
		<input type="hidden" name="url" value="<? echo $post->url; ?>" />
		<input type="hidden" name="guid" value="<? echo $post->guid; ?>" />
	</table>
</form>
<? $count++; ?>
<? endforeach; ?>
<div class="controls">
	<a class="button inactive escape" href="media.php">Cancel</a>
	<a class="button enter" href="javascript:void(0);" onclick="SaveAll('media')">Save All</a>
</div>
<div class="clear"></div>
