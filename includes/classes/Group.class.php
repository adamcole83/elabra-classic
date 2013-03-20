<?php

class Group
{
	protected static $tblName = "groups";
	protected static $dbFields = array("id", "name");
	
	public $id;
	public $name;
	public $group_id;
	public $username;
	
	public static function can($permission, $user_id=null)
	{
        global $database;
        global $session;
        
        if(self::exists("permissions", array('id'=>$permission, "group_id"=>$session->user($user_id)->group_id)))
        	return true;
        else
        	return false;
	}
	
	public static function get_name($id)
	{
		if($id)
			return self::find_by_id("groups", $id);
	}
		
	public function get($table="groups")
	{
		global $database;
		if($this->id)
			return self::find_by_id($table, $this->id);
	}
	
	public static function add($name, $permissions)
	{
		global $database;
		global $session;
		
		# if group name exists 
		if(self::exists('groups',array("name"=>$name)))
			return false;
		
		# insert group and get id
		$database->insert("groups", array("name" => $name));
		$group_id = $database->insert_id();
		
		# insert permissions
		foreach($permissions as $id)
			$database->insert("permissions", 
								array("id" => $id,
									  "name" => camelize($id, true),
									  "group_id" => $group_id));
									  
		//log_action('Group Created', "GID{$group_id}", $session->user()->username);
		
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function update($name, $permissions)
	{
		global $database;
		global $session;
		
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
		//log_action('Group Updated', "GID{$this->id}", $session->user()->username);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public static function delete_group_permission($id, $permission)
	{
		global $database;
		global $session;
		//log_action('Group Permission Deleted', "GID{$id} PMID {$permission}", $session->user()->username);
		return $database->delete("permissions", array("group_id" => $id, "id" => $permission));
	}
	
	public static function add_group_permission($id, $permission)
	{
		global $database;
		global $session;
		//log_action('Group Permission Added', "GID{$id} PMID {$permission}", $session->user()->username);
		return $database->insert("permissions", array("id" => $permission, "group_id" => $id, "name" => camelize($permission, true)));
	}
	public static function update_name($id, $name)
	{
		global $database;
		global $session;
		//log_action('Group Updated', "GID{$id}", $session->user()->username);
		return $database->update("groups", array("id" => $id), array("name" => $name));
	}
	
	public static function delete($id)
	{
		global $database;
		global $session;
		//log_action('Group Deleted', "GID{$id}", $session->user()->username);
		$database->delete("groups", array("id"=>$id));
		$database->delete("permissions", array("group_id" => $id));
		
		return true;
	}
	
	public function hasPermission($permission)
	{
		return self::exists("permissions", array('id'=>$permission, "group_id"=>$this->id));
	}
	
	public static function add_permission($id, $group_id = null)
	{
		global $database;
		global $session;
		# if gpermission exists 
		if(self::exists('permissions',array("id"=>$id)))
			return false;
		
		$database->insert("permissions", array("id" => $id, "name" => camelize($id, true), "group_id" => 0));
		
		if($group_id){
			foreach($group_id as $key=>$gid)
				$database->insert("permissions", array("id" => $id, "name" => camelize($id, true), "group_id" => $gid));
		}
		
		//log_action('Permission Added', "PMID {$permission}", $session->user()->username);
		return ($database->affected_rows() >= 1) ? true : false;
	}
	
	public static function remove_permission($id)
	{
		global $database;
		global $session;
		//log_action('Permission Deleted', "PMID {$permission}", $session->user()->username);
		return $database->delete("permissions", array("id" => $id));
	}
	
	public function members()
	{
		return User::find(array("WHERE"=>array("group_id" => $this->id)));
	}
	
	public function exists($table, $field)
	{
		global $database;
		
		# if group name exists 
		$database->select($table, "*", $field);
		if($database->affected_rows() >= 1)
			return true;
		else
			return false;
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