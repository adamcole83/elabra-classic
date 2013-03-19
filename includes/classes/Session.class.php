<?


class Session
{

	private $logged_in = false;
	public $user_id;
	private $user_dept;
	private $message;

	/**
	  * Session class sets up and stores user session information
	  *
	  */
	function __construct()
	{
		session_start();
		if( isset($_SESSION['user_id']) && $_SESSION['user_id'] == 'TEMP')
		{
			$this->logout();
		}
		foreach($_SERVER as $key=>$value) defined($key) ? null : @define($key,$value);
		$this->check_message();
		$this->check_timeout();
		$this->check_login();
		if($this->logged_in){
			// actions to take right away if user is logged in
			$_SESSION['timeout'] = time();
		}else{
			// actions to take right away if user is not logged in
		}
	}

	/**
	  * Logs user in, stores user_id in session
	  *
	  * @param (object) user
	  *
	  */
	public function login($user)
	{
		if($user){
			$this->user_id = $_SESSION['user_id'] = $user->id;
			if(!isset($_SESSION['department']) OR (isset($_SESSION['department']) && $user->department != $_SESSION['department'] && $user->group_id != '9'))
			{
				$this->user_dept = $_SESSION['department'] = $user->department;
			}
			else
			{
				$this->user_dept = $_SESSION['department'];
			}
			$this->logged_in = true;
			User::timestamp($user->id);
		}
	}

	/**
	  * Logout, unsets user session
	  *
	  */
	public function logout()
	{
		unset($_SESSION['user_id']);
	    unset($this->user_id);
	    unset($_SESSION['timeout']);
	    $this->logged_in = false;
	}

	/**
	  * Returns user access status
	  *
	  * @return (Boolean) logged_in
	  *
	  */
	public function is_logged_in()
	{
		return $this->logged_in;
	}

	/**
	  *
	  *
	  * @param (DataType)
	  * @return (DataType)
	  *
	  * @private
	  */
	private function check_login()
	{
		if(isset( $_SESSION['user_id']) ){
			$this->user_id = $_SESSION['user_id'];
			$this->logged_in = true;
		}else{
			unset($this->user_id);
			$this->logged_in = false;
		}
	}

	/**
	  * Stores a message in session
	  *
	  * @param (string) message
	  * @return (string||null) message
	  *
	  */
	public function message($msg=""){
		if(!empty($msg)){
			$_SESSION['message'] = $msg;
		}else{
			return $this->message;
		}
	}

	/**
	  * Checks for messages upon logging in
	  *
	  * @private
	  */
	private function check_message()
	{
		if(isset($_SESSION['message'])){
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		}else{
			$this->message = "";
		}
	}

	private function check_timeout()
	{
		if(isset($_SESSION['timeout'])){
			$inactive = 7200; // 60 minutes
			$session_life = time() - $_SESSION['timeout'];
			if($session_life > $inactive){
				$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
				$this->logout();
				$this->message("No activity within {$inactive} seconds; please log in again.");
				redirect_to('/admin/login.php');
			}
		}
	}

	public function user($user_id=null)
	{
		$id = isset($user_id) ? $user_id : $this->user_id;
		return User::find_by_id($id);
	}

	public function check_access()
	{
		$action = $_GET['action'];

		if($action){
			$action = $action.'s';
			if($_GET['who'])
				if($_GET['who'] == 'own')
					$action = 'edit_own_profile';
			if($action == 'publish_pages')
				return true;
			$action = preg_match('/select/i', $action) ? preg_replace('/select/i', 'view', $action) : $action;
			$user = self::user();
			if(Group::can($action)){
				return true;
			}else{
				log_action('ACCESS DENIED', $user->username.' attempted to access '.$action.' and currently does not have access.');
				redirect_to('index.php');
			}
		}
	}

}

$session = new Session();

?>