<?

class EmailNotification
{
	protected static $header = "From: MU Office of Communications <DoNotReply@medicine.missouri.edu>";
	public $to;
	public $subject;
	public $message;
	public $redirect;
	public $sitename;
	
	public function send()
	{
		return mail($this->to, $this->subject, $this->message, self::$header);
	}
	
	public function send_temp_password($username='', $password='')
	{
		$userencoded = base64_encode($username);
		$sitename = $this->sitename;
		$subject = "Password Update: School of Medicine CMS";
		$message = "Hello {$username},\r\n\r\n".
					"Your temporary password has been updated. You will be required to change your password when you first log in. If this was done in error, please contact the Office of Communications at ".IT_PHONE." immediately.\r\n\r\n".
					"Temporary Password: {$password}\r\n\r\n".
					"Create new password here:\r\n".
					"http://medicine.missouri.edu/admin/changepassword.php?u={$userencoded}&r={$this->redirect}&site={$sitename}";
		$mail = mail($this->to, $subject, $message, self::$header);
		return $mail;
	}
	
	public function send_new_password($username='', $password='')
	{
		$userencoded = base64_encode($username);
		$sitename = $this->sitename;
		$subject = "Welcome! School of Medicine CMS";
		$message = "Hello,\r\n\r\n".
					"You are now a new user of the University of Missouri School of Medicine Content Management System. You will be required to change your password when you first log in. If this was done in error, please contact the Office of Communications at ".IT_PHONE." immediately.\r\n\r\n".
					"Username: {$username}\r\n".
					"Password: {$password}\r\n\r\n".
					"Login here:\r\n".
					"http://medicine.missouri.edu/admin/changepassword.php?u={$userencoded}&r={$this->redirect}&site={$sitename}";
		$mail = mail($this->to, $subject, $message, self::$header);
	//	error_log('Email sent to '.$username);
		return $mail;
	}
	
	public function send_registration_notification($uData)
	{
		$subject = "SOM: Registration Request";
		$message = "User registration request. \r\n";
			foreach($uData as $key => $value) {
				$message .= "$key: $value\r\n";
			}
			$dataStr = base64_encode("username={$uData['username']}&email={$uData['email']}");
		$message .= "\r\nApprove:\r\n".
					"http://medicine.missouri.edu/admin/register.php?a={$dataStr}&r=1 \r\n\r\n".
					"Deny:\r\n".
					"http://medicine.missouri.edu/admin/register.php?a={$dataStr}&r=2";
		$mail = mail($this->to, $subject, $message, self::$header);
		return $mail;
	}
	
}

?>