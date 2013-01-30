<?

// instantiate objects
$_dept = new Department();
$_content = new Content();

// set identifiers
$_content->id = $_GET['id'];

// load fresh data for display
$content = $_content->get($_GET['id']);
$_dept->id = $content->department;
$page_url = DOMAIN.'/'.$_dept->get()->subdir.'/'.$content->url.'.html';

$change_permalink = Group::can('change_permalink');

?>
<script type="text/javascript">
	$(function() {
		var save = function mceSave() {
			Publish('page.<?php echo $content->id; ?>');
			return true;
		}
		LoadMCE(save);
		
		var helpnotes = {
			1: 'No need to click save every time you want to preview your changes! Look for the the Preview (<img style="display:inline-block;" src="images/preview-ss.png" alt="preview-ss" width="20" height="19" />) button for a quick view of your changes!',
			2: 'You can insert predefined content by clicking the Template (<img style="display:inline-block;" src="images/template-ss.png" alt="preview-ss" width="20" height="19" />) button.'
		};
		
		$('#note').html(helpnotes[pickRandomProperty(helpnotes)]);
		
		$('input#title').bind('blur',function() {
			if(this.value == '') {
				return false;
			}
			var slug = Slug(this.value)
				dir = '<?php echo Department::grab($_SESSION['department'])->subdir ?>',
				inputURL = document.getElementById('url'),
				inputGUID = document.getElementById('guid')
			;
			if(inputURL.value == '') {
				inputURL.value = slug;
			}
			if(inputGUID.value == '') {
				inputGUID.value = 'http://medicine.missouri.edu/'+dir+'/'+slug+'.html';
			}
		});
		$('input#url').bind('blur', function() {
			if(this.value == '') {
				return false;
			}
			var dir = '<?php echo Department::grab($_SESSION['department'])->subdir ?>',
				guid = document.getElementById('guid')
			;
			if(guid.value != 'http://medicine.missouri.edu/'+dir+'/'+this.value+'.html') {
				guid.value = 'http://medicine.missouri.edu/'+dir+'/'+this.value+'.html';
			}
		});
		
/*
		window.onbeforeunload = function() {
			if(tinyMCE.activeEditor.isDirty()) {
				return "The changes you made will be lost if you navigate away from this page.";
			}else{
				return null;
			}
		};
*/
	});
</script>

<div class="floatRight">
<a href="<?php echo $page_url; ?>" target="_blank" class="button inactive">View Page</a>
<?php if($_SESSION['user_id'] == 3): ?>
<a href="javascript:void();" onclick="LoadUploader();" class="button inactive">Upload Media</a>
<?php endif; ?>
</div>
<h2 class="tab-pages">
	<?php echo $content->title; ?>
</h2>
<div id="alert-box">&nbsp;</div>

<form class="editor">
	<textarea id="body" name="body"><?php echo $content->body; ?></textarea>
	<br />
	<table class="form">
		<tr>
			<td></td>
			<td><h3>Properties</h3></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="title">Title</label></th>
			<td><input type="text" name="title" id="title" value="<?php echo $content->title ?>" class="required" /></td>
			<td><label for="title" class="error" id="title_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="description">Description<br /><span>meta tag</span></label></th>
			<td><textarea name="description" id="description" class="required"><?php echo $content->description ?></textarea></td>
			<td><label for="description" class="error" id="description_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="url">Slug</label></th>
			<td><input type="text" name="url" id="url" value="<?php echo $content->url ?>" class="required" /></td>
			<td><label for="url" class="error" id="url_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="guid">Permalink</label></th>
			<td><input type="text" name="guid" id="guid" value="<?php echo $content->guid; ?>" class="required" /></td>
			<td><label for="guid" class="error" id="guid_error"><span>(required)</span></label></td>
		</tr>
		<tr>
			<th><label for="parent_id">Parent Page</label></th>
			<td><select id="parent_id" name="parent_id">
				<option value="0">(no parent)</option>
				<?php $content->parent_dropdown($content->parent_id); ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="menu_order">Menu Order</label></th>
			<td><input type="text" name="menu_order" id="menu_order" maxlength="2" value="<?php echo $content->menu_order; ?>" style="min-width:30px;width:30px;" /></td>
			
			<input type="hidden" name="updatedBy" value="<?php echo $_SESSION['user_id']; ?>" />
			<input type="hidden" name="post_type" value="post" />
			<input type="hidden" name="department" value="<?php echo $content->department; ?>" />
		</tr>
		<?php if(Group::can('publish_page')): ?>
		<tr>
			<th><label for="status">Status</label></th>
			<td>
				<select name="status" id="status" class="required">
					<option value="draft" <?php selected($content->status, 'draft', false); ?>>Draft</option>
					<option value="published" <?php selected($content->status, 'published', false); ?>>Published</option>
				</select>
			</td>
		</tr>
		<?php else: ?>
		<input type="hidden" name="status" value="<?php echo $content->status; ?>" />
		<?php endif; ?>
	</table>
</form>

<div class="controls" style="float:left;">
	<a class="button inactive" href="javascript:void(0);" onclick="Purge('page.<?php echo $content->id; ?>.redirect')">Delete</a>
</div>
<div class="controls">	
	<a class="button inactive" href="page.php">Done</a>
	<a class="button" id="publish" href="javascript:void(0);" onclick="Publish('page.<?php echo $content->id; ?>')">Save</a>
</div>

<br class="clear" />

<?php if(Group::can('manage_revisions') && $revisions = $_content->get_all_revisions($content->id)): ?>
<div class="box row revisions" style="margin-top: 30px;">
	<div class="box-header">
		Revisions
	</div>
	<div class="box-container">
		<ul>
			<?php foreach($revisions as $revision): ?>
			<li><a href="revisions.php?revision=<?php echo $revision->id; ?>&action=edit"><?php echo time_to_text($revision->post_created); ?></a> by <?php echo User::get($revision->updatedBy)->username; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>
