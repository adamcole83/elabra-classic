<?
	// instantiate new objects
	$_user = new User();
	$_group = new Group();
	$count = (string) array_shift($db->count('cms.users'));
	$online = (string) array_shift($db->count('cms.users', array('online'=>1)));
	
	$pages = new Paginator;
	$pages->items_total = ($count > 0) ? $count : 1;
	$pages->mid_range = 9;
	$pages->paginate();
	
?>
<? if(Group::find_all('permissions') && Group::find_all() && $_user->find_all()): ?>
<script type="text/javascript">
	$(function() {
		$('#tabulardata').tablesorter({
			headers: {0:{sorter:false},4:{sorter: false},5:{sorter:false}},
			sortList: [[1,0]],
			widgets: ['zebra']
		});
		$('input#search_table').quicksearch('table#tabulardata tbody tr', {
			'delay':'200',
			'loader':'#loader',
			'stripeRows': ['odd', 'even']
		});
		
		$('.nano').each(function() {
			$(this).nanoScroller();
		});
		
	});
</script>
<? endif; ?>
<h2 class="tab-user">Users <span><? echo _p($count,'user'); ?></h2>
<div id="alert-box">&nbsp;</div>

<div id="search">
	<div class="container">
		<label for="search_table">Search</label>
		<input type="text" id="search_table" name="search" value="" />
		<img id="clearsearch" src="images/clear.gif" alt="clear" />
		&nbsp;&nbsp;&nbsp;
		<label for="show">Show</label>
		<? echo $pages->display_items_per_page(); ?>
	</div>
</div>
<div class="controls">
	<? if(Group::can('add_users')): ?>
	<a class="button" href="user.php?action=add">New User</a>
	<? endif; ?>
</div>

	<div class="tableFull" style="margin-top:0px">
		<table id="tabulardata" class="selector" cellspacing="0">
			<thead>
				<tr>
					<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
					<th>Name</th>
					<th>Role</th>
					<th>Department</th>
					<th>Last Login</th>
					<th>Tools</th>
				</tr>
			</thead>
			<tbody>
				<? foreach($_user->find_all($pages->limit) as $user): ?>
				<tr id="user-<? echo $user->id ?>">
					<td style="width:25px;"><input type="checkbox" name="select" value="selected-<? echo $user->id; ?>" /></td>
					<td>
						<span class="bold floatLeft">
							<a href="user.php?action=edit&id=<? echo $user->id; ?>">
								<? if($user->active == '1'): ?>
								<img src="images/user_active.png" class="user-status" alt="User active" title="User is active" width="16" height="16" />
								<? else: ?>
								<img src="images/user_inactive.png" class="user-status" alt="User inactive" title="User is inactive" width="16" height="16" />
								<? endif; ?>
								<? echo ucwords($user->first_name." ".$user->last_name); ?>
							</a>
						</span>
					</td>
					<td><span onclick="SetSearch(this)"><? echo Group::get_name($user->group_id)->name; ?></span></td>
					<td><span onclick="SetSearch(this)"><? echo Department::grab($user->department)->name; ?></span></td>
					<?php
					$time = strtotime($user->prev_login);
					$time = ($time > 0) ? date('D M j, Y g:i a', $time) : 'Never';
					?>
					<td><?php echo $time; ?></td>
					<td><img src="images/iconTrash.png" alt="iconTrash" width="12" height="12" onclick="Purge('user.<? echo $user->id; ?>')" /></td>
				</tr>
				<? endforeach; ?>
				
				<? if(!$_user->find_all()): ?>
				<tr><td colspan="5"><span class="bold">No users found</span></td></tr>
				<? endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
					<th>Username</th>
					<th>Name</th>
					<th>Role</th>
					<th>Department</th>
					<th>Tools</th>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="controls" style="float:left;">
		<select id="modifier">
			<option value="">Bulk Options</option>
			<option value="">---</option>
			<option value="edit">Edit</option>
			<option value="delete">Delete</option>
		</select>
		<button type="button" onclick="ApplySelectedTo('user')">Apply</button>
	</div>
	<ul id="pagination-clean">
		<? echo $pages->display_pages(); ?>
	</ul>

<div id="access">
	<div id="groups" class="nano">
		<div class="tableFull content">
			<table class="selector" cellspacing="0">
				<thead>
					<tr>
						<th><span title="Expand">Groups</span> <span><a href="user.php?action=add_group"><img class="floatRight" src="images/iconAdd.png" alt="Add" title="Add Group" /></a></span></th>
					</tr>
				</thead>
				<tbody>
					<? foreach(Group::find_all('groups', '', 'name ASC') as $group):$counter++; $class = is_float($counter/2) ? "odd" : null; ?>
					<tr class="<? echo $class; ?>">
						<td>
							<span class="bold"><a href="user.php?action=edit_group&id=<? echo $group->id; ?>"><? echo $group->name; ?></a></span>
							<a href="user.php" onclick="Purge('group.<? echo $group->id; ?>')"><img class="floatRight" src="images/iconTrash.png" alt="Delete" title="Delete" width="12" height="12" /></a>
						</td>
					</tr>
					<? endforeach; ?>
					
					<? if(!Group::find_all()): ?>
					<tr><td colspan="2"><span class="bold">&nbsp;&nbsp;&nbsp;No groups found</span></td></tr>
					<? endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div id="perms" class="nano">
		<div class="tableFull content">
			<table class="selector" cellspacing="0">
				<thead>
					<tr>
						<th><span title="Expand">Permissions</span> <span><a href="user.php?action=add_permission"><img class="floatRight" src="images/iconAdd.png" alt="Add" title="Add Permission" /></a></span></th>
					</tr>
				</thead>
				<tbody>
					<? foreach(Group::find_all('permissions', array("group_id" => 0), 'name ASC') as $permission):
					$counter++; $class = is_float($counter/2) ? "odd" : null; ?>
					<tr class="<? echo $class; ?>">
						<td>
							<? echo $permission->name; ?>
							<a href="user.php" onclick="Purge('permission.<? echo $permission->id; ?>')"><img class="floatRight" src="images/iconTrash.png" alt="Delete" title="Delete" width="12" height="12" /></a>
						</td>
					</tr>
					<? endforeach; ?>
					
					<? if(!Group::find_all('permissions')): ?>
					<tr><td colspan="2"><span class="bold">No permissions found</span></td></tr>
					<? endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
