<?php
class sifntUserAuth
{
	private $user_id;
	
	function __construct()
	{
	}

	// Quick function to call print_r within a HTML <pre>
	private function debug($arr){

		$backtrace = debug_backtrace();
		$file = $backtrace[0]['file'];
		$line = $backtrace[0]['line'];
		
		echo '<pre>';
		echo "<b>$file:$line</b>\n";
		print_r($arr);
		echo '</pre>';
	}

	public function getUserName()
	{
		$user_name = $this->getUserByIPAddress();

		return $user_name;
	}

	public function getUserId($user_name)
	{
		$user = User::where('username', '=', $user_name)->first();
		if($user){
			if($user->email && $user->password){
				return -1;
			}
			else{
				return $user->id;
			}
		}
		else{
			$user = new User;
			$user->username = $user_name;
			$user->save();

			return $user->id;
		}
	}

	private function getUserByIPAddress()
	{
		$user_name = false;

		// Air-Stream IPv4
		if(preg_match("/^10(\.[0-9]{1,3}){3}/", $_SERVER['REMOTE_ADDR'])){
			// RH external use hax
			if(preg_match("/^10\.108\.8\.([1][2][89]|[1][3-9][0-9]|[2][0-9]{2})/", $_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] == '10.108.8.17'){
				$user_name = 'anonymous';
			}
			else{
				$user_name = $this->getASUserName($_SERVER['REMOTE_ADDR']);
			}
		}

		// Hyperboria
		if(preg_match("/^fc[0-9a-f:]+/i", $_SERVER['REMOTE_ADDR'])){
			$user_name = str_replace(':', '', substr($_SERVER['REMOTE_ADDR'], 0, 9));
		}

		return $user_name ? $user_name : 'anonymous';
	}

	// Based on code by justo@air-stream.org - http://code.ridgehaven.wan/browser/phpbot/module/ip.php
	private function getASUserName($ip)
	{
		$ip = '10.112.0.65';
		if($member = Cache::get('asip-' . $ip . '-member')){
			return $member;
		}
		else{
			$postdata = "txtIPAddress=" . $ip;

			$ch = curl_init(); 
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 

			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);

			curl_setopt($ch, CURLOPT_URL, 'https://members.air-stream.wan/whois/search');
			$content = curl_exec($ch);
			curl_close($ch);

			//IP not Found
			if (!$content || strpos($content, 'No results!') !== FALSE)
			{
				return false;
			}
			//IP Found
			else
			{
				//Get table rows
				preg_match_all('/<tr>.*?<\/tr>/s',$content,$rows);
				$nodes = count($rows[0]) . "<br>";

				//echo "Count: " . $nodes;
				for($i=0; $i <= $nodes-1; $i++)
				{
					//Get <td>
					preg_match_all("/<td>.*?<\/td>/s",$rows[0][$i],$nodeinfo[$i]);

					//Set Shit Up
					$info[$i][0] = trim(strip_tags($nodeinfo[$i][0][0])); //header
					$info[$i][1] = trim(strip_tags($nodeinfo[$i][0][1])); //data

				}

				$ipAdress = $info[0][1];
				$ap = $info[1][1];
				$type = $info[2][1];
				$subnet = $info[3][1];
				$member = isset($info[4][1]) ? $info[4][1] : false;
				$host = gethostbyaddr($ipAdress);

				Cache::put('asip-' . $ip . '-member', $member, 1440);

				return $member;
			}
		}

	}
}
?>
