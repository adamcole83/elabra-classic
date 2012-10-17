<?

// instantiate objects
$_user = new User();
$_dept = new Department();

// set identifiers
$_user->id = $_GET['id'];

// load fresh data for display
$user = $_user->get($_GET['id']);

?>
<h2 class="tab-user"><? echo $user->first_name." ".$user->last_name; ?></h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for=""></label></th>
			<td><input type="radio" name="active" value="1" <? checked($user->active, '1', true); ?> /> Active &nbsp;&nbsp;
				<input type="radio" name="active" value="0" <? checked($user->active, '0', true); ?> /> Inactive
			</td>
		</tr>
		<tr>
			<th><label for="">First Name</label></th>
			<td><input type="text" id="first_name" name="first_name" value="<? echo $user->first_name; ?>" class="required" /></td>
			<td><label class="error" for="first_name" id="first_name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="">Last Name</label></th>
			<td><input type="text" id="last_name" name="last_name" value="<? echo $user->last_name; ?>" class="required" /></td>
			<td><label class="error" for="last_name" id="last_name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="">Email</label></th>
			<td><input type="text" name="email" id="email" value="<? echo $user->email; ?>" class="required" /></td>
			<td><label class="error" for="email" id="email_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="">Phone</label></th>
			<td><input type="text" name="phone_number" id="phone" value="<? echo $user->phone_number; ?>" class="phone" /></td>
			<td><label class="error" for="phone" id="phone_error"></label></td>
		</tr>
		<tr>
			<th><label for="">Department</label></th>
			<td><select name="department" id="department" class="required">
				<? foreach($_dept->find_all() as $dept): ?>
				<option <? selected($user->department, $dept->id); ?> value="<? echo $dept->id; ?>"><? echo $dept->name; ?></option>
				<? endforeach; ?>
				<? if(!$_dept->find_all()): ?>
				<option selected="selected" disabled="disabled" value="">No departments found</option>
				<? endif; ?>
			</select></td>
			<td><label class="error" for="department" id="department_error"><span>(required)</span></label>
		</tr>
		<tr>
			<th><label for="group_id">Group</label></th>
			<td><select name="group_id" id="group_id" class="required">
				<option value=""></option>
				<? foreach(Group::find_all("groups", "", "name ASC") as $group): ?>
				<option <? selected($user->group_id, $group->id); ?> value="<? echo $group->id; ?>"><? echo $group->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label class="error" for="group_id" id="group_id_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="auth_type">Auth Type</label></th>
			<td>
				<select name="pawprintuser" id="auth_type" class="required">
					<option <? selected($user->pawprintuser, 0); ?> value="0">Database</option>
					<option <? selected($user->pawprintuser, 1); ?> value="1">LDAP</option>
				</select>
			</td>
			<td><label class="error" for="auth_type" id="auth_type_error"><span>(required)</span></label></td>
		</tr>
		<div id="password-container">
			<tr>
				<th><label for="password">Password <small>Twice</small></label></th>
				<td><input type="password" name="password" id="password" value="" class="password" /></td>
				<td><label class="error" for="password" id="password_error"></label></td>
			</tr>
			<tr>
				<th><label for="passwordconfirm"></label></th>
				<td><input type="password" name="passwordconfirm" id="passwordconfirm" value="" /></td>
			</tr>
		</div>
		<tr>
			<th><label for="sendto">Send temporary password to user?</label></th>
			<td><input type="radio" name="sendto" id="sendto-yes" value="1" /> Yes <input type="radio" name="sendto" id="sendto-no" value="0" checked="checked" /> No &nbsp;&nbsp;
				<small>If yes, temporary password will be generated.</small>
			</td>
		</tr>
		<input type="hidden" name="username" value="<?php echo $user->username; ?>" />
	</table>
</form>

<div class="controls" style="float:left;">
	<a class="button inactive" href="javascript:void(0);" onclick="Purge('user.<? echo $user->id; ?>.redirect')">Delete</a>
</div>
<div class="controls">
	<a class="button inactive escape" href="user.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="Save('user.<? echo $user->id; ?>')">Save</a>
</div>	


<script type="text/javascript">
	$('#sendto-yes').click(function() {
		$('input#password, input#passwordconfirm').val(randomString());
	});
	$('#auth_type').change(function() {
		switch($(this).val())
		{
			case '1':
				$('#sendto-no').attr('checked', 'checked');
				$('input#password, input#passwordconfirm').val('');
				break;
		}
	});
</script>

