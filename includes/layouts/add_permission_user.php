<?

// instantiate objects
$_group = new Group();

?>
<h2 class="tab-user">New Permission</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="name">Name</label></th>
			<td><input type="text" name="name" id="name" value="" class="required noduplicate" /></th>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></th>
		</tr>
		<tr>
			<td></td>
			<td><h3>Add to groups</h3></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<table class="userlist">
					<? foreach(Group::find_all('groups', '', 'name ASC') as $group): ?>
					<tr>
						<td><input type="checkbox" name="group" value="<? echo $group->id ?>" />&nbsp;<? echo $group->name ?></td>
					</tr>
					<? endforeach; ?>
				</table>
			</td>
			<td></td>
		</tr>
	</table>
</form>

<div class="controls">
	<input type="hidden" name="table" value="permissions" />
	<input type="hidden" name="groups" value="" />
	<a class="button inactive" href="user.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="Save('permission')">Save</a>
</div>
<div class="clear"></div>
