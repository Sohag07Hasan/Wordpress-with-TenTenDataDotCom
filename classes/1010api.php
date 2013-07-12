<?php
/**
 * API wrapper
 * */

class TenTenDataDotCom{
	
	const api_end_point = 'http://www.1010data.com/cgi-bin/gw.k/';
	
	//ncessary varialbes
	private $uid;
	private $pswd;
	private $sid;
	private $api;
	private $apiversion;
	
	//initiator function
	function __construct($uid, $pswd){
		$this->uid = $uid;
		$this->pswd = $pswd;
		$this->apiversion = '3';
	}
	
	
	//login proceduer
	function login(){
		$this->api = 'login';
		$this->sid = null;
		$api_url = $this->generate_api_url();
		
		return $this->request($api_url);
	}
	
	
	//generate query url to make the post request
	function generate_api_url(){
		$parameters = array(
			'uid' 		 => $this->uid,
			'pswd' 		 => $this->pswd,
			'api' 		 => $this->api,
			'apiversion' => $this->apiversion			
		);
		
		if(!empty($this->sid)){
			$parameters['sid'] = $this->$sid;
		}
		
		return self::api_end_point . '?' . http_build_query($parameters);
	}
	
	
	//request controller function
	private function request($url, $header = array(), $fields = ''){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		if($fields){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		}
		
		$response = curl_exec($ch);
		$status = curl_getinfo($ch);
		curl_close($ch);
		
		var_dump($url);
		var_dump($status);
		var_dump($response);
	}
	
}