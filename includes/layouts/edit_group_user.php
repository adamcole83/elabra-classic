<?

// instantiate objects
$_group = new Group();
$_group->id = $_GET['id'];

?>
<h2 class="tab-user"><? echo $_group->get()->name; ?></h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="name">Name</label></th>
			<td><input onblur="Update(this,'group.name.<? echo $_GET['id'].".".$_group->get()->name ?>')" type="text" name="name" id="name" value="<? echo $_group->get()->name ?>" /></td>
			<td><label for="name" id="name_error" class="error"><span>[autosave <strong>on</strong>] (required)</span></label></td>
		</tr>
		<tr>
			<td></td>
			<td><h3>Group Permissions <label for="permission" id="permission_error" class="error"><span>[autosave <strong>on</strong>] (required)</span></label></h3></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<table class="userlist">
					<? foreach(Group::find_all('permissions', array("group_id"=>0), 'name DESC') as $permission): ?>
					<tr>
						<td><input type="checkbox" <? checked($_group->hasPermission($permission->id),$permission->id) ?> name="permission" value="<? echo $permission->id ?>" onclick="TogglePermission(this, <? echo $_GET['id'] ?>)" />&nbsp;<? echo $permission->name ?></td>
					</tr>
					<? endforeach; ?>
				</table>
			</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2">
				
			</td>
		</tr>
	</table>
</form>

<div class="controls" style="float:left;">
	<a class="button inactive" href="javascript:void(0);" onclick="Delete('group.<? echo $_group->id; ?>')">Delete</a>
</div>
<div class="controls">
	<a class="button" href="user.php">Done</a>
</div>
<div class="clear"></div>