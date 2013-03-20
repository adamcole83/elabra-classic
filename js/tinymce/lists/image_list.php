<?
	require_once('../../../includes/initialize.php');
	$post = new Content();
	$post->department = $_SESSION['department'];
?>
<? 

echo 'var tinyMCEImageList = [';
	foreach($post->find_all('attachment') as $attachment):
		if(mime2type($attachment->post_mime_type) == 'image'):
			echo '["'.$attachment->title.'", "' . DOMAIN.$attachment->url . '"],';
		endif;
	endforeach;
echo '];';


