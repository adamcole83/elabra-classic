<?
	// instantiate object
	$post = new Content();
	$post->department = $_SESSION['department'];
	$count = $post->count('article');
	
	$pages = new Paginator;
	$pages->items_total = ($count > 0) ? $count : 1;
	$pages->mid_range = 9;
	$pages->paginate();
?>
<? if($post->find_all('article')): ?>
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

<h2 class="tab-news">News & Events <span>Today is <? echo date('l, F j, Y') ?> &nbsp;&middot;&nbsp; <? echo _p($count,'article'); ?></span></h2>
<div id="alert-box">&nbsp;</div>

<div id="search">
	<div class="container">
		<label for="search_table">Search</label>
		<input disabled="disabled" type="text" id="search_table" name="search" value="" />
		<img id="clearsearch" src="images/clear.gif" alt="clear" />
		&nbsp;&nbsp;&nbsp;
		<label for="show">Show</label>
		<? echo $pages->display_items_per_page(); ?>
	</div>
</div>

<div class="controls">
	<a class="button" href="news.php?action=add">New Article</a>
</div>

<div class="tableFull">
	<table id="tabulardata" class="selector" cellspacing="0">
		<thead>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th style="width:600px;">Article</th>
				<th>Created By</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<? foreach($post->find_all('article',$pages->limit) as $article): ?>
			<tr id="article-<? echo $article->id; ?>" class="article-item">
				<td><input type="checkbox" name="select" value="selected-<? echo $article->id; ?>" /></td>
				<td style="width:600px;">
					<h4><a href="news.php?action=edit&id=<? echo $article->id; ?>"><? echo _t($article->title,110); ?></a></h4>
					<p><? echo _t($article->body, 200); ?></p>
				</td>
				<td><? echo User::get($article->updatedBy)->username; ?></td>
				<td>
					<? echo _d($article->post_created); ?><br />
					<? echo ucfirst($article->status); ?>
				</td>
			</tr>
			<? endforeach; ?>
			
			<? if(!$post->find_all('article')): ?>
			<tr><td colspan="4"><span class="bold">No news found</span></td></tr>
			<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th style="width:600px;">Article</th>
				<th>Created By</th>
				<th>Date</th>
			</tr>
		</tfoot>
	</table>
</div>
<div class="controls" style="float:left;">
	<select id="modifier">
		<option value="">Bulk Options</option>
		<option value="">---</option>
		<option value="publish">Publish</option>
		<option value="edit">Edit</option>
		<option value="delete">Delete</option>
	</select>
	<button type="button" onclick="ApplySelectedTo('media')">Apply</button>
</div>
<ul id="pagination-clean">
	<? echo $pages->display_pages(); ?>
</ul>
<div class="clear"></div>