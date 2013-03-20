<?php

require_once('../../includes/initialize.php');

$file = $_GET['f'];
$height = $_GET['height'];
$width = $_GET['width'];
$url = parse_url($file);

error_log(dirname($url['path']));

WideImage::load('/var/www/html/medicine.missouri.edu/'.dirname($url['path']))->resize($width, $height)->output('jpg', 90);