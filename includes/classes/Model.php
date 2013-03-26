<?php

class Model {

	private static $tblName;
	private static $dbFields;

	public function __construct()
	{

	}

	public function create($data = array())
	{
		if ( ! is_array($data))
		{
			return false;
		}

		global $db;

		if ( $db->query( QueryBuilder::build_insert(self::$tblName, $data) ))
		{
			return $db->insert_id();
		}
		else
		{
			return false;
		}
	}

	public function update($id = null, $conds = array(), $data = array())
	{
		if ( ! isset($id) || ! is_array($data))
		{
			return false;
		}

		global $db;

		$db->query( QueryBuilder::build_update(self::$tblName, $conds, $data) );

		return $db->affected_rows() >= 1;
	}

	public function delete($id)
	{
		if ( ! isset($id))
		{
			return false;
		}

		global $db;

		$db->query( QueryBuilder::build_delete(self::$tblName, array('id' => $id)) );

		return $db->affected_rows() == 1;
	}

	public static function find_all()
	{
		return self::find_by_sql( QueryBuilder::build_select(self::$tblName) );
	}

	public static function find_by($key, $value)
	{
		$params = array();
		if ( ! is_array($key))
		{
			$params[$key] = $value;
		}
		else
		{
			$params = $key;
		}
		return self::find_by_sql( QueryBuilder::build_select(self::$tblName, '*', $conds) );
	}

	public static function find_by_sql($sql="")
	{
		global $db;

		$result_set = $db->query($sql);
		$object_array = array();
		while ($row = $db->fetch_array($result_set))
		{
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	protected function instantiate($record)
	{
		$object = new self;
		foreach ($record as $attribute => $value)
		{
			if ($object->has_attribute( $attribute ))
			{
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	protected function has_attribute($attribute)
	{
		return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes()
	{
		$attributes = array();
		foreach (self::$dbFields as $field)
		{
			if (property_exists($this, $field))
			{
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}

	protected function sanitized_attributes()
	{
		global $db;
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value)
		{
			$clean_attributes[$key] = $db->escape_value($value);
		}
		return $clean_attributes;
	}

	public static function dbFields()
	{
		return self::$dbFields;
	}

	public static function tblName()
	{
		return self::$tblName;
	}

}