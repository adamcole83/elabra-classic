<?

// instantiate objects
$_dept = new Department();
$content = new Content();
$content->department = $_SESSION['department'];

// set identifiers
$_dept->id = $_SESSION['department'];

$change_permalink = Group::can('change_permalink');
?>
<script type="text/javascript">
	$(function() {
		LoadMCE('code');
		$('input#title').bind('blur',function() {
			if(this.value == '') {
				return false;
			}
			var slug = Slug(this.value),
				dir = '<?php echo Department::grab($_SESSION['department'])->subdir ?>',
				inputURL = document.getElementById('url'),
				inputGUID = document.getElementById('guid')
			;
			if(inputURL.value == '') {
				inputURL.value = slug;
			}
			if(inputGUID.value == '') {
				inputGUID.value = 'http://medicine.missouri.edu/'+dir+'/'+slug+'.html';
			}
		});
		$('input#url').bind('blur', function() {
			if(this.value == '') {
				return false;
			}
			var dir = '<?php echo Department::grab($_SESSION['department'])->subdir ?>',
				guid = document.getElementById('guid')
			;
			if(guid.value != 'http://medicine.missouri.edu/'+dir+'/'+this.value+'.html') {
				guid.value = 'http://medicine.missouri.edu/'+dir+'/'+this.value+'.html';
			}
		});
		window.onbeforeunload = function() {
			if(tinyMCE.activeEditor.isDirty()) {
				return "The changes you made will be lost if you navigate away from this page.";
			}else{
				return null;
			}
		};
	});
</script>
<h2 class="tab-pages">New Page</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<textarea id="body" name="body"></textarea>
	<br />
	<table class="form">
		<tr>
			<td></td>
			<td><h3>Properties</h3></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="title">Title</label></th>
			<td><input type="text" name="title" id="title" value="" class="required" /></td>
			<td><label for="title" class="error" id="title_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="description">Description<br /><span>meta tag</span></label></th>
			<td><textarea name="description" id="description" class="required"></textarea></td>
			<td><label for="description" class="error" id="description_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="url">Slug</label></th>
			<td><input type="text" name="url" id="url" value="" class="required" /></td>
			<td><label for="url" class="error" id="url_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="guid">Permalink</label></th>
			<td><input type="text" name="guid" id="guid" value="" class="required" /></td>
			<td><label for="guid" class="error" id="guid_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="parent_id">Parent Page</label></th>
			<td><select id="parent_id" name="parent_id">
				<option value="0">(no parent)</option>
				<?php $content->parent_dropdown(); ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="menu_order">Menu Order</label></th>
			<td><input type="text" name="menu_order" id="menu_order" maxlength="2" value="0" style="min-width:30px;width:30px;" /></td>
		</tr>
		<? if(Group::can('publish_page')): ?>
		<tr>
			<th><label for="status">Status</label></th>
			<td>
				<select name="status" id="status" class="required">
					<option value="draft">Draft</option>
					<option value="published">Published</option>
				</select>
			</td>
		</tr>
		<? else: ?>
		<input type="hidden" name="status" value="draft" />
		<? endif; ?>
		
		<input type="hidden" name="updatedBy" value="<? echo $_SESSION['user_id']; ?>" />
		<input type="hidden" name="post_type" value="post" />
		<input type="hidden" name="department" value="<? echo $_SESSION['department']; ?>" />
	</table>
</form>
<div class="controls">	
	<a class="button inactive" href="page.php">Done</a>
	<a class="button" id="publish" href="javascript:void(0);" onclick="Save('page')">Save</a>
</div>
<div class="clear"></div>