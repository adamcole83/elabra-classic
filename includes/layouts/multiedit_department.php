<h2 class="tab-pages">Edit Departments</h2>
<div id="alert-box">&nbsp;</div>

<? foreach( $_GET['id'] as $id ): ?>
<?
	// instantiate objects
	$_dept = new Department();
	$content = new Content();
	
	// set identifiers
	$_dept->id = $id;
	$content->department = $id;
	
	// load fresh data for display
	$dept = $_dept->get();
?>
	
<form class="editor">
	<table class="form">
		<tr>
			<td></td><td colspan="2"><h3><? echo _t($dept->name,40); ?></h3></td>
		</tr>
		<tr>
			<th><label for="name">Name</label></th>
			<td><input type="text" name="name" id="name" value="<? echo $dept->name; ?>" class="required" /></td>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="code">Code</label></th>
			<td><input type="text" name="code" id="shortcode" value="<? echo $dept->shortcode; ?>" maxlength="4" class="required" /></td>
			<td><label class="error" for="code" id="shortcode_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="subdir">Subdirectory</label></th>
			<td><input type="text" name="subdir" id="subdir" value="<? echo $dept->subdir; ?>" class="required" /></td>
			<td><label class="error" for="subdir" id="subdir_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="index_id">Home Page</label></th>
			<td>
				<select name="index_id" id="index_id" class="required">
					<? foreach($content->find_all('post') as $page): ?>
					<option <? selected($dept->index_id,$page->id) ?> value="<? echo $page->id; ?>"><? echo $page->title ?></option>
					<? endforeach; ?>
					<? if(!$content->find_all('post')): ?>
					<option selected="selected" disabled="disabled" value="">No pages found</option>
					<? endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="dev_mode">Developer Mode</label></th>
			<td><p><input type="radio" <? checked($dept->dev_mode,'1') ?> name="dev_mode" value="1" /> On <input type="radio" <? checked($dept->dev_mode,'2') ?> name="dev_mode" value="2" /> Off</p></td>
		</tr>
	</table>
</form>
	
<? endforeach; ?>

<div class="controls">
	<a class="button inactive" href="department.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="SaveAll('department')">Save All</a>
</div>
<div class="clear"></div>