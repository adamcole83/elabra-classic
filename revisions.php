<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();

	$content = new Content();
	
	$rev_id = $_GET['action'] == 'edit' ? $_GET['revision'] : $_GET['old'];
	
	$revision = Content::find_by_id($rev_id);
	$post = Content::find_by_id($revision->parent_id);
	
	if($_GET['restore']) {
		if($content->restore_to_revision($rev_id)) {
			header('Location: page.php?action=edit&id='.$post->id);
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="//www.w3.org/1999/xhtml">
	<head>
		<title>Dashboard &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
	    	var gCurrentView = "page";
			$(function() {
				$('input[name="left"],input[name="right"]').bind('click', function() {
					
				});
			});
		</script>
	</head>
	<body>
		<? include('includes/tools.php'); ?>
		<div id="header">
			<? include('includes/header.php'); ?>
		</div><!-- #header -->
		<div id="main">
			<div class="container">
				<div id="content" class="">
					
					<?php if($_GET['action'] == 'edit' && $revision = Content::find_by_id($rev_id)): ?>
					<h2 class="tab-pages">Revision for "<a href="page.php?action=edit&id=<?php echo $revision->parent_id; ?>"><?php echo $revision->title; ?></a>" created on <?php echo time_to_text($revision->post_created); ?></h2>
					<div class="clear"></div>
					
					<div class="row" style="margin:30px 0;">
						<div class="row">
							<div class="column size1of4">
								<strong>Title</strong>
							</div>
							<div class="column size3of4">
								<?php echo $revision->title; ?>
							</div>
						</div>
						<div class="row" style="margin-top: 30px;">
							<div class="column size1of4">
								<strong>Content</strong>
							</div>
							<div class="column size3of4">
								<?php echo $revision->body; ?>
							</div>
						</div>
					</div>
					<?php endif; ?>
					
					<?php if($_GET['action'] == 'diff' && $_GET['old'] && $_GET['new']): ?>
					<?php
					$left = Content::find_by_id($_GET['old']);
					$right = Content::find_by_id($_GET['new']);
					?>
					<h2 class="tab-pages">Compare Revisions of "<a href="page.php?action=edit&id=<?php echo $right->parent_id; ?>"><?php echo $right->title; ?></a>"</h2>
					<div class="clear"></div>
					
					<div class="row">
						<script type="text/javascript">
							$(function() {
								$('#diff-old-v span').text('<?php echo time_to_text($left->post_created); ?>');
								$('#diff-new-v span').text('<?php echo time_to_text($right->post_created); ?>');
							});
						</script>
						<?php
						$options = array(
							'ignoreWhitespace' => true
						);
						$diff = new Diff(explode("\n", $left->body), explode("\n", $right->body), $options);
						// Generate an inline diff
						$renderer = new Diff_Renderer_Html_SideBySide;
						echo $diff->render($renderer);
						
						?>
					</div>
					<br class="clear" /><br />
					<?php endif; ?>
					
					<div class="row dboard">
						<h3>Revisions</h3>
						
						<?php if($revisions = $post->get_all_revisions($post->id)): ?>
						<form action="<?php echo $_SERVER['SELF']; ?>" method="get">
							<button class="button" type="submit" name="action" value="diff">Compare Revisions</button>
							<div class="clear"></div>
							<div class="tableFull">
								<table id="tabulardata" class="selector" cellspacing="0">
									<thead>
										<tr>
											<th style="width:12px;">Old</th>
											<th style="width:12px;">New</th>
											<th>Date Created</th>
											<th>Author</th>
											<th style="width:75px;">Actions</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><input type="radio" name="old" value="<?php echo $post->id; ?>" /></td>
											<td><input type="radio" name="new" value="<?php echo $post->id; ?>" checked="checked" /></td>
											<td><a href="page.php?action=edit&id=<?php echo $post->id; ?>"><?php echo time_to_text($post->updated); ?></a> [Current revision]</td>
											<td><?php echo User::get($post->updatedBy)->username; ?></td>
											<td></td>
										</tr>
										<?php $hide = false; ?>
										<?php foreach($revisions as $rev): ?>
										<tr>
											<td><input type="radio" name="old" value="<?php echo $rev->id; ?>" <?php echo $rev->id == $revision->id ? 'checked="checked"':null; ?> /></td>
											
											<td><input type="radio" name="new" value="<?php echo $rev->id; ?>" <?php echo $hide ? 'disabled="disabled"':null; ?> /></td>
											<?php if($rev->id == $revision->id) $hide = true; ?>
											
											<td><a href="revisions.php?revision=<?php echo $rev->id; ?>&action=edit"><?php echo time_to_text($rev->post_created); ?></a></td>
											<td><?php echo User::get($rev->updatedBy)->username; ?></td>
											<td><a href="?action=edit&revision=<?php echo $rev->id; ?>&restore=true">Restore</a></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</form>
						<?php else: ?>
						<div id="alert-box" class="notification">
							<p>No Revisions found.</p>
						</div>
						<?php endif; ?>
					</div>
					
				</div>
			</div><!-- .container -->
		</div><!-- #main -->
		<div id="loader">
			&nbsp;
		</div>
		<!--[if lte IE 7]>
		<div id="bwarn"><p>Please use Mozilla Firefox 3.6+, Google Chrome 9.0+, Apple Safari 5+ or Internet Explorer 8+ or you may risk losing data!</p></div>
		<![endif]-->
	</body>
</html>