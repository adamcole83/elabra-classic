<div id="menu">
	<ul>
		<li id="tabdashboard"><a href="index.php">Dashboard</a></li>
		<li id="tabpage"><a href="page.php">Pages</a></li>
		<li id="tabmedia"><a href="media.php">Media</a></li>

		<? if(Group::can('manage_news')): ?>
		<li id="tabnews"><a href="news.php">News</a></li>
		<? endif; ?>

		<? if(Group::can('manage_calendar')): ?>
		<!-- <li id="tabcalendar"><a href="calendar.php">Calendar</a></li> -->
		<? endif; ?>

		<? if(Group::can('view_departments')): ?>
		<li id="tabdepartment"><a href="department.php">Departments</a></li>
		<? endif; ?>

		<? if(Group::can('view_users')): ?>
		<li id="tabuser"><a href="user.php">Users</a></li>
		<? endif; ?>

		<? if(Group::can('deploy_applications')): ?>
		<!-- <li id="tabdeploy"><a href="deploy.php">Deploy</a></li> -->
		<? endif; ?>

		<? if(Group::can('package_applications')): ?>
		<!-- <li id="tabpack"><a href="pack.php">Package</a></li> -->
		<? endif; ?>

		<li id="help"><a href="help.php">Help</a></li>
	</ul>
</div><!-- #menu -->
