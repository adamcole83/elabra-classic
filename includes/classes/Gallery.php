<?php

class Gallery
{
	private static 	$tblAlbums 		= "gallery_testing.albums",
					$tblPhotos 		= "gallery_testing.photos",
					$tblComments	= "gallery_testing.comments",
					$fields			= array(),
					
					$dbfAlbums		= array(
										"album_id",
										"album_name",
										"album_path",
										"album_created"
									),
					$dbfPhotos		= array(
										"photo_id",
										"photo_album_id",
										"photo_name",
										"photo_submitted_date",
										"photo_submitted_by",
										"photo_views",
										"photo_caption",
										"EXIF_date_taken",
										"EXIF_camera",
										"EXIF_shutterspeed",
										"EXIF_focallength",
										"EXIF_flash",
										"EXIF_aperture"
									),
					$dbfComments	= array(
										"comment_id",
										"comment_photo_id",
										"comment_author",
										"comment_email",
										"comment_date",
										"comment_ip",
										"comment_message"
									)
	;
	
	public 	$album_id,
			$album_name,
			$album_path,
			$album_created,
			$photo_id,
			$photo_album_id,
			$photo_submitted_date,
			$photo_submitted_by,
			$photo_views,
			$photo_caption,
			$EXIF_date_taken,
			$EXIF_camera,
			$EXIF_shutterspeed,
			$EXIF_focallength,
			$EXIF_flash,
			$EXIF_aperture,
			$comment_id,
			$comment_photo_id,
			$comment_author,
			$comment_email,
			$comment_date,
			$comment_ip,
			$comment_message
	;
	
	/* SET DATA */
	public function addAlbum()
	{
		global $db;
		
		$this->fields = $this->dbfAlbums;
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".$this->tblAlbum." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($db->query($sql)){
			$this->album_id = $db->insert_id();
			return true;
		}else{
			return false;
		}
	}
	
	public function addPhoto()
	{
		global $db;
		
		$this->fields = $this->dbfphotos;
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".$this->tblAlbum." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($db->query($sql)){
			$this->photo_id = $db->insert_id();
			return true;
		}else{
			return false;
		}
	}
	
	public function addComment()
	{
		global $db;
		
		$this->fields = $this->dbfComments;
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".$this->tblAlbum." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($db->query($sql)){
			$this->comment_id = $db->insert_id();
			return true;
		}else{
			return false;
		}
	}

	
	/* GET DATA */
	public static function getAlbum($id)
	{
		if( $id )
		{
			return self::find_by_sql(
						QueryBuilder::build_select(
							self::$tblAlbum,
							"*",
							array("album_id"=>$id)
						));
		}
		else
		{
			throw new Exception("Album ID was not set");
		}
	}
	
	public static function getPhoto($id)
	{
		if( $id )
		{
			$this->fields = $this->dbfPhotos;
			return $this->find_by_sql(
						QueryBuilder::build_select(
							$this->tblPhoto,
							"*",
							array("photo_id"=>$this->photo_id)
						));
		}
		else
		{
			throw new Exception("Photo ID was not set");
		}
	}
	
	public function listAlbums($start='',$limit='')
	{
		return self::find_by_sql(
					QueryBuilder::build_select(
						self::$tblAlbums,
						"*",'',
						'album_name ASC',
						$limit,
						$start
					),self::$dbfAlbums);
	}
	
	public static function numAlbums()
	{
		global $db;
		return array_shift($db->count(self::$tblAlbums));
	}
	
	public function listPhotos($start='',$limit='')
	{
		return self::find_by_sql(
					QueryBuilder::build_select(
						self::$tblPhotos,
						"*",
						( $this->album_id ) ? array("photo_album_id" => $this->album_id) : '',
						'photo_id DESC',
						$limit,
						$start
					),self::$dbfPhotos);
	}
	
	public static function numPhotos($id)
	{
		global $db;
		if( $id )
			return array_shift($db->count(self::$tblPhotos, array('photo_album_id'=>$id)));
		else
			return array_shift($db->count(self::$tblPhotos));
	}
	
	public static function numViews($id)
	{
		global $db;
		return array_shift($db->select(self::$tblPhotos, 'views', array('photo_id'=>$id)));
	}
	
	public function listComments()
	{
		return $this->find_by_sql(
						QueryBuilder::build_select(
							$this->tblAlbums,
							"*",
							array("comment_photo_id" => $this->photo_id)
						));
	}
	
	/* Object Instantiation */
	private function find_by_sql($sql="", $fields=array())
	{
		global $database;
		
	    $result_set = $database->query($sql);
	    $object_array = array();
	    while ($row = $database->fetch_array($result_set)) {
	      $object_array[] = self::instantiate($row,$fields);
	    }
	    return $object_array;
	}
	
	private function instantiate($record,$fields)
	{
		$object = new self;
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute,$fields)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute,$fields)
	{
		return array_key_exists($attribute, $this->attributes($fields));
	}
	
	private function attributes($fields=array())
	{
		$attributes = array();
		foreach($fields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}

	private function sanitized_attributes()
	{
		global $database;
		$clean_attributes = array();
		foreach($this->attributes() as $key => $value){
			$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}


}

?>