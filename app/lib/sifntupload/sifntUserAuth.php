<?php
class sifntUserAuth
{
	private $user_id;
	private $auth_trusted = false;
	
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
		$user_name = false;

		if(!$user_name && Config::get('auth.cas')) {
			if($user_name = $this->getUserByCas()) {
				$this->auth_trusted = true;
			}
		}
		
		if (!$user_name) {
			$user_name = $this->getUserByIPAddress();
		}

		return $user_name;
	}

	public function getUserId($user_name)
	{
		$user = User::where('username', '=', $user_name)->first();
		if($user){
			if(!$this->auth_trusted && $user->email && $user->password){
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
		if($member = Cache::get('asip-' . $ip . '-member')){
			return $member;
		}
		else{
			$url="https://members.air-stream.wan/login";
			$username = Config::get('auth.members.username');
			$password = Config::get('auth.members.password');
			$postdata = "username=".$username."&password=".$password."&txtIPAddress=" . $ip;

			$strCookie = 'symfony=3n4oq02k94830sh571b49c3dm2; path=/';
			session_write_close();
			
			$ch = curl_init(); 
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_URL, $url); 
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_COOKIE, $strCookie); 
			curl_setopt ($ch, CURLOPT_REFERER, $url); 

			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
			curl_setopt ($ch, CURLOPT_POST, 1); 
			curl_exec ($ch); 

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
				$html = str_get_html($content);
				$table = $html->find('table');
				$rows = $table[0]->find('tr');
				$data = array();
				foreach($rows as $row) {
					$cols = $row->find('td');
					foreach($cols as $col) {
						$data[] = $col->plaintext;
					}
				}
				
				$info = array(
					'ip' => trim($data[1]),
					'ap' => trim($data[3]),
					'type' => trim($data[5]),
					'subnet' => trim($data[7]),
					'member' => isset($data[9]) ? trim($data[9]) : false,
					'host' => gethostbyaddr(trim($data[1])),
				);

				$ip = $info['ip'];
				$member = $info['member'];
				Cache::put('asip-' . $ip . '-member', $member, 1440);

				return $member;
			}
		}
	}

	private function getUserByCas()
	{
		$user_name = false;

		if($auth = phpCAS::checkAuthentication()) {
			$user_name = phpCAS::getUser();
		}

		return $user_name;
	}

	public static function logout()
	{
		Auth::logout();

		if(Config::get('auth.cas') && phpCAS::isAuthenticated()) {
			phpCAS::logout(array('url' => baseURL()));
		}
	}
}
?>
