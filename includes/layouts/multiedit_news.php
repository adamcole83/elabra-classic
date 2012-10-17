<h2 class="tab-pages">Edit Articles</h2>
<div id="alert-box">&nbsp;</div>

<? foreach( $_GET['id'] as $id ): ?>
<?
	// instantiate objects
	$content = new Content();
	$department = new Department();
	
	// set identifiers
	$content->id = $id;
	
	// load fresh data from db
	$post = $content->get($id);
?>
	
<form class="editor">
	<table class="form">
		<tr>
			<td></td><td colspan="2"><h3><? echo _t($post->title,30); ?></h3></td>
		</tr>
		<tr>
			<th><label for="title">Title</label><br /></th>
			<td><input type="text" name="title" id="title" value="<? echo $post->title ?>" class="required" /></td>
			<td><label for="title" class="error" id="title_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="url">Slug (Page URL)</label></th>
			<td><input type="text" name="url" id="url" value="<? echo $post->url ?>" class="required" /></td>
			<td><label for="url" class="error" id="url_error"><span>(required)</span></label></td>
		</tr>
		</tr>
			<th><label for="post_created">Publish</label></th>
			<td><input type="text" name="post_created" id="post_created" value="<? echo _d($post->post_created) ?>" class="required" /></td>
			<td><label for="post_created" class="error" id="post_created_error"><span>(required) [click to change]</span></label></td>
		</tr>
		<tr>
			<th><label for="department">Department</label></th>
			<td><select id="department" name="department" class="required">
				<? foreach($department->find_all() as $dept): ?>
				<option <? selected($dept->id, $post->department, false, Group::can('delete_pages')) ?> value="<? echo $dept->id; ?>"><? echo $dept->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label for="department" class="error" id="department_error"><span>(required)</span></label></td>
		</tr>
	</table>
</form>
	
<? endforeach; ?>

<div class="controls">
	<a class="button inactive" href="news.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="SaveAll('article')">Save All</a>
</div>
<div class="clear"></div>