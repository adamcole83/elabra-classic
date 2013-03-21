<h2 class="tab-pages">Edit Users</h2>
<div id="alert-box">&nbsp;</div>

<? foreach( $_GET['id'] as $id ): ?>
<?
	// instantiate objects
	$_user = new User();
	$_dept = new Department();
	
	// set identifiers
	$_user->id = $id;
	
	// load fresh data for display
	$user = $_user->get($id);
?>
	
<form class="editor">
	<table class="form">
		<tr>
			<td></td><td colspan="2"><h3><? echo $user->full_name(); ?></h3></td>
		</tr>
		<tr>
			<th><label for="">First Name</label></th>
			<td><input type="text" id="first_name" name="first_name" value="<? echo $user->first_name; ?>" class="required" /></td>
			<td><label class="error" for="first_name" id="first_name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="">Last Name</label></th>
			<td><input type="text" id="last_name" name="last_name" value="<? echo $user->last_name; ?>" class="required" /></td>
			<td><span class="error" id="last_name_error"><span>(required)</span></span></td>
		</tr>
		<tr>
			<th><label for="">Email</label></th>
			<td><input type="text" name="email" id="email" value="<? echo $user->email; ?>" class="required" /></td>
			<td><label class="error" for="email" id="email_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="">Phone</label></th>
			<td><input type="text" name="phone" id="phone" value="<? echo $user->phone_number; ?>" class="phone" /></td>
			<td><label class="error" for="phone" id="phone_error"></label></td>
		</tr>
		<tr>
			<th><label for="">Department</label></th>
			<td><select name="department" id="department" class="required">
				<option value=""></option>
				<? foreach($_dept->find_all() as $dept): ?>
				<option <? selected($user->department, $dept->id); ?> value="<? echo $dept->id; ?>"><? echo $dept->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label class="error" for="department" id="department_error"><span>(required)</span></label>
		</tr>
		<tr>
			<th><label for="">Group</label></th>
			<td><select name="group" id="group" class="required">
				<option value=""></option>
				<? foreach(Group::find_all("groups", "", "name ASC") as $group): ?>
				<option <? selected($user->group_id, $group->id); ?> value="<? echo $group->id; ?>"><? echo $group->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label class="error" for="group" id="group_error"><span>(required)</span></label></td>		
		</tr>
	</table>
</form>
	
<? endforeach; ?>

<div class="controls">
	<a class="button inactive" href="user.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="SaveAll('user')">Save All</a>
</div>
<div class="clear"></div>