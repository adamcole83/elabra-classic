<?

class Department
{
	protected static $tblName = "departments";
	protected static $dbFields = array('id', 'name', 'code', 'subdir', 'index_id', 'dev_mode');
	
	public $id;
	public $name;
	public $code;
	public $subdir;
	public $index_id;
	public $dev_mode;
	
	public function get()
	{
		global $database;
		if($this->id) {
			return self::find_by_id($this->id);
		}
	}
	
	public static function grab($id)
	{
		if($id)
			return self::find_by_id($id);
	}
	
	public function save()
	{
		return (isset($this->id)) ? $this->update() : $this->create();
	}
	
	public function create()
	{
		global $database;
		if(!$this->department_exists($this->code)){
			$attributes = $this->sanitized_attributes();
			$sql = "INSERT INTO ".self::$tblName." (";
			$sql .= join(", ", array_keys($attributes));
			$sql .= ") VALUES ('";
			$sql .= join("', '", array_values($attributes));
			$sql .= "')";
			
			error_log('[Department:create()]: '.$sql,0);
			
			if($database->query($sql)){
				$this->id = $database->insert_id();
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function update()
	{
		global $database;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			if(!empty($value) || is_numeric($value)){
				$attribute_pairs[] = "{$key}='{$value}'";
			}
		}
		$sql = "UPDATE ".self::$tblName." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
		
		error_log('[Department:update()]: '.$sql,0);
		
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false; 
	}
	
	public function delete()
	{
		global $database;
		
		$sql = "DELETE FROM ".self::$tblName;
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function find_by_sql($sql="")
	{
		global $database;
		
	    $result_set = $database->query($sql);
	    $object_array = array();
	    while ($row = $database->fetch_array($result_set)) {
	      $object_array[] = self::instantiate($row);
	    }
	    return $object_array;
	}
	
	public static function find_all($limit='') {
		return self::find_by_sql("SELECT * FROM ".self::$tblName." ORDER BY name ASC $limit");
	}
	
	public function find_by_id($id=0)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	private function instantiate($record)
	{
		$object = new self;
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute)
	{
		return array_key_exists($attribute, $this->attributes());
	}
	
	private function attributes()
	{
		$attributes = array();
		foreach(self::$dbFields as $field) {
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
	
	public function getAssocUser($id)
	{
		global $db;
		$user_array = self::find_by_sql("SELECT * FROM users WHERE department='{$id}'");
		$meta_array = self::find_by_sql("SELECT user_id AS id, meta_key AS code FROM usermeta WHERE meta_key='department' AND meta_value='{$id}'");
		$result_array = array_merge($user_array, $meta_array);
		return !empty($result_array) ? $result_array : array();
	}
	
	private function department_exists($item)
	{
		$results = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE code='{$item}'");
		return (!empty($results)) ? true : false;
	}
	
	public static function find_by_code($code)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE code='{$code}'");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_name($name)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE name='{$name}'");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_dir($param)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE subdir='{$param}'");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function listAvailable()
	{
		global $session;
		global $db;
		
		if( ! Group::can('edit_departments')) {
			$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE id='{$session->user()->department}'");
			$meta_array = self::find_by_sql("SELECT meta_value AS id FROM usermeta WHERE meta_key='department' AND user_id='{$session->user()->id}'");
			foreach($meta_array as $d) {
				$result_array = array_merge($result_array, self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE id='{$d->id}'"));
			}
		}else{
			$result_array = self::find_all();
		}
		return !empty($result_array) ? $result_array : false;
	}
	
	function set_up_department()
	{
		$dept_path = PUBLIC_ROOT.DS.$this->subdir.DS;
		if(!file_exists($dept_path)) {
			if(@mkdir($dept_path)) {
				
			}
		}
	}
	
	public function relocate($from='', $to='')
	{
		error_log('...Relocating', 0);
		// Query all content
		$posts = Content::find_by_sql("SELECT * FROM cms.posts WHERE department = {$this->id}");
		
		foreach($posts as $post)
		{
			$content = new Content();
			$content->id = $post->id;
			$content->body = preg_replace("/\/$from\//i", '/'.$to.'/', $post->body);
			$content->url = preg_replace("/\/$from\//i", '/'.$to.'/', $post->url);
			$content->guid = preg_replace("/\/$from\//i", '/'.$to.'/', $post->guid);
			$content->save();
		}
		
		$dept = new self;
		$dept->id = $this->id;
		$dept->subdir = $to;
		$dept->save();
		
		rename(PUBLIC_ROOT.DS.$from, PUBLIC_ROOT.DS.$to);
		
		return true;
	}

}

?>