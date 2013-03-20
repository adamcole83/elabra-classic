<?

class Uploader
{
	private $directory;
	private $target_path;
	private $max_file_size = 300000;
	private $file_name;
	private $file_tmpname;
	private $file_type;
	private $file_size;
	private $file_error;
	private $content;
	private $err;
	private $allowed = array('text/html');
	public $keep_file = false;
	
	function __construct()
	{
		$this->directory = SITE_ROOT.DS.'layouts'.DS.'upload'.DS.'files';
	}
	
	public function newFile($file)
	{
		if($file)
		{
			$this->file_name = $file['name'];
			$this->file_tmpname = $file['tmp_name'];
			$this->file_type = $file['type'];
			$this->file_size = $file['size'];
			$this->file_error = $file['error'];
			$this->target_path = $this->directory.DS.basename($this->file_name);
		}
		
		if(!$this->file_name)
			$this->err = "Filename is not defined!";
			
		if(!in_array($this->file_type, $this->allowed))
			$this->err = "Filetype not allowed";
			
		if($this->file_size > $this->max_file_size)
			$this->err = "File size too large.";
			
		if(!isset($this->err))
		{			
			if(!is_uploaded_file($this->file_tmpname))
				$this->err = "File ".$this->file_name." is not uploaded correctly";
				
			if(!move_uploaded_file($this->file_tmpname, $this->target_path))
				$this->err = "File was not copied to directory";
		}
	}
	
	public function get_file_contents()
	{
		if(file_exists($this->target_path))
		{
			$fh = fopen($this->target_path, 'r');
			$contents = fread($fh, 8000);
			fclose($fh);
			
			if(!$this->keep_file)
				unlink($this->target_path);
			
			return $contents;
		}
		else
		{
			$this->err = "File does not exist to obtain contents";
		}
	}
	
	public function error()
	{
		return $this->err;
	}
	
	public function is_complete()
	{
		if(isset($this->err))
			return false;
		else
			return true;
	}
}

?>