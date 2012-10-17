<?php

class Forum
{
	public $id,
	
	public static function get_name($id)
	{
		if($id)
			return self::find_by_id("groups", $id);
	}
		
	public static function add($name, $permissions)
	{
		global $database;
		
		# insert group and get id
		$database->insert("groups", array("name" => $name));
		$group_id = $database->insert_id();
		
		# insert permissions
		foreach($permissions as $id)
			$database->insert("permissions", 
								array("id" => $id,
									  "name" => camelize($id, true),
									  "group_id" => $group_id));
		
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function update($name, $permissions)
	{
		global $database;
		
		# update groups table
		$database->update("groups", array("id" => $this->id), array("name" => $name));
		
		# delete existing permissions
		$database->delete("permissions", array("group_id" => $this->id));
		
		# insert new permissions
		foreach($permissions as $id){
			$name = $database->fetch_array($database->select("permissions", 
							  "name", array("id" => $id, "group_id" => 0),
							  null,
							  array(),
							  1));
			$database->insert("permissions",
							  array("id" => $id,
							  		"name" => $name['name'],
							  		"group_id" => $this->id));
		}
		
		return ($database->affected_rows() == 1) ? true : false;
	}
		
	public static function delete($id)
	{
		global $database;
		
		$database->delete("groups", array("id"=>$id));
		$database->delete("permissions", array("group_id" => $id));
		
		return true;
	}
			
	public static function find_by_sql($sql="")
	{
		global $database;
		
	    $result_set = $database->query($sql);
	    $object_array = array();
	    while ($row = $database->fetch_array($result_set)) {
	      $object_array[] = self::instantiate($row);
	    }
	    return $object_array;
	}

	public static function find_all($tables = 'groups', $conds=null, $order=null) {
		return self::find_by_sql(QueryBuilder::build_select($tables, "*", $conds, $order));
	}
	
	public function find_by_id($table="groups",$id=0)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".$table." WHERE id='{$id}' LIMIT 1");
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

}

?>