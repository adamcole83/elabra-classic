<?
	
	// instantiate objects
	$_content = new Content();
	$_dept = new Department();
	
	// set identifiers
	$dept_id = $_SESSION['department'];
	$_dept->id = $dept_id;
	$index_id = $_dept->get()->index_id;
	$_content->department = $dept_id;
	
	$subdir = $_dept->get()->subdir;
	$count = $_content->count();
	
/*
	$pages = new Paginator;
	$pages->items_total = ($count > 0) ? $count : 1;
	$pages->paginate();
*/
?>
<? if($_content->find_all()): ?>
<script type="text/javascript">
	$(function() {		
		$('input#search_table').quicksearch('table#tabulardata tbody tr', {
			'delay':'200',
			'loader':'#loader',
			'stripeRows': ['odd', 'even']
		});
	});
</script>
<? endif; ?>
<h2 class="tab-pages">Pages <span><? echo _p($count,'page'); ?></span></h2>
<div id="alert-box">&nbsp;</div>

<div id="search">
	<div class="container">
		<label for="search_table">Search</label>
		<input type="text" id="search_table" value="" />
		<img id="clearsearch" src="images/clear.gif" alt="clear" />
		&nbsp;&nbsp;&nbsp;
<!-- 		<label for="show">Show</label> -->
		<?// echo $pages->display_items_per_page(); ?>
	</div>
</div>
<div class="controls">
	<? if(Group::can('add_pages')): ?>
	<a class="button" href="page.php?action=add">New Page</a>
	<? endif; ?>
</div>
<div id="legend">
	<ul>
		<?php if(User::can('modify_home_page')): ?>
		<li><img src="images/landing.gif" alt="landing" width="20" height="10" />Main index</li>
		<?php endif; ?>
		<li><img src="images/draft.gif" alt="draft" width="20" height="10" />Draft</li>
		<li><img src="images/nobackup.gif" alt="nobackup" width="20" height="10" />No backup</li>
<!-- 		<li><img src="images/parent.gif" alt="parent" width="20" height="10" />Parent</li> -->
	</ul>
</div>
<div id="page-select" class="tableFull" style="margin-top:0;">					
	<table id="tabulardata" class="selector" cellspacing="0">
		<thead>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>Page</th>
				<th>Date</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<? foreach($_content->display_loop($pages->limit) as $content): ?>
				<?php if($content->id == $index_id && ! User::can('modify_home_page')) continue; ?>
				<tr id="page-<? echo $content->id ?>">
					<td style="width:3%;"><input type="checkbox" name="select" value="selected-<? echo $content->id; ?>" /></td>
					<td>
						<? echo $content->icon('draft') . $content->icon('main'); ?>
						<span><a class="bold" href="page.php?action=edit&id=<? echo $content->id ?>"><?php echo $content->level; ?> <? echo $content->title; ?></a></span>
						<small class="floatRight"><a target="_blank" href="<?php echo $content->guid; ?>">Preview</a></small>
						<br />
						<? echo $content->breadcrumb; ?>
					</td>
					<td style="width:25%;">
						<? echo time_to_text($content->updated); ?><br />Updated by <? echo User::get($content->updatedBy)->username; ?><br />
					</td>
					<td style="width:10%;">
						<? echo ucfirst($content->status); ?>
					</td>
				</tr>
			<? endforeach; ?>
			
			<? if(!$_content->find_all()): ?>
			<tr><td colspan="5"><span class="bold">No pages found</span></td></tr>
			<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>Page</th>
				<th>Date</th>
				<th>Status</th>
			</tr>
		</tfoot>
	</table>
</div><!-- .tableFull -->
<div class="controls" style="float:left;">
	<select id="modifier">
		<option value="">Bulk Options</option>
		<option value="">---</option>
		<option value="publish">Publish</option>
		<option value="edit">Edit</option>
		<option value="delete">Delete</option>
	</select>
	<button type="button" onclick="ApplySelectedTo('page')">Apply</button>
</div>
<ul id="pagination-clean">
	<?// echo $pages->display_pages(); ?>
</ul>
<div class="clear"></div>