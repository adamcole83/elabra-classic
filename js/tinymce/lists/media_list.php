<?
	require_once('../../../includes/initialize.php');
	$post = new Content();
	$post->department = $_SESSION['department'];
?>
<? 

echo 'var tinyMCEMediaList = [';
	foreach($post->find_all('attachment') as $attachment):
		if( preg_match('/audio|video|image/',mime2type($attachment->post_mime_type)) ):
			echo '["'.$attachment->title.'", "' . DOMAIN.$attachment->url . '"],';
		endif;
	endforeach;
echo '];';


