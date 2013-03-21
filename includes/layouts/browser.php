<?

$action = $_GET['a'];

switch($action) {
	case 'open':
		$do = 'Open';
		$btn = 'Select';
		break;
	case 'extract':
		$btn = 'Extract Here';
		$do = 'Extract to';
		break;
}

?>
<div id="filebrowser" class="open">
	<div class="container">
		<h1 class="title"></h1>
		<form>
			<p><? echo $do; ?>: 
				<select id="location" name="location">
					<option value="/">/</option>
					<option value="var/">var/</option>
					<option value="www/">www/</option>
					<option value="html/">html/</option>
					<option selected="selected" value="medicine.missouri.edu/">medicine.missouri.edu/</option>
				</select>
			</p>
		</form>
		<div class="browser">
			&nbsp;
		</div>
		<div class="buttons">
			<button type="button" id="d-new-folder" class="new-folder">New Folder</button>
			<button type="button" id="d-<? echo $action; ?>" class="okay"><? echo $btn; ?></button>
			<button type="button" id="d-cancel" class="cancel">Cancel</button>
		</div>
		<div id="folder-window">
			<h1 class="title">New Folder</h1>
			<p>Name of New Folder:<br /><input type="text" name="folder" id="folder" /></p>
			<div class="buttons">
				<button type="button" id="f-create" class="extract">Create</button>
				<button type="button" id="f-cancel" class="cancel">Cancel</button>
			</div>
		</div>
	</div><!-- .container -->
</div>