		<div id="tools">
			<div id="toolMessages" class="tool">
				<div>
					<h1>Messages <span>0</span></h1>
					<div id="alert-msg">
						<?php echo output_message($message); ?>
						No messages
					</div>
				</div>
				<span class="close" onclick="ShowTools('toolMessages')">&nbsp;</span>
			</div>
			<div id="toolHelp" class="tool">
				<div>
					<h1>Office of Communications</h1>
					<p>1 Hospital Dr, DC018.00, Columbia, MO 65212</p>
					<table id="contact" cellspacing="0">
						<tr>
							<td>
								<ul>
									<li><a href="mailto:ThompsonStev@health.missouri.edu">Steve Thompson &middot; 573.884.6485</a></li>
								</ul>
							</td>
							<td>
								<ul><li><a href="mailto:JenkinsAC@health.missouri.edu">Adam Jenkins &middot; 573.882.0348</a></li></ul>
							</td>
							<td>
								<ul><li><a href="mailto:BoruckiK@health.missouri.edu">Beau Borucki &middot; 573.884.9463</a></li></ul>
							</td>
						</tr>
					</table>
				</div>
				<span class="close" onclick="ShowTools('toolHelp')">&nbsp;</span>
			</div><!-- #toolHelp -->
			<div id="toolDepartment" class="tool">
				<div>
					<h1>Select a Department</h1>
					<table id="contact" cellspacing="0">
						<tr>
						<? $num = 0; ?>
						<? foreach(Department::listAvailable() as $dept): ?>
							<? if(!is_float($num/3)) { echo "</tr><tr>"; } ?>
							<td>
								<ul>
									<? $class = ($dept->id == $_SESSION['department']) ? 'bold' : ''; ?>
									<li onclick="ChangeDepartment(<? echo $dept->id; ?>)" title="<? echo $dept->name; ?>"><span class="<? echo $class; ?>"><? echo $dept->name; ?></span> <small style="color:rgba(85,80,54,.4);">/<? echo $dept->subdir; ?></small></li>
								</ul>
							</td>
							<? $num++; ?>
						<? endforeach; ?>
						</tr>
					</table>
				</div>
				<span class="close" onclick="ShowTools('toolDepartment')">&nbsp;</span>
			</div>
		</div><!-- #tools -->
		
		<? include('toolmenu.php'); ?>