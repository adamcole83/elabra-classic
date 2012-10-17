<h2 class="tab-user">New User</h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="username">Username</label></th>
			<td><input type="text" id="username" name="username" value="" class="required username" /></td>
			<td><label class="error" for="username" id="username_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="first_name">First Name</label></th>
			<td><input type="text" id="first_name" name="first_name" value="" class="required" /></td>
			<td><label class="error" for="first_name" id="first_name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="last_name">Last Name</label></th>
			<td><input type="text" id="last_name" name="last_name" value="" class="required" /></td>
			<td><label class="error" for="last_name" id="last_name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="email">Email</label></th>
			<td><input type="text" name="email" id="email" value="" class="required email" /></td>
			<td><label class="error" for="email" id="email_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="phone_number">Phone</label></th>
			<td><input type="text" name="phone_number" id="phone" value="" class="phone" /></td>
			<td><label class="error" for="phone" id="phone_error"></label></td>
		</tr>
		<tr>
			<th><label for="department">Department</label></th>
			<td><select name="department" id="department" class="required">
				<? if(!Department::find_all()): ?>
				<option selected="selected" disabled="disabled" value="">No departments found</option>
				<? endif; ?>
				<option value=""></option>
				<? foreach(Department::find_all() as $dept): ?>
				<option value="<? echo $dept->id; ?>"><? echo $dept->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label class="error" for="department" id="department_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="group_id">Group</label></th>
			<td><select name="group_id" id="group_id" class="required">
				<? if(!Group::find_all("groups", "", "name ASC")): ?>
				<option selected="selected" disabled="disabled" value="">No groups found</option>
				<? endif; ?>
				<option value=""></option>
				<? foreach(Group::find_all("groups", "", "name ASC") as $group): ?>
				<option <? echo ($group->name == 'Editor') ? 'selected="selected"':''; ?> value="<? echo $group->id; ?>"><? echo $group->name; ?></option>
				<? endforeach; ?>
			</select></td>
			<td><label class="error" for="group_id" id="group_id_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="auth_type">Auth Type</label></th>
			<td>
				<select name="auth_type" id="auth_type" class="required">
					<option value="0">Database</option>
					<option value="1">LDAP</option>
				</select>
			</td>
			<td><label class="error" for="auth_type" id="auth_type_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="password">Password <small>Twice</small></label></th>
			<td><input type="password" name="password" id="password" value="" class="required password" /></td>
			<td><label class="error" for="password" id="password_error"></label></td>
		</tr>
		<tr>
			<th><label for="passwordconfirm"></label></th>
			<td><input type="password" name="passwordconfirm" id="passwordconfirm" value="" /></td>
		</tr>
		<tr>
			<th><label for="sendto">Send temporary password to user?</label></th>
			<td><input type="radio" name="sendto" id="sendto-yes" value="1" /> Yes <input type="radio" name="sendto" id="sendto-no" value="0" checked="checked" /> No &nbsp;&nbsp;
				<small>If yes, temporary password will be generated.</small>
			</td>
		</tr>
	</table>
</form>

<div class="controls">
	<a class="button inactive" href="user.php">Cancel</a>
	<a class="button" href="javascript:void(0);" onclick="Save('user.redirect')">Save</a>
</div>
<div class="clear"></div>


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
