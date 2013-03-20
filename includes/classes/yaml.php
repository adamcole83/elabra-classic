<?php

$script_name = $_SERVER['SCRIPT_FILENAME'];
$doc_root = addcslashes($_SERVER['DOCUMENT_ROOT'], '/');
$regex = "/$doc_root\/([\w-]+)\/[\w\W]+/i";
$site_root = preg_replace($regex, '$1', $script_name);
$yaml = Spyc::YAMLLoad(PUBLIC_ROOT.DS.$site_root.DS.'includes/config.yaml');
$basename = basename($_SERVER['PHP_SELF']);
$uri = (preg_match('/.php/',$basename)) ? $basename : 'index.php';
$uri = preg_replace('/\.php/','',$uri);

if($yaml['page']){
	foreach($yaml['page'] as $parent=>$child){
		foreach($yaml['page'][$parent] as $child=>$attribute){
			if($child == $uri){
				$category = $parent;
				break;
			}
		}
	}
	$_PAGE = (object) $yaml['page'][$category][$uri];
	$_PAGE->category = $category;
	$_PAGE->url = $uri;
}

$_SITE = (object) $yaml;

?>