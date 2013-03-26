<?php

require_once "Model.php";

class DepartmentMeta extends Model {

	private static $tblName = "deptmeta";
	private static $dbFields = array('umeta_id', 'dept_id', 'meta_key', 'meta_value');

	public function __contruct()
	{
		parent::__contruct();
	}

}