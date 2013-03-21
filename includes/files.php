<?php
require_once('initialize.php');
	
	if($_GET['new']){
		mkdir($_GET['new'], 0755, true);
		echo 1;
		die;
	}

	$startdir = '../../';
	$showdirs = true;
	$filetypes = array(
							'png' => 'png.png',
							'jpeg' => 'jpeg.png',
							'bmp' => 'bmp.png',
							'jpg' => 'jpeg.png', 
							'gif' => 'gif.png',
							'zip' => 'archive.png',
							'rar' => 'archive.png',
							'txt' => 'txt.png',
							'htm' => 'html.png',
							'log' => 'log.png',
							'html' => 'html.png',
							'fla' => 'fla.png',
							'swf' => 'swf.png',
							'xls' => 'xls.png',
							'xlsx' => 'xls.png',
							'doc' => 'doc.png',
							'docx' => 'doc.png',
							'pdf' => 'pdf.png',
							'psd' => 'psd.png',
							'rm' => 'real.gif',
							'mpg' => 'mov.png',
							'mpeg' => 'mov.png',
							'mov' => 'mov.png',
							'mp4' => 'mov.png',
							'mp3' => 'audio.png',
							'wav' => 'audio.png',
							'aiff' => 'audio.png',
							'avi' => 'video.gif',
							'eps' => 'eps.gif',
							'gz' => 'archive.png',
							'asc' => 'sig.gif',
							'php' => 'php.png',
							'xml' => 'xml.png'
							);
	
	/* BRAINS */
	if($startdir)
		$startdir = preg_replace("/^\//", "${1}", $startdir);
		
	$leadon = $startdir;
	if($leadon=='.')
		$leadon = '';
	
	if((substr($leadon, -1, 1)!='/') && $leadon!='')
		$leadon = $leadon . '/';
		
	$startdir = $leadon;
	
	if($_GET['dir']) {
		if(substr($_GET['dir'], -1, 1)!='/')
			$_GET['dir'] = strip_tags($_GET['dir']) . '/';
		
		$dirok = true;
		$dirnames = @split('/', strip_tags($_GET['dir']));
		for($di=0; $di<sizeof($dirnames); $di++) {
			if($di<(sizeof($dirnames)-2))
				$dotdotdir = $dotdotdir . $dirnames[$di] . '/';
			
			if($dirnames[$di] == '..')
				$dirok = false;
		}
		
		if(substr($_GET['dir'], 0, 1)=='/')
			$dirok = false;
		
		if($dirok)
			 $leadon = $leadon . strip_tags($_GET['dir']);
	}

	$opendir = $includeurl.$leadon;
	if(!$leadon)
		$opendir = '.';
		
	if(!file_exists($opendir)) {
		$opendir = '.';
		$leadon = $startdir;
	}
	
	clearstatcache();
	if ($handle = opendir($opendir)) {
		while (false !== ($file = readdir($handle))) { 
			//first see if this file is required in the listing
			if ($file == "." || $file == "..")
				continue;
			
			$discard = false;
			for($hi=0;$hi<sizeof($hide);$hi++) {
				if(strpos($file, $hide[$hi])!==false)
					$discard = true;
			}
			
			if($discard)
				continue;
			
			if (@filetype($includeurl.$leadon.$file) == "dir") {
				if(!$showdirs)
					continue;
			
				$n++;
				if($_GET['sort']=="date")
					$key = @filemtime($includeurl.$leadon.$file) . ".$n";
				else
					$key = $n;
				
				$dirs[$key] = $file;
			
			}else {
				$n++;
				$key = $n;
				$files[$key] = $file;
			}
		}
		closedir($handle); 
	}
	
	@natcasesort($dirs);
	
	//order files correctly
	$dirs = @array_values($dirs);
	$files = @array_values($files);
	$filecount = 0;
?>

<div class="view">
	<? for($i=0;$i<sizeof($dirs);$i++): ?>
		<div>
			<img src="/admin/images/ico/folder_l.png" alt="<?php echo $dirs[$i];?>" />
			<span class="dir"><?php echo $dirs[$i];?></span>
		</div>
	<? endfor; ?>
	<? if($_GET['type'] == 'open'): ?>
		<? for($i=0;$i<sizeof($files);$i++): ?>
			<? $icon = 'unknown.png';
				$ext = strtolower(substr($files[$i], strrpos($files[$i], '.')+1));
					
				if($filetypes[$ext])
					$icon = $filetypes[$ext];
						
				$filename = $files[$i];
				if(strlen($filename)>10)
					$filename = substr($files[$i], 0, 10) . '...';
			?>
			<div>
				<img src="/admin/images/ico/<?php echo $icon;?>" alt="<?php echo $files[$i];?>" />
				<span><?php echo $filename;?></span>
			</div>
		<? endfor; ?>
	<? endif; ?>
</div>