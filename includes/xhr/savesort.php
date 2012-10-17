<?
	require_once('../initialize.php');

	foreach( $_POST as $key => $value ) {
		if($value != 'undefined') {
			if(!preg_match('/d[0-9]+/i', $value)) {
				$content = new Content();
				$content->id = $value;
				$content->menu_order = $key;
				$content->save();
			}
			else
			{
				$value = preg_replace('/d/i','', $value);
				$meta = $db->fetch_array($db->select('cms.deptmeta','*', array('umeta_id'=>$value)));
				$meta = explode(';',$meta['meta_value']);
				$meta[0] = $key;
				$db->update('cms.deptmeta', array('umeta_id' => $value), array('meta_value' => implode(';',$meta)));
			}
		}
	}
	
?>