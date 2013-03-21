<?php

class CropImage
{
	var $imgSrc,
		$myImage,
		$x,
		$y,
		$thumb;
	
	var $cropWidth = 150;
	var $cropHeight = 100;
	
	public function __construct($image)
	{
		$this->imgSrc = $image;
		//$this->getImage();
	}
	
	public function getImage()
	{
		$filetype = explode('.', $this->imgSrc);
		$filetype = $filetype[count($filetype) -1];
		$filetype = strtolower($filetype);
		
		list($width, $height) = getimagesize($this->imgSrc);
		
		switch($filetype)
		{
			case 'jpg':
				$this->myImage = imagecreatefromjpeg($this->imgSrc);
				break;
				
			case 'gif':
				$this->myImage = imagecreatefromgif($this->imgSrc);
				break;
				
			case 'png':
				$this->myImage = imagecreatefrompng($this->imgSrc);
				break;
			
			default:
				die("Image filetype not accepted: jpg, png, gif only");
				break;
		}
		
		$cropped_image_gd = imagecreatetruecolor($this->cropWidth, $this->cropHeight);
		
		$wm = $width / $this->cropWidth;
		$hm = $height / $this->cropHeight;
		$w_height = $this->cropWidth / 2;
		$w_width = $this->cropHeight / 2;
		
		if($width > $height)
		{
			$adjusted_width = $width / $hm;
			$half_width = $adjusted_width / 2;
			$int_width = $half_width - $w_height;
			
			imagecopyresampled($cropped_image_gd, $this->myImage, -$int_width, 0,0,0, $adjusted_width, $this->cropHeight, $width, $height);
		}
		elseif($width < $height)
		{
			$adjusted_height = $height / $wm;
			$half_height = $adjusted_height / 2;
			$int_height = $half_height - $h_height;
			
			imagecopyresampled($cropped_image_gd, $this->myImage, 0, -$int_height, 0,0, $this->cropWidth, $adjusted_height, $width, $height);
		}
		else
		{
			imagecopyresampled($cropped_image_gd, $this->myImage, 0,0,0,0, $this->cropWidth, $this->cropHeight, $width, $height);
		}
		
		return imagejpeg($cropped_image_gd);
	}
	
	
}

?>