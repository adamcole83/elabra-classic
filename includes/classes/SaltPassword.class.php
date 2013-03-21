<?php
/*************************
 * SaltPassword Class Copyright (c)- 2010 - Ákos Nikházy.
 * All rights reserved.   This program and the accompanying materials
 * are made available under the terms of the 
 * GNU General Public License (GPL) Version 2, June 1991, 
 * which accompanies this distribution, and is available at: 
 * http://www.opensource.org/licenses/gpl-license.php
 
 *It creates new password with salt, to save in database. It also salt given password with given salt to identify user.
 *Version 1.0
**************************/
class SaltPassword 
{
	protected $lenght = 8;
	protected $special = false;
	
	
	public function __construct($_lenght=8,$_special=false)
    {
        $this->lenght = $_lenght;
		$this->special = $_special;
    }
	/*
	 *Creates random string with letters,numbers and with optional lenght and special characters. 
	 *Letters will randomly upper and lowercase
	 */
	private function makeRandomString()
	{
		$abc = array();

		foreach(range('a','z') as $a)
		{//Array with all letters in it
			$abc[] = $a; 
		}
		
		foreach(range(0,9) as $a)
		{//All numbers in array too
			$abc[] = $a; 
		}
		
		if($this->special)
		{//Specials in array
			$abc[] = '!';
			$abc[] = '%';
			$abc[] = '+';
			$abc[] = '-';
			$abc[] = '_';
			$abc[] = ' ';
			$abc[] = '*';
			$abc[] = '=';
			$abc[] = '.';
			$abc[] = '?';
			//$abc[] = ':';
			//$abc[] = ',';
			//$abc[] = ';';
			//You can add more characters for the array as you wish. 
			//Always add only one character per line so the password lenght will not affected.
		}
		
		for($i=0;$i<$this->lenght;$i++)
		{
			shuffle($abc);//Shuffle the array in every loop, so its more random
			$j 			 = rand(0,count($abc)-1);//Random array element form shuffled array. Randomnesssssss ;)
			$randomText .= (rand(0,10)%2 == 0) ? strtoupper($abc[$j]) : $abc[$j]; //If its not an odd number then it will be upper case
		}
		
		return $randomText;
	}
	
	/*
	 *Creates a random salt
	 */
	private function createSalt()
	{
		return md5($this->makeRandomString(20,true).$this->makeRandomString(20,true));
	}
	
	/*
	 *Salt a givn password with a given salt
	 */
	public function saltPw($pw,$salt)
	{
		return md5($pw.'SaltPasswordClass'.$salt);//Never change the md5 parametes if you started to use it.
	}
	
	/*
	 *Crate a new password and its salt and salted version.
	 */
	public function createPw($password="")
	{
		if($this->lenght >= 6 && $this->lenght <= 20)
		{//It creates password between 6 and 20 characters. Smaller is to small, bigger is too big. But modify the values as you wish!
			$r = array();
			
			$r['plain_password'] 	=  (empty($password)) ? $this->makeRandomString() : $password; //You can send this in email to the user. Never save it in db/file
			$r['salt'] 			= $this->createSalt(); //Save this in db/file
			$r['salted_password'] 	= $this->saltPw($r['plain_password'],$r['salt']); //Save this in db/file
			
			return $r;
		}
		else
		{
			return false;//Return false if to small or too big number was given as lenght 
		}
	}
}

$SaltPassword = new SaltPassword();
$salt =& $SaltPassword;
?>
