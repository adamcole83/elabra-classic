<?

class User
{
	protected static $tblName = "users";
	protected static $dbFields = array("id", "username", "password", "salt", "first_name", "last_name", "email", "phone_number", "log_date", "log_time", "department", "editor", "group_id", "prev_login", "active", "pawprintuser", "title", "domain");
	
	public $id = 0;
	public $username;
	public $password;
	public $department;
	public $email;
	public $log_date;
	public $log_time;
	public $salt;
	public $editor;
	public $group_id;
	public $prev_login;
	public $first_name;
	public $last_name;
	public $phone_number;
	public $pawprintuser;
	public $active;
	
	function __construct()
	{
		
	}
	
	public static function get($id)
	{
		if(!empty($id)) {
			return self::find_by_id($id);
		}else{
			return false;
		}
	}
	
	public function authenticate($username="", $password="")
	{
		global $database;
		global $salt;
		$username = $database->escape_value($username);
		$password = $database->escape_value($password);
		
		$sql = "SELECT * FROM ".self::$tblName;
		$sql .= " WHERE username = '{$username}'";
		$result = $database->query($sql);
		
		if($database->num_rows($result) < 1){
			return false;
		}
		
		$userData = $database->fetch_array($result);
		
		$salted_password = $salt->saltPw($password, $userData['salt']);
		
		if($salted_password != $userData['password']){
			return false;
		}
		else{
			$result_array = self::find_by_sql($sql);
			return !empty($result_array) ? array_shift($result_array) : false;
		}
	}
	
	public function dbAuthenticate($username="")
	{
		global $db;

		$username = $db->escape_value($username);
		$result = self::find_by_sql(QueryBuilder::build_select('users','*',array('username'=>$username)));
		
		return !empty($result) ? array_shift($result) : false;
	}
	
	public function save()
	{
		return ($this->id != 0) ? $this->update() : $this->create();
	}
	
	public function create()
	{
		global $database;
		global $salt;
		global $session;
		if(!self::username_exists($this->username)){
			if(!empty($this->password)){
				$newpass = $salt->createPw($this->password);
				$this->salt = $newpass['salt'];
				$this->password = $newpass['salted_password'];
			}
			$attributes = $this->sanitized_attributes();
			$sql = "INSERT INTO ".self::$tblName." (";
			$sql .= join(", ", array_keys($attributes));
			$sql .= ") VALUES ('";
			$sql .= join("', '", array_values($attributes));
			$sql .= "')";
			$database->query($sql);
			if( $database->affected_rows() == 1 ){
				$this->id = $database->insert_id();
				//log_action('User Created', "UID{$this->id} username: {$this->username}", $session->user()->username);
				return true;
			}else{
				error_log('Database failed: '.$sql);
				return false;
			}
		}else{
			error_log('Username exists: '.$this->username);
			return false;
		}
	}
	
	public function update()
	{
		global $database;
		global $salt;
		global $session;
		if(!empty($this->password)){
			$newpass = $salt->createPw($this->password);
			$this->salt = $newpass['salt'];
			$this->password = $newpass['salted_password'];
		}
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			if(!empty($value) && $key != 'id'){	
				$attribute_pairs[] = "{$key}='{$value}'";
			}
		}
		$sql = "UPDATE ".self::$tblName." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$database->query($sql);
		
		error_log($sql,0);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function delete()
	{
		global $database;
		global $session;
		
		$sql = "DELETE FROM ".self::$tblName;
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		//log_action('User Deleted', "UID{$this->id}", $session->user()->username);
		return ($database->affected_rows() == 1) ? true : false;
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
	
	public static function find($options = array())
	{
		$option = $options[0];
		foreach($option as $key => $value)
			$attribute_pairs[] = $key."='".$value."'";
		
		$attributes = join(', ', $attribute_pairs);
		
		return self::find_by_sql("SELECT * FROM ".self::$tblName." ".$option." ".$attributes);
	}
	
	public static function find_all($limit='') {
		return self::find_by_sql("SELECT * FROM ".self::$tblName." ORDER BY username ASC $limit");
	}
	
	public function find_by_id($id=0)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public function find_by_username($username="")
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE username='{$username}' LIMIT 1");
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
	
	public function department()
	{
		return DEPARTMENT;
	}
	
	private function username_exists($username="")
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE username='{$id}' LIMIT 1");
		return !empty($result_array) ? true : false;
	}
	
	public function timestamp($id=0)
	{
		$get = self::find_by_id($id);
		$obj = new self;
		$obj->id = $id;
		$obj->log_date = date( 'Y-m-d' );
		$obj->log_time = date( 'H:i:s' );
		$obj->prev_login = $get->log_date.' '.$get->log_time;
		$obj->update();
	}
	
	public static function can($permission)
	{
		return Group::can($permission);
	}
	
	public static function activate($usr)
	{
		global $db;
		$db->update(self::$tblName, array('username'=>$usr), array('active'=>'1'));
		return ($db->affected_rows() == 1) ? true : false;
	}
	
	public function full_name()
	{
		return $this->first_name . " " . $this->last_name;
	}

}

?>