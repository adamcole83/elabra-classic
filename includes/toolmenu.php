		<div id="toolmenu" <?php echo defined('ENVIRONMENT') && ENVIRONMENT == 'maintenance' ? 'class="maintenance"':null; ?>>
			<div class="container">
				
				<?php if(defined('ENVIRONMENT') && ENVIRONMENT != 'production'): ?>
				<script type="text/javascript">
				$(function() {
					PulseAnimation($('.pulse'), true, 0, 1000, 600);
				});
				</script>
				<div class="floatLeft pulse"><?php echo strtoupper(ENVIRONMENT); ?> MODE ACTIVE</div>
				
				BRANCH: <?php echo sc_branch(); ?>
				
				<?php endif; ?>
<!-- 				<span onclick="ShowTools('toolMessages')"><span class="msgcount">0</span> Messages</span> -->
				<span onclick="ShowTools('toolDepartment')">Change Department</span>
<!-- 				<span onclick="ShowTools('toolHelp')">Contact Support</span> -->
				<span><a href="login.php?do=logout">Logout</a></span>
			</div><!-- .container -->
		</div><!-- #toolmenu -->
