<?
	
	// instantiate objects
	$_dept = new Department();
	$content = new Content();
	
	// set identifiers
	$_dept->id = $_GET['id'];
	$content->department = $_GET['id'];
	
	// load fresh data for display
	$dept = $_dept->get();
	
	
	$parents = (array) $content->list_all_parents();
	
	
?>
<style>
	fieldset { padding:0; border:0; }
	.ui-dialog .ui-state-error { padding: .3em; }
</style>
<script type="text/javascript">
	$(function() {
		MenuOrderInit();
	});
</script>
<h2 class="tab-department"><? echo $dept->name; ?></h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="name">Name</label></th>
			<td><input type="text" name="name" id="name" value="<? echo $dept->name; ?>" class="required" /></td>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="code">Code</label></th>
			<td><input type="text" name="code" id="shortcode" value="<? echo $dept->code; ?>" maxlength="4" class="required" /></td>
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
					<option value="" selected="selected" disabled="disabled">No pages found</option>
					<? endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="dev_mode">Developer Mode</label></th>
			<td><p><input type="radio" <? checked($dept->dev_mode,'1') ?> name="dev_mode" value="1" /> On <input type="radio" <? checked($dept->dev_mode,'2') ?> name="dev_mode" value="2" /> Off</p></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td></td>
			<td><h3>Reorder Main Menu <span>[autosave <strong>on</strong>]</span></h3></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<ul id="menu-order">
					<?// if(!$parents): ?>
<!-- 					<li class="empty"><a href="page.php">No menu items found</a></span></li> -->
					<?// else: ?>
						<? for($i=0;$i<7;$i++): ?>
							<? if($parents[$i]->id == $dept->index_id) continue; ?>
							<? if($parents[$i]): ?>
								<li id="<? echo $parents[$i]->id ?>" class="menu-item"><? echo $parents[$i]->title ?><span><? echo _n($parents[$i]->menu_order,$i+1) ?></span></li>
							<? else: ?>
								<li class="empty">Add external link<span><? echo $i+1; ?></span></li>
							<? endif; ?>
						<? endfor; ?>
					<?// endif; ?>
				</ul>
				<div id="remove-menu">
					
				</div>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td></td>
			<td><h3>Members of this Department</h3></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<table class="userlist">
					<? foreach($_dept->getAssocUser($dept->id) as $user): ?>
					<tr id="user-<? echo $user->id; ?>">
						<td><a class="arrow" href="user.php?action=edit&id=<? echo $user->id; ?>"><? echo User::get($user->id)->username ?></a></td>
						<? if($user->code == 'department'): ?>
						<td><button type="button" style="padding:0px;" onclick="RelinquishUser('<? echo $dept->id ?>.<? echo $user->id ?>')">Relinquish</button></td>
						<? endif; ?>
					</tr>
					<? endforeach; ?>
					<? if(!$_dept->getAssocUser($dept->id)): ?>
					<tr><td>No users</td></tr>
					<? endif; ?>
				</table>
			</td>
			<td></td>
		</tr>
	</table>
</form>

<div class="controls" style="float:left">
	<a class="button inactive" href="javascript:void(0);" onclick="Delete('department.<? echo $dept->id; ?>')">Delete</a>
</div>
<div class="controls">
	<a class="button inactive" href="department.php">Done</a>
	<a class="button" href="javascript:void(0);" onclick="ListUsers('AssignUser.<? echo $_GET['id']; ?>')">Assign User</a>
	<a class="button" href="javascript:void(0);" onclick="Save('department.<? echo $dept->id; ?>')">Save</a>
</div>
<div class="clear"></div>

<div id="dialog-form" title="New External Link">
	<form>
		<fieldset>
			<label for="title">Title</label>
			<input type="text" name="menu-title" id="menu-title" /><br />
			<label for="link">Link</label>
			<input type="text" name="menu-link" id="menu-link" />
			<input type="hidden" name="department" id="department" value="<? echo $_GET['id'] ?>" />
		</fieldset>
	</form>
</div>