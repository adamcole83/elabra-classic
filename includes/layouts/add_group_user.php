<h2 class="tab-user">New Group</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="name">Name</label></th>
			<td><input class="required noduplicate" type="text" name="name" id="name" value="" /></td>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td></td>
			<td><h3>Group Permissions <label class="error" for="permission" id="permission_error"><span>(required)</span></label></h3></td>
			<td></td>
		</tr>
		<tr>
			<td>
			<td>
				<table class="userlist">
					<? foreach(Group::find_all('permissions', array("group_id"=>0), 'name ASC') as $permission): ?>
					<tr>
						<td><input type="checkbox" class="required" name="permission" value="<? echo $permission->id ?>" />&nbsp;<? echo $permission->name ?></td>
					</tr>
					<? endforeach; ?>
				</table>
			</td>
			<td></td>
		</tr>
	</table>
</form>

<div class="controls">
	<input type="hidden" name="table" value="groups" />
	<input type="hidden" name="permissions" value="" />
	<a class="button inactive" href="user.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="Save('group')">Save</a>
</div>
<div class="clear"></div>
