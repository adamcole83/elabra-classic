<h2 class="tab-pages">Edit Pages</h2>
<div id="alert-box">&nbsp;</div>

<? foreach( $_GET['id'] as $id ): ?>
<?
	$content = new Content();
	$content->id = $id;
	$content->department = $_SESSION['department'];
	$post = $content->get('post');
	
	$dept = new Department();
?>
	
<form class="editor" id="<? echo $post->id; ?>">
	<table class="form">
		<tr>
			<td></td><td colspan="2"><h3><? echo _t($post->title,30); ?></h3></td>
		</tr>
		<tr>
			<th><label for="title">Title</label></th>
			<td><input type="text" name="title" id="title" value="<? echo $post->title ?>" class="required" /></td>
			<td><label for="title" class="error" id="title_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="description">Description</label></th>
			<td><textarea name="description" id="description" class="required"><? echo $post->description ?></textarea></td>
			<td><label for="description" class="error" id="description_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="url">URL</label></th>
			<td><input type="text" name="url" id="url" value="<? echo $post->url ?>" class="required" /></td>
			<td><label for="url" class="error" id="url_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="parent_id">Parent Page</label></th>
			<td><select id="parent_id" name="parent_id">
				<option value="0">(no parent)</option>
				<?php $content->parent_dropdown($post->parent_id); ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="menu_order">Menu Order</label></th>
			<td><input type="text" name="menu_order" id="menu_order" maxlength="2" value="<?php echo $post->menu_order; ?>" style="min-width:30px;width:30px;" /></td>
			<input type="hidden" name="department" value="<? echo $post->department; ?>" />
			<input type="hidden" name="url" value="<? echo $post->url; ?>" />
			<input type="hidden" name="guid" value="<? echo $post->guid; ?>" />
		</tr>
	</table>
</form>
	
<? endforeach; ?>

<div class="controls">
	<a class="button inactive" href="page.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="SaveAll('page')">Save All</a>
</div>
<div class="clear"></div>