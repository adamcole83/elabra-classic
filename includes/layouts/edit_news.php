<?
	
	// instantiate objects
	$content = new Content();
	$department = new Department();
	
	// set identifiers
	$content->id = $_GET['id'];
	
	// load fresh data from db
	$post = $content->get($_GET['id']);
?>
<script type="text/javascript">
	$(function() {
		LoadMCE();
		$('#title').bind('blur', function() {
			var string = $(this).val();
			$('#url').val( Slug(string) );
		});
	});
</script>
<h2 class="tab-pages">Edit Article</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<div class="title">
		<label for="title">Title <span>(required)</span></label><br />
		<input type="text" name="title" id="title" value="<? echo $post->title ?>" class="required" />
	</div>
	<textarea id="body" name="body"><? echo $post->body ?></textarea>
	<br />
	<table class="form">
		<tr>
			<td></td>
			<td><h3>Properties</h3></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="url">Slug (Page URL)</label></th>
			<td><input type="text" name="url" id="url" value="<? echo $post->url; ?>" class="required" /></td>
			<td><label for="url" class="error" id="url_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="attachment">Featured Media</label></th>
			<td><p><a class="button inactive" href="javascript:void(0);" onclick="">Set Media</a><span class="display"><? echo _n($post->feature_media_id,'None'); ?></span></p></td>
			<td><input type="hidden" name="featured_media_id" value="<? echo $post->featured_media_id ?>" /><label><span>Images will be sized accordingly</span></label></td>
		</tr>
		<tr>
			<th><label for="attachment">Thumbnail Image</label></th>
			<td><p><a class="button inactive" href="javascript:void(0);" onclick="">Set Image</a><span class="display"><? echo _n($post->thumb_id,'None'); ?></span></p></td>
			<td><input type="hidden" name="thumb_id" value="<? echo $post->thumb_id ?>" /></td>
		</tr>
		</tr>
			<th><label for="post_created">Publish</label></th>
			<td><p><a class="button inactive datepicker" href="javascript:void(0);">Select Date</a><span class="display date"><? echo date('m/d/y',$post->post_created); ?></span></p></td>
			<td><input type="hidden" name="post_created" value="<? echo date('m/d/y',$post->post_created); ?>" /></td>
		</tr>
		<tr>
			<th><label for="department">Department</label></th>
			<td><p><? echo Department::grab($_SESSION['department'])->name; ?></p></td>
			<td><input type="hidden" name="department" value="<? echo $_SESSION['department'] ?>" /></td>
		</tr>
	</table>
</form>

<div class="controls" style="float:left;">
	<a class="button inactive" href="javascript:void(0);" onclick="Purge('page.<? echo $content->id; ?>.redirect')">Delete</a>
</div>
<div class="controls">	
	<a class="button inactive" href="news.php">Cancel</a>
	<a class="button inactive reload" href="javascript:void(0);" onclick="Save('article.<? echo $post->id; ?>',this)">Save</a>
	<? if(Group::can('publish_page')): ?>
	<a class="button save" id="publish" href="javascript:void(0);" onclick="Publish('article.<? echo $content->id; ?>')">Publish</a>
	<? endif; ?>
</div>
