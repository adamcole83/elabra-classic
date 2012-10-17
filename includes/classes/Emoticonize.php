<?

class Emoticonize
{
	private $imageLocation = '/images/emoticons/';
	
	private $imageType = '.png';
	
	private $search_string;
	
	private $result_string;
	
	private $emoticons = array(
				'/>:-P/'	=>	'Angry-Tongue',
				'/>:P/'		=>	'Angry-Tongue',
				'/>:p/'		=>	'Angry-Tongue',
				'/>:-\(/'	=>	'Angry',
				'/>:\(/'	=>	'Angry',
				'/:-\]/'	=>	'Blushing',
				'/:\]/'		=>	'Blushing',
				'/:-\?/'	=>	'Confused',
				'/:\?/'		=>	'Confused',
				'/B-\)/'	=>	'Cool',
				'/:\'-\(/'	=>	'Crying',
				'/:\(\(/'	=>	'Crying',
				'/:\'\(/'	=>	'Crying',
				'/:-\[/'	=>	'Embarrassed',
				'/:\[/'		=>	'Embarrassed',
				'/>:-D/'	=>	'Evil',
				'/>:D/'		=>	'Evil',
				'/:-\(/'	=>	'Frown',
				'/:\(/'		=>	'Frown',
				'/:-o/'		=>	'Gasp',
				'/:o/'		=>	'Gasp',
				'/:O/'		=>	'Gasp',
				'/X-\(/'	=>	'Grimmace',
				'/X\(/'		=>	'Grimmace',
				'/:-D/'		=>	'Grin',
				'/:D/'		=>	'Grin',
				'/;-D/'		=>	'Grinning-Wink',
				'/;D/'		=>	'Grinning-Wink',
				'/O:-\)/'	=>	'Innocent',
				'/O:\)/'	=>	'Innocent',
				'/:-\*/'	=>	'Kiss',
				'/:\*/'		=>	'Kiss',
				'/:-X/'		=>	'Lips-Are-Sealed',
				'/:X/'		=>	'Lips-Are-Sealed',
				'/>:-\)/'	=>	'Mischievous',
				'/>:\)/'	=>	'Mischievous',
				'/:-\$/'	=>	'Money-Mouth',
				'/:\$/'		=>	'Money-Mouth',
				'/>:-\|/'	=>	'Not-Amused',
				'/>:\|/'	=>	'Not-Amused',
				'/:-!/'		=>	'Oops',
				'/:!/'		=>	'Oops',
				'/>:-O/'	=>	'Shouting',
				'/>:O/'		=>	'Shouting',
				'/\|-\)/'	=>	'Sleeping',
				'/:-\)/'	=>	'Smile',
				'/:\)/'		=>	'Smile',
				'/:->/'		=>	'Smirk',
				'/:>/'		=>	'Smirk',
				'/:-\|/'	=>	'Straight-Faced',
				'/:\|/'		=>	'Straight-Faced',
				'/:-P/'		=>	'Tongue',
				'/:P/'		=>	'Tongue',
				'/:p/'		=>	'Tongue',
				"/:-\//"	=>	'Undecided',
				"/:\//"		=>	'Undecided',
				'/;-P/'		=>	'Wink-Tongue',
				'/;P/'		=>	'Wink-Tongue',
				'/;p/'		=>	'Wink-Tongue',
				'/;-\)/'	=>	'Wink',
				'/;\)/'		=>	'Wink'
	);
	
	function __construct($string='')
	{
		$this->search_string = $string;
		if($this->search_string != '')
		{
			$this->displayEmoticons();
		}
	}
	
	public function displayEmoticons()
	{
		if($this->search_string != '')
		{
			foreach($this->emoticons as $emoticon => $image)
			{
				$patterns[] = $emoticon;
				$replace[] = $this->setImageLink($image);
			}
			$string = preg_replace($patterns, $replace, $this->search_string, '-1', $count);
			
			$this->result_string = $string;
		}
		else
		{
			$this->result_string = "String not set.";
		}
	}
	
	public function setImageLink($image)
	{
		return '<img class="emoticon" src="'.$this->imageLocation.$image.$this->imageType.'" width="18" height="18" alt="'.$image.'" />';
	}
	
	public function setString($string)
	{
		$this->search_string = $string;
	}
	
	public function getEmoticons()
	{
		foreach($this->emoticons as $e => $i)
		{
			$e = preg_replace('/\//', '', stripslashes($e))."<br ./>";
			$emoticons[$e] = $i;
		}
		return $emoticons;
	}
	
	public function getContent()
	{
		return $this->result_string;
	}
}

?>