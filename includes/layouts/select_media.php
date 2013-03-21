<?
	$post = new Content();
	$post->department = $_SESSION['department'];
	$count = $post->count('attachment');
	
	$dirsize = 0;
	foreach($post->find_all('attachment') as $attachment){
		$dirsize += @filesize(PUBLIC_ROOT.DS.$attachment->url);
	}
	
	$pages = new Paginator;
	$pages->items_total = ($count > 0) ? $count : 1;
	$pages->mid_range = 9;
	$pages->paginate();
	
?>
<? if($post->find_all('attachment')): ?>
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

<h2 class="tab-media">Media Library <span><? echo _p($count,'file'); ?> &nbsp;&middot;&nbsp; <? echo sizeFormat($dirsize); ?></span></h2>

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
	<a class="button" href="media.php?action=upload">Upload New</a>
</div>

<div class="tableFull">
	<table id="tabulardata" class="tablesorter selector" cellpadding="10" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>File</th>
				<th style="width:50px;">Size</th>
				<th style="width:230px;">Uploaded</th>
			</tr>
		</thead>
		<tbody>
			<? foreach($post->find_all('attachment',$pages->limit) as $attachment): ?>
			<? if(preg_match('/rotatingbanner/i', $attachment->title) && !User::can('manage_banners')) continue; ?>
			<tr id="media-<? echo $attachment->id; ?>" class="media-item">
				<td><input type="checkbox" name="select" value="selected-<? echo $attachment->id; ?>" /></td>
				<td style="width:600px;">
					<p class="media-image">
						<? if(ext2type(file_extension($attachment->url)) != 'image'): ?>
						<a href="media.php?action=edit&id=<? $attachment->id; ?>"></a><img src="<? echo type_icon($attachment->post_mime_type); ?>" title="<? echo $attachment->title ?>" /></a>
						<? else: ?>
						<a href="media.php?action=edit&id=<? $attachment->id; ?>"></a><img src="includes/thumb.php?f=<? echo urlencode(PUBLIC_ROOT.$attachment->url) ?>&width=60&height=60" title="<? echo $attachment->title ?>" /></a>
						<? endif; ?>
					</p>
					<a href="media.php?action=edit&id=<? echo $attachment->id; ?>"><span id="media-title" class="bold"><? echo $attachment->title; ?></span> (<? echo basename($attachment->url); ?>)</a><br />
					<span id="media-type" onclick="SetSearch(this)"><? echo strtoupper(ext2type(file_extension($attachment->url))); ?></span> - 
					<span id="media-ext" onclick="SetSearch(this)"><? echo strtoupper(file_extension($attachment->url)); ?></span><br />
				</td>
				<td id="media-size"><? echo sizeFormat(@filesize(PUBLIC_ROOT.DS.$attachment->url)); ?></td>
				<td>
					<? echo time_to_text($attachment->post_created); ?><br />
					<? echo User::get($attachment->updatedBy)->username; ?>
				</td>
			</tr>
			<? endforeach; ?>
			
			<? if(!$post->find_all('attachment')): ?>
			<tr><td colspan="4"><span class="bold">No media found</span></td></tr>
			<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th><input type="checkbox" name="select_all" onclick="SelectAll(this)" /></th>
				<th>File</th>
				<th style="width:50px;">Size</th>
				<th style="width:230px;">Uploaded</th>
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
	<button type="button" onclick="ApplySelectedTo('media')">Apply</button>
</div>
<ul id="pagination-clean">
	<? echo $pages->display_pages(); ?>
</ul>
<div class="clear"></div>
