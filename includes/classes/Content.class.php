<?

class Content
{
	
	protected static $tblName = "posts";
	protected static $dbFields = array("id", "parent_id", "status", "title", "description", "department", "url", "guid", "body", "draft", "updated", "updatedBy", "sidebar", "post_type", "post_created", "post_mime_type", "menu_order");
	
	public $id;
	public $status;
	public $title;
	public $description;
	public $department;
	public $url;
	public $body;
	public $draft;
	public $updated;
	public $updatedBy;
	public $sidebar;
	public $post_type;
	public $baseurl;
	public $search_string;
	public $post_created;
	public $post_mime_type;
	public $parent_id;
	public $menu_order;
	public $guid;
	public $breadcrumb;
	private $breadcrumbs;
	public $loop_items = array();
	
	public function Content()
	{
		$this->baseurl = $this->urlcapture();
	}
	
	public function get($type="post")
	{
		global $database;
		if($this->id) {
			return self::find_by_id($this->id);
		}elseif($this->baseurl){
			$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE url='{$this->baseurl}' AND post_type='{$type}t'");
			return !empty($result_array) ? array_shift($result_array) : false;
		}
	}
	
	public static function get_content($dept)
	{
		$obj = new self;
		$obj->department = $dept;
		return $obj->getContent();
	}
	
	public function getContent()
	{
		if( !empty($this->baseurl) ) {
			
			// check if page exists in db
			$result_array = self::find_by_url($this->baseurl);
			if(!empty($result_array)) {
				return $result_array;
			}
			
			// if page not found search for it
			$this->search_string = str_replace('.php','', basename(REQUEST_URI));
			$page = $this->searchContent();
			$dir = Department::grab($page->department)->subdir;
			if(!empty( $page->url )) {
				header ('HTTP/1.1 301 Moved Permanently');
  				header ('Location: '."/$dir/{$page->url}.html");
				return false;
			}
			
			// if still not found see if page is static
			$path = PUBLIC_ROOT.DS.SITE_ROOT.DS.$this->baseurl.".html";
			if(file_exists($path)) {
				header ('HTTP/1.1 301 Moved Permanently');
  				header('Location: '.DS.SITE_ROOT.DS.$this->baseurl.".shtml");
				return false;
			}
			
			// if all else fails show 404
			return $this->show_404();
			
		}else{
			return $this->show_404();
		}
	}
		
	function urlcapture()
	{
		return (isset($_GET['url'])) ? $_GET['url'] : 'home';
	}
	
	function show_404()
	{
		header('HTTP/1.1 404 Not Found');
		return array_shift( self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE url='404' LIMIT 1") );
	}
	
	public function searchContent()
	{
		if($this->search_string) {
			$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE url LIKE '%".$this->search_string."%' AND department='".$this->department."' AND post_type='post'");
			if( !empty($result_array) ) {
				return array_shift($result_array);
			}else{
				return null;
			}
		}
	}
	
	
	public function save()
	{
		return (isset($this->id)) ? $this->update() : $this->create();
	}
	
	public function create()
	{
		global $database, $session;
		
		$attributes = $this->sanitized_attributes();
		
		// Created on
		$attributes['post_created'] = time();
		
		$sql = "INSERT INTO ".self::$tblName." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";		
		if($database->query($sql)){
			return $database->insert_id();
		}else{
			return false;
		}
	}
	
	public function update()
	{
		global $database, $session;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			if(!empty($value) || is_numeric($value)){	
				$attribute_pairs[] = "{$key}='{$value}'";
			}
		}
		
		// Create revision
		$obj = self::find_by_id($this->id);
		if($obj->post_type == 'post') {
			self::new_revision($this->id, $attributes);
		}
		
		$sql = "UPDATE ".self::$tblName." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false; 
	}
	
	public function delete()
	{
		global $database, $session;
		$obj = self::find_by_id($this->id);
		if( $obj->post_type == 'post' ) {
			self::delete_all_revisions($this->id);
		}
		$sql = "DELETE FROM ".self::$tblName;
		$sql .= " WHERE id=". $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public function find_all($type="post",$limit='') {
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$this->department."' AND post_type='{$type}' ORDER BY title ASC {$limit}");
	}
	
	public function build_page_loop($parent=0,$limit='',$level=0,$divider)
	{
		$items = self::find_by_sql(sprintf("SELECT * FROM ".self::$tblName." WHERE parent_id = %d AND post_type = 'post' AND department = %d ORDER BY menu_order,title {$limit}", $parent, $this->department));
		
		if(! empty($items))
		{
			foreach($items as $item)
			{
				$item->level = str_repeat($divider, $level);
				$item->breadcrumb = preg_replace('/\S/', '&nbsp;',$item->level).$this->breadcrumb($item->id, $item->id);
				array_push($this->loop_items, $item);
				$this->build_page_loop($item->id, $limit, $level + 1, $divider);
			}
		}
		else
		{
			return false;
		}
	}
	
	public function display_loop($limit='', $divider=" – ")
	{
		$this->build_page_loop(0,$limit,0,$divider);
		return (object) $this->loop_items;
	}
	
	private function breadcrumb($id=0, $item=null)
	{
		$html='';
		if($id != 0)
		{
			if($id == $item)
			{
				$page = self::find_by_id($id);
				//if($page->parent_id == 0) return;
				$this->breadcrumbs[] = '<span title="'.$page->guid.'">'.$page->title.'</span>';
				return $this->breadcrumb($page->parent_id);
			}
			else
			{
				$page = self::find_by_id($id);
				$this->breadcrumbs[] = '<span title="'.$page->guid.'">'.$page->title.'</span> <strong> &raquo; </strong> ';
				return $this->breadcrumb($page->parent_id);
			}
		}
		else
		{
			$html .= '<small>';
			foreach(array_reverse($this->breadcrumbs) as $key => $crumb)
			{
				$html .= $crumb;
			}
			$html .= '</small>';
			$this->breadcrumbs = null;
			return $html;
		}
	}
	
	public function parent_dropdown($default = 0, $parent = 0, $level = 0)
	{
		$items = self::find_by_sql(sprintf("SELECT id, parent_id, title FROM ".self::$tblName." WHERE parent_id = %d AND post_type = 'post' AND department = %d ORDER BY menu_order, title", $parent, $this->department));
		
		if( ! empty($items))
		{
			foreach($items as $item)
			{
				$pad = str_repeat('&nbsp;', $level * 3);
				if($item->id == $default)
				{
					$current = ' selected="selected"';
				}
				else
				{
					$current = '';
				}
				
				if($item->id != $this->id)
				{
					echo "\n\t<option class=\"level-$level\" value=\"{$item->id}\"$current>$pad {$item->title}</option>";
				}
				$this->parent_dropdown($default, $item->id, $level + 1);
			}
		}
		else
		{
			return false;
		}
	}
	
	function find_parents($dept)
	{
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$dept."' AND posts.parent_id=posts.id AND posts.id != '{$this->id}' ORDER BY title ASC");
	}
	
	function list_all_parents()
	{
		global $db;
		$array = array();
		$page_results = $db->query("SELECT id,title,url,menu_order FROM ".self::$tblName." WHERE department='".$this->department."' AND posts.parent_id=posts.id ORDER BY menu_order ASC");
		while($row = $db->fetch_array($page_results))
		{
			array_push($array, $row);
		}
		$meta_results = $db->select('cms.deptmeta','*',array( 'meta_key' => 'menu-item', 'dept_id' => $this->department ));
		while($row = $db->fetch_array($meta_results))
		{
			$value = explode(';',$row['meta_value']);
			$obj['id'] = 'd'.$row['umeta_id'];
			$obj['menu_order'] = $value[0];
			$obj['title'] = $value[1];
			$obj['url'] = $value[2];
			array_push($array, $obj);
		}
		
		return $this->aasort($array,'menu_order');
	}
	
	function aasort (&$array, $key)
	{
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]= $va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[]= (object) $array[$ii];
		}
		return $ret;
	}
	
	public function get_attachments() {
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$this->department."' AND post_type='attachment' ORDER BY title ASC");
	}
	
	public function find_by_id($id=0)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public function find_by_url($url)
	{
		$result_array = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE url='{$url}' AND department='{$this->department}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public function list_all_banners()
	{
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$this->department."' AND post_type='attachment' AND title LIKE '%RotatingBanner%' ORDER BY title ASC");
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
	
	public static function dbFields()
	{
		return self::$dbFields;
	}
	
	public static function tblName()
	{
		return self::$tblName;
	}
	
	public function publish()
	{
		global $session, $database;
		
		$object = self::find_by_id($this->id);
		$this->id = $object->id;
		$this->updated = time();
		$this->updatedBy = $session->user()->id;
		$this->status = "published";
		if($object->post_type == 'post') {
			$this->body = $object->draft;
			$this->department = $object->department;
			$this->url = $object->url;
			$this->hardcopy();
		}
		
		return $this->update();
	}
	
	public function new_revision($post_id=null, $attributes=null)
	{
		global $session, $database;
		
		if( ! $post_id)
			return false;
		
		// 1. Get current post
		$post = $this->find_by_id($post_id);
		
		if( ! $post)
			return false;
		
		// If nothing has changed, don't save a revision
		if($attributes['body'] == $post->body && $attributes['title'] == $post->title)
			return false;
		
		// 2. Create a new revision
		$timestamp = time();
		
		$obj = new self;
		$obj->post_type = 'revision';
		$obj->updatedBy = $_SESSION['user_id'];
		$obj->post_created = time();
		$obj->parent_id = $post_id;
		$obj->title = $post->title;
		$obj->body = $post->body;
		$obj->status = 'inherit';
		$obj->url = $post_id.'-revision';
		$rev_id = $obj->create();
		
		// 3. Clean up revisions
		self::clean_revisions($post->id);
		
		// 4. Return
		return $rev_id;
	}
	
	public function restore_to_revision($rev_id=null)
	{
		if(!$rev_id)
			return false;
		
		// 1. Get content of revision
		$revision = self::find_by_id($rev_id);
		
		// 2. Store content to post
		$obj = new self;
		$obj->id = $revision->parent_id;
		$obj->body = $revision->body;
		$obj->title = $revision->title;
		$obj->updatedBy = $_SESSION['user_id'];
		$obj->updated = time();
		if( ! $obj->update())
			return false;
		else
			$obj = null;
		
		// 3. Delete revision
		$obj = new self;
		$obj->id = $rev_id;
		$obj->delete();
		
		// 4. Return
		return true;
	}
	
	public function clean_revisions($post_id=null)
	{
		global $db;
		
		$result = $db->query(  "DELETE FROM ".self::$tblName." 
								WHERE parent_id={$post_id} 
									AND post_type='revision' 
									AND id NOT IN (
										SELECT id 
										FROM (
											SELECT id 
											FROM ".self::$tblName." 
											WHERE parent_id={$post_id} 
												AND post_type='revision' 
											ORDER BY id DESC 
											LIMIT ".POST_REVISION_COUNT."
										) foo
									);");
		return $result;
	}
	
	public function get_all_revisions($post_id=null)
	{
		$revisions = self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE parent_id={$post_id} AND post_type='revision' ORDER BY id DESC;");
		
		return $revisions;
	}
	
	public function delete_all_revisions($post_id=null)
	{
		global $db;
		
		$result = $db->query("DELETE FROM ".self::$tblName." WHERE parent_id={$post_id} AND post_type='revision';");
		
		return $result;
	}
	
/*
	public function publish()
	{
		global $session, $database;
		
		$this->updated = time();
		$this->updatedBy = $session->user()->id;
		$this->status = 'published';
		return $this->update();
	}
*/
	
	private function hardcopy()
	{
		$backup = $this->filestring();
		if($handle = @fopen($backup, 'w')) { // overwrite
			$timestamp = "\r\n".'<!--% Published on '.strftime("%Y-%m-%d %H:%M:%S", time())." by ".User::get($this->updatedBy)->username." %-->";
			$content = preg_replace('/<!--%\s[A-Za-z0-9\s\-\:]+\s%-->/m','',$this->body);
			fwrite($handle, $content.$timestamp);
			fclose($handle);
			//chmod($backup, 0664);
		} else {
			global $session;
			$session->message('Could not store hard copy.');
		}
	}
	
	private function mark_file_deleted()
	{
		$filestring = $this->filestring();
		$oldfile = $filestring;
		$newfile = $filestring.'.del';
		rename($oldfile, $newfile);
	}
	
	public function filestring()
	{
		return PUBLIC_ROOT.DS.Department::grab($this->department)->subdir.DS.'content'.DS.$this->url.'.php';
	}
	
	public function icon($stat)
	{
		switch ($stat) {
			case 'draft':
				if($this->status=='draft')
					return image('nico', 'draft.gif');
				break;
				
			case 'main':
				if($this->id == Department::grab($this->department)->index_id)
					return image('nico', 'landing.gif');
				break;
				
			case 'nobackup':
				return image('nico', 'nobackup.gif');
				break;
			case 'parent':
				if($this->parent_id == 0)
					return image('nico', 'parent.gif');
				break;
		}
	}
	
	public function getCopy()
	{
		$file = $this->filestring();
		$systime = $this->updated;
		$filetime = @filemtime($file);
		switch($this->status)
		{
			case 'published':
				return ($filetime > $systime) ? @file_get_contents($file) : $this->body;
				break;
			case 'draft':
				return ($filetime > $systime) ? @file_get_contents($file) : $this->draft;
				break;
		}
	}
	
	public function count($type="post",$conds=array())
	{
		global $db;
		return (string) array_shift($db->count(self::$tblName, array_merge($conds,array('department' => $this->department, 'post_type'=>$type))));
	}
		
	public function getRecent($count)
	{
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$this->department."' AND post_type='post' ORDER BY id DESC LIMIT {$count}");
	}
	
	public function getRetired($days)
	{
		$backdate = mktime(0, 0, 0, date("m"), date("d")-$days, date("y")); 
		return self::find_by_sql("SELECT * FROM ".self::$tblName." WHERE department='".$this->department."' AND updated < {$backdate} AND post_type='post'");
	}
	
	public function getUpdated($count)
	{
		return self::find_by_sql('SELECT * FROM '.self::$tblName." WHERE department='".$this->department."' AND post_type='post' ORDER BY updated DESC LIMIT {$count}");
	}

}

?>