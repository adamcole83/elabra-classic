<?

class DataConnect
{
		
	function __construct()
	{
		
	}
	
	public function save()
	{
		return (isset($this->id)) ? $this->update() : $this->create();
	}

	public function create()
	{
		global $database;
		$child = get_called_class();
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".$child::$tblName." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES (";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($database->query($sql)){
			$this->id = $database->insert_id();
			return true;
		}else{
			return false;
		}
	}
	
	public function update()
	{
		global $database;
		$child = get_called_class();
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			if(!empty($value)){	
				$attribute_pairs[] = "{$key}='{$value}'";
			}
		}
		$sql = "UPDATE ".$child::$tblName." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_values($this->id);
		$database->query($sql);
		return ($database->affected_rows == 1) ? true : false; 
	}
	
	public function delete()
	{
		global $database;
		$child = get_called_class();
		$sql = "DELETE FROM ".$child::$tblName;
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		
		return ($database->affected_rows == 1) ? true : false;
	}
	
	
	public function find_by_sql($sql="")
	{
		global $database;
		$child = get_called_class();
		
	    $result_set = $database->query($sql);
	    $object_array = array();
	    while ($row = $database->fetch_array($result_set)) {
	      $object_array[] = self::instantiate($row);
	    }
	    return $object_array;
	}
	
	public static function find_all() {
		$child = get_called_class();
		return self::find_by_sql("SELECT * FROM ".$child::$tblName);
	}
	
	protected function find_by_id($id=0)
	{
		$child = get_called_class();
		$result_array = self::find_by_sql("SELECT * FROM ".$child::$tblName." WHERE id={$id} LIMIT 1");
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
		$child = get_called_class();
		$attributes = array();
		foreach($child::$dbFields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}
	
	private function sanitize_attributes()
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