<?
	// instantiate objects
	$_dept = new Department();
	$count = (string) array_shift($db->count('cms.departments'));
	
	$pages = new Paginator;
	$pages->items_total = ($count > 0) ? $count : 1;
	$pages->mid_range = 9;
	$pages->paginate();
?>
<? if($_dept->find_all()): ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tabulardata').tablesorter({
			headers: {0:{sorter:false}},
			sortList: [[1,0]],
			widgets: ['zebra']
		});
		$('input#search_table').quicksearch('table#tabulardata tbody tr', {
			'delay':'200',
			'loader':'#loader',
			'stripeRows': ['odd', 'even']
		});
	});
</script>
<? endif; ?>
<h2 class="tab-department">Departments <span><? echo _p($count,'department'); ?></span></h2>
<div id="alert-box">&nbsp;</div>
<div id="search">
	<div class="container">
		<label for="search_table">Search</label>
		<input disabled="disabled" type="text" id="search_table" value="" />
		<img id="clearsearch" src="images/clear.gif" alt="clear" />
		&nbsp;&nbsp;&nbsp;
		<label for="show">Show</label>
		<? echo $pages->display_items_per_page(); ?>
	</div>
</div>
<div class="controls">
	<? if(Group::can('add_department')): ?>
	<a class="button" href="department.php?action=add">New Department</a>
	<? endif; ?>
</div>
<div class="tableFull" style="margin-top:0;">
	<table id="tabulardata" class="tablesorter selector list" cellpadding="10" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>Department</th>
				<th>Shortcode</th>
				<th>Subdirectory</th>
			</tr>
		</thead>
		<tbody>
			<? foreach($_dept->find_all($pages->limit) as $dept): ?>
			<tr id="dept-<? echo $dept->id ?>">
				<td style="width:25px;"><input type="checkbox" name="select" value="selected-<? echo $dept->id ?>" /</td>
				<td>
					<a href="department.php?action=edit&id=<? echo $dept->id ?>"><span class="bold"><? echo $dept->name; ?></span></a>
				</td>
				<td>
					<? echo $dept->code; ?>
				</td>
				<td>
					<a target="_blank" href="http://medicine.missouri.edu/<? echo $dept->subdir; ?>/"><? echo $dept->subdir; ?></a>
				</td>
			</tr>
			<? endforeach; ?>
			
			<? if(!$_dept->find_all()): ?>
			<tr><td colspan="3"><span class="bold">Nothing here to see</span></td></tr>
			<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>Department</th>
				<th>Shortcode</th>
				<th>Subdirectory</th>
			</tr>
		</tfoot>
	</table>
</div><!-- #editor -->
<div class="controls" style="float:left;">
	<select id="modifier">
		<option value="">Bulk Options</option>
		<option value="">---</option>
		<option value="edit">Edit</option>
		<option value="delete">Delete</option>
	</select>
	<button type="button" onclick="ApplySelectedTo('department')">Apply</button>
</div>

<ul id="pagination-clean">
	<? echo $pages->display_pages(); ?>
</ul>
<div class="clear"></div>