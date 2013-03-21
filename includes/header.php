			<div class="container">
				<h1>Manage Content for <span id="dept"><? echo Department::grab($_SESSION['department'])->name; ?></span></h1>
				<div id="summary">
					You're currently logged in as <span><? echo $session->user()->username; ?></span> and your access level is <span><? echo Group::get_name($session->user()->group_id)->name; ?></span>.
				</div><!-- #summary -->
				
				<? include('navigation_tabs.php'); ?>
			</div><!-- .container -->
