<?
	require_once('../../../includes/initialize.php');
	$post = new Content();
	$post->department = $_SESSION['department'];
	
	$dept = new Department();
	$dept->id = $_SESSION['department'];
	$subdir = $dept->get()->subdir;
?>
<? 

echo 'var tinyMCELinkList = [';
	
	if($post->find_all('attachment')):
		echo '["--- Files ---", ""],';
		foreach($post->find_all('attachment') as $attachment):
			if(preg_match('/document|archive|interactive|spreadsheet|text|code|video/',ext2type(file_extension(PUBLIC_ROOT.$attachment->url)))):
				echo '["'.$attachment->title.'", "' . $attachment->guid . '"],'."\n";
			endif;
		endforeach;
		echo '["",""],';
	endif;
	
	if($post->find_all('post')):
		echo '["--- Pages ---", ""],';
		foreach($post->display_loop('','&nbsp;&nbsp;') as $page):
			echo '["'.$page->level.' '.$page->title.'", "'.$page->guid.'"],'."\n";
		endforeach;
	endif;
	
	if($post->find_All('article')):
		echo '["",""],';
		echo '["--- News Articles ---", ""],';
		foreach($post->find_All('article') as $article):
			echo '["'._t($article->title, 40).'", "' . $article->guid . '"],'."\n";
		endforeach;
	endif;
	
echo '["---",""]];';


