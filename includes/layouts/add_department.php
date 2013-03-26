<?php $content = new Content(); ?>
<h2 class="tab-department">New Department</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<h3>Basic Information</h3>
	<table class="form">
		<tr>
			<th><label for="name">Title</label></th>
			<td><input class="required" type="text" name="name" id="name" value="" /></td>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="code">Short Code</label></th>
			<td><input class="required" type="text" name="code" id="code" value="" maxlength="4" /></td>
			<td><label class="error" for="code" id="code_error"><span>(required)</span></label></td>
		<tr>
			<th><label for="subdir">Subdirectory</label></th>
			<td><input class="required" type="text" name="subdir" id="subdir" value="" /></td>
			<td><label class="error" for="subdir" id="subdir_error"><span>(required)</span></label></td>
		</tr>
		<?php if (Group::can('duplicate_data')): ?>
		<tr>
			<th><label for="import_id">Import Data From</label></th>
			<td>
				<select name="import_id" id="import_id">
					<?php if ( ! Department::find_all()): ?>
						<option selected="selected" disabled="disabled" value="">No departments found</option>
					<?php else: ?>
						<option value=""></option>
						<? foreach(Department::find_all() as $dept): ?>
						<option value="<? echo $dept->id; ?>"><? echo $dept->name; ?></option>
						<? endforeach; ?>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th><label for="dev_mode">Developer Mode</label></th>
			<td><p><input type="radio" checked="checked" name="dev_mode" value="1" /> On <input type="radio" name="dev_mode" value="2" /> Off</p></td>
		</tr>
	</table>
</form>

<div class="controls">
	<a class="button inactive" href="department.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="Save('department.redirect')">Save</a>
</div>
<div class="clear"></div>