<?php
/**
 * API wrapper
 * */

class TenTenDataDotCom{
	
	const api_end_point = 'https://www2.1010data.com/cgi-bin/gw.k/';
	
	//ncessary varialbes
	private $uid;
	private $pswd;
	private $encrypted_pswd;
	private $sid;
	private $api;
	private $apiversion;
	
	//initiator function
	function __construct($uid, $pswd){
		$session = array();
		$session['uid'] = $this->uid = $uid;
		$session['pswd'] = $this->pswd = $pswd;
		$session['apiversion'] = $this->apiversion = '3';

		$this->set_session($session);
	}
	
	
	//login proceduer
	function login(){
		$this->api = 'login';
		$this->sid = null;
		$api_url = $this->generate_api_url();
		
		$headers = array();
		$headers[] = "Content-Type: text/xml";
		$headers[] = "Content-Length: 0";
		$headers[] = "Connection: close";

		$response = $this->request($api_url, $headers);
		$xml = simplexml_load_string($response);
		
		if((string)$xml->rc == '0'){
			$session = array(
				'rc' => '0',
				'sid' => (string)$xml->sid,
				'encrypted_pswd' => (string)$xml->pswd,
				'msg' => (string) $xml->msg,
				'is_loggedin' => true			
			);
		}
		else{
			$session = array(
				'rc' => (string)$xml->rc,
				//'sid' => (string)$xml->sid,
				//'encrypted_pswd' => (string)$xml->pswd,
				'msg' => (string) $xml->msg,
				'is_loggedin' => false
			);
		}
		
		return $this->set_session($session);
	}
	
	
	//generate query url to make the post request
	function generate_api_url(){
		$parameters = array(
			'uid' 		 => $this->uid,
			'pswd' 		 => $this->pswd,
			'api' 		 => $this->api,
			'apiversion' => $this->apiversion			
		);
		
		if(!empty($this->sid) && !empty($this->encrypted_pswd)){
			$parameters['sid'] = $this->sid;
			$parameters['pswd'] = $this->encrypted_pswd;
		}
		
		return self::api_end_point . '?' . http_build_query($parameters);
	}
	
	
	
	/**
	 * reurns the memebership
	 * */
	public function get_membership(){
		$this->refresh_authentication();
		$this->api = 'getuser';

		$headers = array();
		$headers[] = "Content-Type: text/xml";
		$headers[] = "Content-Length: 0";
		$headers[] = "Connection: close";
		
		$api_url = $this->generate_api_url();

		$fields = '<in><group>mhasan_mhasan_1010</group></in>';
		
		$response = $this->request($api_url, $headers, $fields);
		$xml = simplexml_load_string($response);
		var_dump($xml);
		die();
	
	}
	
	//refresh the authentication
	private function refresh_authentication(){
		if(isset($_SESSION['tenten']['is_loggedin']) && $_SESSION['tenten']['is_loggedin'] == true){
			$this->uid = $this->get_session('uid');
			$this->sid = $this->get_session('sid');
			$this->pswd = $this->get_session('pswd');
			$this->encrypted_pswd = $this->get_session('encrypted_pswd');
			$this->apiversion = '3';
		}
		else{
			$this->login();
		}
	}
	
	
	//request controller function
	private function request($url, $headers = array(), $fields = ''){
		$response = '<?xml version="1.0" ?>';
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		
		if($fields){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		}
		
		if($headers){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		$response .= curl_exec($ch);
		$status = curl_getinfo($ch);
		
		curl_close($ch);

		var_dump($status);
		
		return $response;
	}
	
	
	function set_session($data = array()){
		if(!empty($data)){
			foreach($data as $key => $value){
				$_SESSION['tenten'][$key] = $value;
			}
		}
	}
	
	
	function get_session($key){
		return $_SESSION['tenten'][$key];
	}
	
}