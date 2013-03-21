<?
	
	// instantiate objects
	$_dept = new Department();
	$content = new Content();
	
	// set identifiers
	$_dept->id = $_GET['id'];
	$content->department = $_GET['id'];
	
	// load fresh data for display
	$dept = $_dept->get();
	
?>
<style>
	fieldset { padding:0; border:0; }
	.ui-dialog .ui-state-error { padding: .3em; }
</style>

<h2 class="tab-department"><? echo $dept->name; ?></h2>
<div id="alert-box">&nbsp;</div>
<form class="editor">
	<table class="form">
		<tr>
			<th><label for="name">Title</label></th>
			<td><input type="text" name="name" id="name" value="<? echo $dept->name; ?>" class="required" /></td>
			<td><label class="error" for="name" id="name_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="subdir">Public Directory</label></th>
			<td><input type="text" name="subdir" id="subdir" value="<? echo $dept->subdir; ?>" class="required" /></td>
			<td><label class="error" for="subdir" id="subdir_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="index_id">Front Page</label></th>
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
	<a class="button inactive" href="javascript:void(0);" onclick="Relocate(<? echo $dept->id; ?>, '<? echo $dept->subdir; ?>')">Relocate Department</a>
	<a class="button" href="javascript:void(0);" onclick="ListUsers('AssignUser.<? echo $_GET['id']; ?>')">Assign User</a>
	<a class="button" href="javascript:void(0);" onclick="Save('department.<? echo $dept->id; ?>')">Save</a>
</div>
<div class="clear"></div>
<br />
<div class="box row">
	<div class="box-header">
		Menus
	</div>
	<div class="box-container">
		<div class="column size1of3">
			<div class="box widget row" style="margin-right: 10px;">
				<div class="box-header">Custom Menu <button type="button" style="float:right" onclick="menu.bind.click.addcustom();">Add to Menu</button></div>
				<div class="widget-container">
					<form id="add-custom">
						<div class="form-field block">
							<label for="title">Title</label>
							<input type="text" name="title" id="custom-menu-title" />
						</div>
						<div class="form-field block">
							<label for="link">Link</label>
							<input type="text" name="url" id="custom-menu-url" />
						</div>
						<input type="hidden" name="department" id="department" value="<? echo $_GET['id'] ?>" />
					</form>
				</div>
			</div>
			<br />
			<div class="box widget row" style="margin-right: 10px;">
				<div class="box-header">Pages <button type="button" style="float:right" onclick="menu.bind.click.addpage();">Add to Menu</button></div>
				<div class="widget-container">
					<ul id="add-page">
						<? foreach($content->display_loop('', '&nbsp;&nbsp;') as $page): ?>
						<li>
							<?php echo $page->level; ?>
							<input type="checkbox" class="addto" name="add-<? echo $page->id ?>" />
							<span><?php echo $page->title; ?></span>
						</li>
						<? endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="column size2of3 lastcolumn">
			<div class="box" style="margin-left: 10px;">
				<div class="box-header">Menu</div>
				<div class="box-container" style="min-height: 300px;background:white;">
					<ul class="menu ui-sortable" id="menu-to-edit">
						
					</ul>
				</div>
				<div class="controls">
					<button type="button" class="button" style="float:right" onclick="menu.bind.click.savemenu();">Save Menu</button>
				</div>
			</div>
		</div>
	</div>
</div>


<li id="menu-item" class="menu-item" style="display:none;">
	<dl class="menu-item-bar">
		<dt class="menu-item-handle">
			<span class="item-title"></span>
			<span class="item-controls">
				<span class="item-type"></span>
				<a class="item-edit" href="includes/xhr/nav-menus.php?edit-menu-item=">Edit Menu</a>
			</span>
		</dt>	
	</dl>
	<div class="menu-item-settings" id="menu-item-settings-">
		<div class="form-field block field-url">
			<label>URL</label>
			<input type="text" name="menu-item-url" value="" />
		</div>
		<div class="form-field block field-title">
			<label>Navigation Label</label>
			<input type="text" name="menu-item-title" value="" />
		</div>
		<div>
			<input type="hidden" name="menu-item-original-id" value="" />
			<input type="hidden" name="menu-item-parent-id" value="" />
			<input type="hidden" name="menu-item-order" value="" />
			<input type="hidden" name="menu-item-type" value="" />
			<p><a href="" class="delete">Remove</a> | <a href="" class="cancel">Cancel</a></p>
		</div>
	</div>
</li>


<script type="text/javascript">
$(function(){
	menu.init({
		department	: <? echo $_GET['id']; ?>,
		template	: '#menu-item',
		dropzone	: '#menu-to-edit'
	});
});
</script>