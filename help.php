<?
require_once('includes/initialize.php');
check_session();
$message = $session->message();
/* 	include('includes/uptime.php'); */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
	<title>Help &amp; Support &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
	<? include('includes/head.php'); ?>

	<script type="text/javascript">
		var gCurrentView = "<? echo get_page_info('view'); ?>";
			/*
$(function() {
				$('#uptime').serverUptime({ upSeconds: <? echo $uptimeSecs; ?> });
			});
*/
</script>
<link href="https://d3jyn100am7dxp.cloudfront.net/assets/widget_embed.cssgz?1335938180" media="screen" rel="stylesheet" type="text/css" />
<!--If you already have fancybox on the page this script tag should be omitted-->
<script src="https://d3jyn100am7dxp.cloudfront.net/assets/widget_embed_libraries.jsgz?1335938181" type="text/javascript"></script>

<style type="text/css" media="screen">
	h3:not(:first-of-type) {
		margin-top: 20px;
		padding-top: 20px;
		padding-bottom: 5px;
		border-bottom: 1px solid #ccc;
	}
</style>

</head>
<body>
	<? include('includes/tools.php'); ?>
	<div id="header">
		<? include('includes/header.php'); ?>
	</div><!-- #header -->
	<div id="main">
		<div class="container">
			<div id="content">
				<h2 class="tab-help">Help &amp; Support <!-- <span id="uptime"><?php echo $staticUptime; ?></span> --></h2>
				<div id="alert-box" <?php if($message): ?>style="display:block;"<?php endif; ?>><p><?php echo $message; ?></p></div>

				<h3>Table of Contents</h3>

				<ol>
					<li><a href="#what-is-cms">What is a content management system?</a></li>
					<li><a href="#faqs">Frequently Asked Questions</a></li>
					<li><a href="#logging-in">Logging in</a></li>
					<li><a href="#dashboard">The Dashboard Tab</a></li>
					<li><a href="#pages">The Pages Tab</a></li>
					<li><a href="#editor">The Page Editor</a></li>
					<li><a href="#toolbar">The Toolbar</a></li>
					<li><a href="#media">The Media Tab</a></li>
				</ol>

				<h3 id="what-is-cms">What is a content management system?</h3>
				<p>A content management system (CMS) is a web-based software system that provides authoring, collaboration, and administrative tools designed to allow users with beginner-level knowledge of web markup languages to create and manage website content with relative ease. A CMS also provides the foundation for collaboration, giving users the ability to manage documents and images while allowing multiple authors editing and participation rights.</p>

				<h3 id="faqs">Frequently Asked Questions</h3>
				<h4>How do I make something into a link?</h4>
				<p>Highlight a selection of text, or a picture, and click the “Insert/Edit Link” button ( IMG ) on the toolbar in the editing panel. From here, you may either paste the URL you want the link to go to into the “Link URL” field, or use the drop-down menu in the “Link List” field to link to a page in the directory, or to a document that has been uploaded to the media manager.</p>

				<h4>How do I add images to a webpage?</h4>
				<p>Your image must first be uploaded to the Media Library. To do this, click on the “Media” tab at the top of the screen, and press the “Upload New” button in the top right. <strong>It is recommended that your image be the desired size before you upload it.</strong></p>
				<p>Then, open the page you would like to insert the picture into from the “Pages” tab. Click on “Insert/Edit Image” in the toolbar ( IMG ) and find the image you just uploaded in the “Image List” drop down menu. After choosing the image, click on the “Appearance” tab and select either “Left” or “Right” to align your image if you need to, and set the Vertical and Horizontal space to 5. This creates margins so that text or other page elements are not flush with your image. If you need to tweak the size of your image, you can adjust the dimensions from the Appearance tab by entering higher or lower number values, but make sure that “Constrain Proportions” is checked first.</p>

				<h4>How do I change the fonts?</h4>
				<p>For editors, fonts cannot be changed - they are "embedded" in the page design (CSS) for all School of Medicine sites in order to maintain a uniform look.</p>

				<h4>Why does text I copy and pasted from Microsoft Word mess up the page?</h4>
				<p>When you paste directly from Word, the Word content is transparently wrapped up in HTML containing XML code, CSS class references and style attributes. Before pasting, press the “Paste as Plain Text” button (  ) and it will remove formatting and only insert the text.</p>

				<h4>Why can’t I change things on the home page?</h4>
				<p>There are different administrative tiers given to users within the content management system. Users granted Editor status are not able to make changes to the homepage directly.</p>

				<h3 id="logging-in">Logging in</h3>
				<p>In order to make changes to web pages within your department, you must first obtain authorization from the Office of Communications. If you would like to request permission to edit pages for yourself or a designated staff member, please contact us at <a href="mailto:mumedicine@health.missouri.edu">mumedicine@health.missouri.edu</a></p>

				<p>There are multiple levels of user clearance, and some have more ability to make changes than others. As an editor, you will not be able to create new pages or delete existing ones, and you will be unable to make changes to the home page without authorization.</p>

				<h4>Your first time logging in</h4>
				<p>To log in to the School of Medicine CMS, sign in <strong>using your valid Pawprint</strong>, via the CMS secure link.</p>

				<p align="center"><img src="images/help/login.png" alt="" /></p>

				<h3 id="dashboard">The Dashboard Tab</h3>
				<p>The Dashboard tab serves as an informational hub that shows the user:</p>
				<ul>
					<li>pages that have recently been updated.</li>
					<li>recently created pages.</li>
					<li>who made changes to pages recently.</li>
					<li>when those changes were made.</li>
				</ul>
				<p>As an editor, you will have access to the dashboard of the department you are overseeing. The ability to make changes to pages in multiple departments is possible only for authorized users.</p>

				<p align="center"><img src="images/help/Dashboard.JPG" alt="" width="800" /></p>

				<h3 id="pages">The Pages Tab</h3>
				<p>After clicking on the Pages tab, you will see a listing of all the pages that are included in your department directory.</p>
				<p>Selecting <strong>“Preview”</strong> will open a new browser tab, allowing you to view the corresponding page as it is currently displayed online.</p>
				<p>The <strong>Status</strong> of the page shows you whether the page is Published or in Draft mode. Pages in Draft mode are not publicly visible.</p>
				<p>The Main index page appears with a gold box next to the title. <strong>This is your homepage.</strong> Only users classified as Developers or Admins are able to make changes to main indexes.</p>
				<p>The checkboxes on the far left correspond to the Bulk Options drop-down, allowing you to publish, edit or delete pages. As an editor, it is recommended that you disregard these options.</p>

				<div id="alert-box" class="info" style="display:block;">
					<p>Selecting the page title opens the Page Editor, which is where your editing work will take place.</p>
				</div>

				<p align="center"><img src="images/help/Pages.JPG" width="800" alt="" /></p>

				<h3 id="editor">The Page Editor</h3>
				<p>After selecting a page to edit, the page content will be visible in the editing panel, which looks like this:</p>

				<p align="center"><img src="images/help/editor.JPG" width="800" alt="" /></p>

				<p>The content in this box is the content included within the body of the page you selected.</p>
				<p>If the changes you want to make are text based, simply highlight the text you’d like to change, type out the changes and press Save. Alterations will not be visible on the live website until you hit save.</p>

				<h4>Properties Panel</h4>

				<p align="center"><img src="images/help/properties.JPG" width="800" alt="" /></p>

				<p>The properties panel contains the <strong>Title</strong> of the page as it is displayed within the CMS and on the internet. The title is the name of a page when you open it in a browser tab, for example.</p>
				<p>The <strong>Description</strong> is a brief abstract of the content contained on the page and is useful information for search engines.</p>
				<p><strong>Slug</strong> and <strong>Permalink</strong> should remain unchanged for pages that have already been created. Changing either of these two fields will cause links leading to that page to stop working.</p>
				<p><strong>Parent Page</strong> and <strong>Menu Order</strong> are organizational parameters and should remain unchanged.</p>
				<p><strong>Status</strong> changes the page from Draft to Published (publicly visible).</p>
				<p>As an Editor, it is important for the fields in the Properties panel to remain unchanged.</p>
				<div id="alert-box" class="info" style="display:block;">
					<p>Any changes to this panel are likely to cause errors.</p>
				</div>

				<h3 id="toolbar">The Toolbar</h3>

				<p align="center"><img src="images/toolbar.png" alt="toolbar" width="920" height="52" usemap="toolbar" /></p>
				<map name="toolbar">
					<area shape="rect" coords="3,2,25,23" href="#" alt="" class="qtip" title="b" />
					<area shape="rect" coords="25,2,47,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="47,2,69,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="69,2,92,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="96,2,118,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="118,2,150,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="150,2,183,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="187,2,209,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="209,2,231,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="231,2,253,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="253,2,276,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="280,2,302,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="302,2,324,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="324,2,347,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="351,2,373,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="373,2,395,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="395,2,418,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="422,2,445,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="453,2,541,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="545,2,578,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="582,2,604,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="604,2,627,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="631,2,654,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="658,2,680,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="680,2,702,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="702,2,724,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="724,2,747,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="751,2,773,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="773,2,796,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="800,2,822,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="822,2,845,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="849,2,871,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="871,2,893,23" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="893,2,916,23" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="3,28,25,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="25,28,47,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="47,28,69,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="69,28,92,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="96,28,119,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="123,28,145,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="145,28,168,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="172,28,194,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="194,28,216,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="216,28,239,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="243,28,265,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="265,28,287,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="287,28,310,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="314,28,336,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="336,28,359,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="363,28,385,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="385,28,408,49" href="#" alt="" class="qtip" title="" />

					<area shape="rect" coords="412,28,434,49" href="#" alt="" class="qtip" title="" />
					<area shape="rect" coords="434,28,457,49" href="#" alt="" class="qtip" title="" />
				</map>

				<div class="box row" style="margin-bottom:20px;">
					<div class="box-header">
						Bold, Italic, Underline, Strikethrough
					</div>
					<div class="box-container">
						<p><img src="images/help/bold-italic-underline.JPG" alt="Bold, Italic, Underline, Strikethrough" border="none" /></p>
						<p>If you’re familiar with using word processors like Microsoft Word, you are already familiar with these text formatting options. You may highlight a selection of text and click on the icon to change it, or click the icon and begin typing. The necessary tags will be added to the HTML markup of the page automatically.</p>
					</div>
				</div>

				<div class="box row" style="margin-bottom:20px;">
					<div class="box-header">Bulleted Lists, Numbered List, and Block Quotes</div>
					<div class="box-container">
						<p><img src="images/help/bullet-numbered-block.JPG" alt="Bulleted Lists, Numbered List, and Block Quotes" /></p>
						<p>Pressing the Bulleted Lists button will begin a circular bullet list, and hitting enter will create another bullet point underneath it. This is called an unordered list. Pressing tab will start a nested list within the active list. Pressing enter twice will end the list and begin a new paragraph.</p>
						<p>An ordered list works the same way but with numbered points instead of bullets.</p>
						<p>Block quotes define quotations and are only used in special circumstances.</p>
					</div>
				</div>

				<div class="box row" style="margin-bottom:20px;">
					<div class="box-header">Text Align</div>
					<div class="box-container">
						<p><img src="images/help/text-align.JPG" alt="Text Align" /></p>
						<p>The text align property specifies the horizontal alignment of text in an element. These will apply textalign: left, center, right and justify to the HTML of the selected text.</p>
						<p>Note: "Justify" stretches the lines so that each line has equal width, like text you might find in a newspaper column. This option has its uses, but as an Editor it is generally recommended that you avoid it.</p>
					</div>
				</div>

				<div class="box row" style="margin-bottom:20px;">
					<div class="box-header">Add Anchor</div>
					<div class="box-container">
						<p><img src="images/help/anchor.JPG" alt="" /></p>
						<p>The &lt;a&gt;, or anchor tag, defines a hyperlink, which is used to link from one page to another. Hyperlinks have two ends, called anchors, and a direction. Using this tool inserts an anchor with a “name” attribute, which you will have to specify before inserting it.</p>
						<p>After defining the anchor name, it can be linked to from the same page, or from another page. This is useful if you want to jump to a specific spot in a page containing a large amount of text. Anchor names must be unique within a document and the end anchor must be a case-sensitive match. Typically, editors will not need to use anchors when making changes to a webpage.</p>
					</div>
				</div>
				<div class="box row" style="margin-bottom:20px;">
					<div class="box-header">Insert Image</div>
					<div class="box-container">
						<p><img src="images/help/image.JPG" alt="Insert Image" /></p>
						<p>Your image must first be uploaded to the Media Library. To do this, click on the “Media” tab at the top of the screen, and press the “Upload New” button in the top right. It is important that your image is the desired size before you upload it.</p>
						<p>Then, open the page you would like to insert the picture into from the “Pages” tab. Click on “Insert/Edit Image” in the toolbar and find the image you just uploaded in the “Image List” drop down menu. Click on the “Appearance” tab and select either “Left” or “Right” to align your image, and set the Vertical and Horizontal space to 5. If you need to tweak the size of your image, you can adjust the dimensions from the Appearance tab by entering higher or lower number values, but make sure that “Constrain Proportions” is checked.</p>
					</div>
				</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Insert Media</div>
						<div class="box-container">
							<p><img src="images/help/media.JPG" alt="" /></p>
							<p>The Insert Media tool is used for inserting audio and video content. These features are powered by HTML5 and thus are not supported in versions of Internet Explorer 8 and earlier. This is a tool for advanced users only</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Insert Predefined Template Content</div>
						<div class="box-container">
							<p><img src="images/help/template.JPG" alt="" /></p>
							<p>The insert predefined template content tool allows you to insert several ready-made page elements that appear throughout our network of sites, such as faculty bios and styled side boxes. This is a tool primarily for advanced users and developers creating new pages. </p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Headings</div>
						<div class="box-container">
							<p><img src="images/help/headings.JPG" alt="" /></p>
							<p>Headings are a great way to bring more attention to a section of text on a webpage. However, they should be used sparingly and only to introduce the content on the page that follows it. Do not use headings to simply make text BIG or BOLD. Search engines, like Google, use headings to index the structure and content of your pages, so it is very important to be aware of the text contained within headings.</p>
							<p>Browsers automatically add a margin before and after each heading, so using them as a stylistic tool will lead to formatting complications.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Select Text Color</div>
						<div class="box-container">
							<p><img src="images/help/text-color.JPG" alt="" /></p>
							<p>Using this tool applies a <span> to the selection of text with the style modifier and a hex code for the color you chose from the palette. This tool is only to be used in certain very special circumstances – text should remain the same color as the default settings as often as possible to maintain a uniform look. </p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Paste as Plain Text</div>
						<div class="box-container">
							<p><img src="images/help/plain-text.JPG" alt="" /></p>
							<p>The “Paste as Plain Text” feature, like Windows Notepad or Mac TextEdit, will strip out all formatting that accompanies content from a Word document or web page. <strong>Press this button before pasting something into the editing panel from Microsoft Word.</strong></p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Paste From Word</div>
						<div class="box-container">
							<p><img src="images/help/word.JPG" alt="" /></p>
							<p>The “Paste from Word” feature will preserve basic Word document formatting while removing special Word-specific code that transparently accompanies a paste from Word and potentially changes the display of a web page. When you paste directly from Word into an editor that has not been configured for it, the Word content is actually wrapped up in HTML containing XML code, CSS class references and style attributes.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Insert Special Character</div>
						<div class="box-container">
							<p><img src="images/help/special.JPG" alt="" /></p>
							<p>This tool simplifies special character insertion. Selecting the icon brings up a panel allowing you to choose from a variety of characters to insert, such as Greek letters and mathematical symbols.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Decrease/Increase Indent</div>
						<div class="box-container">
							<p><img src="images/help/index.JPG" alt="" /></p>
							<p>This tool adds a style attribute with “margin-left:” to the selected text. Use this if you need to indent a paragraph. Keep in mind that indents are rarely used outside of news articles on the web.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Subscript, Superscript</div>
						<div class="box-container">
							<p><img src="images/help/subscript.JPG" alt="" /></p>
							<p>This is a text formatting tool useful for inserting superscripts, such as squares, and subscripts useful in writing out chemical formulas.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Undo/Redo Action</div>
						<div class="box-container">
							<p>These buttons function the same as the keyboard shortcuts CTRL + Z (undo) and CTRL + Y (redo). Use them if you want to undo mistakes, or redo actions.</p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Fullscreen, Preview</div>
						<div class="box-container">
							<p>The editing panel can be expanded to full screen mode within your browser. The Full Screen button toggles this on and off.</p>
							<p>The Preview button is useful for previewing changes to a webpage without saving. <strong>If you are hesitant about how changes are going to look, it is highly recommended to use the Preview button to view them before saving.</strong></p>
						</div>
					</div>
					<div class="box row" style="margin-bottom:20px;">
						<div class="box-header">Using Tables</div>
						<div class="box-container">
							<p>A table is divided into rows (with the &lt;tr&gt;) tag), and each row is divided into data cells (with the &lt;td&gt; tag). &lt;td&gt; stands for “table data,” and holds the content of a data cell. A &lt;td&gt; tag can contain text, links, images, lists, other tables, and a variety of other elements.</p>
							<p><strong>However, as an editor, it is important to use tables for table-relevant data only.</strong> Some examples would include an upcoming schedule of events, or a table showing clinic rotations.</p>
							<p><strong>Do not use tables to add a box or border around standard text content.</strong></p>
						</div>
					</div>

					<h3 id="media">The Media Tab</h3>
					<p>The Media Library contains uploaded images and documents that can be linked to in webpages.</p>

					<p align="center"><img src="images/help/Mediatab.JPG" width="800" alt="" /></p>

					<p>To upload images or documents, press the <strong>Upload New</strong> button and navigate to what you wish to upload.</p>
					<p>Only certain file types are supported for uploading. <strong>If you are having trouble uploading a .jpg image, make sure that the .jpg is LOWER CASE.</strong> You will need to save the image in the permitted .jpg format before uploading.</p>
					<p>To <strong>DELETE</strong> an image you have uploaded, check the corresponding checkbox on the left, navigate down to the Bulk Options drop down, and choose Delete > Apply.</p>

					<div id="alert-box" class="info" style="display:block;">
						<p>Please be conscious of the file size of images you upload to the Media Library to avoid taking up an unnecessarily large amount of space.</p>
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
		<?php include('includes/footer.php'); ?>
	</body>
	</html>