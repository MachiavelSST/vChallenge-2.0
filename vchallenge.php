<?php
 /*
 *  vChallenge 2.0 - PHP Bot Checker Open-Source
 *  ********************************
 *  @Author: Machiavel
 *  @Telegram: @MachiavelSST
 *  @Version: 2.0
 *  ********************************
 */
 
define('VCHALLENGE_VERSION', '2.0');
define('VCHALLENGE_ADMIN', 'admin@mail.com');
define('VCHALLENGE_SOURCE', 'https://github.com/MachiavelSST');

class vChallenge {

	public function __construct($challenge = "reCAPTCHA", $reCAPTCHA_key = "", $reCAPTCHA_PRIVATE_KEY = ""){
		$this->getToken = "CSRF-Token";
		$this->getCookie = "X-CSRF-TOKEN";
		$this->getExpire = "CSRF-Expire";
		$this->IPHeader = "REMOTE_ADDR";
		$this->Challenge = $challenge;
		$this->tokenTime = 3600; // Session duration (1 hours by default).
		$this->reCAPTCHA_KEY = $reCAPTCHA_key;
		$this->reCAPTCHA_PRIVATE_KEY = $reCAPTCHA_PRIVATE_KEY;
		try {
			if(!in_array($this->Challenge, array('5s', 'reCAPTCHA'))){
				throw new Exception('Unknow challenge.');
			}
			if($this->Challenge == "reCAPTCHA"){
				if(empty($this->reCAPTCHA_KEY) || empty($this->reCAPTCHA_PRIVATE_KEY)){
					throw new Exception('Challenge is set to reCaptcha, please set Key and Private Key.');
				}
			}
		} catch (Exception $e) {
			exit($e->getMessage());
		}
		@session_name('__vCHALL');
		@session_start();
		@ob_start();
		$this->setChallenge();
	}
	private function isValid($element){
		return array_key_exists($element, $_SESSION) ? true : false;
	}
	private function getElement($element){
		if($this->isValid($element))
			return $_SESSION[$element];
	}
	private function createElement(array $parameters = []){
		if(!empty($parameters)){
			foreach($parameters as $element => $value){
				$_SESSION[$element] = $value;
			}
		}
	}
	private function deleteElement(array $parameters = []){
		if(!empty($parameters)){
			foreach($parameters as $element){
				unset($_SESSION[$element]);
			}
		}
	}
	private function checkExpire(){
		if($this->isValid($this->getExpire)){
			$timeout = time() - $this->getElement($this->getExpire);
			return $timeout < $this->tokenTime ? true : false;
		}
		return false;
	}
	private function checkClient(){
		if(isset($_COOKIE[$this->getCookie])){
			$client = explode('refCS', $this->getElement($this->getToken));
			$ipAddress = $client[1];
			$userAgent = $client[2];
			return password_verify($this->getElement($this->getToken), $_COOKIE[$this->getCookie]) && $this->getIP() == $ipAddress && $this->getUA() == $userAgent ? true : false;
		}
		return false;
	}
	private function getIP(){
		$headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR'];
		foreach($headers as $header){
			$IPHeader = !empty($_SERVER[$header]) ? $header : 'REMOTE_ADDR';
			$this->IPHeader = $IPHeader;
			if(filter_var($_SERVER[$IPHeader], FILTER_VALIDATE_IP)) {
				$IP = str_replace(array('.',':','::'), array('','',''), $_SERVER[$IPHeader]);
					return $IP;
			}
		}
		return "127001";
	}
	private function getUA(){
		$UA = isset($_SERVER['HTTP_USER_AGENT']) ? md5(preg_replace('/[^A-Za-z0-9\-]/', '', $_SERVER['HTTP_USER_AGENT'])) : md5(rand());
		return $UA;
	}
	private function createNewToken(){
		$token = md5(uniqid(rand(), true)) . session_id() . count($_SESSION) . count($_SERVER) . "refCS" . $this->getIP() . "refCS" . $this->getUA();
		return $token;
	}
	private function getServerProtocol(){
		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
		header($protocol . ' 503 Service Temporarily Unavailable');
	}
	public function setReCAPTCHA($key = "", $private_key = ""){
		if(empty($key) || empty($private_key)){
			die("Challenge is set to reCaptcha, please set Key and Private Key.");
		}
		$this->reCAPTCHA_KEY = $key;
		$this->reCAPTCHA_PRIVATE_KEY = $private_key;
	}
	private function getHTML(){
		$this->getServerProtocol();
		$code = "<DOCTYPE html><html lang='en' xmlns='//www.w3.org/1999/xhtml'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><meta http-equiv='X-UA-Compatible' content='ie=edge'><title>vChallenge ".VCHALLENGE_VERSION."</title><link href='https://bootswatch.com/4/flatly/bootstrap.css' rel='stylesheet' type='text/css'/><script src='https://www.google.com/recaptcha/api.js'></script></head><body><div class='container'><div id='banner' class='page-header'><div class='jumbotron'><h2 class='display-5'>vChallenge ".VCHALLENGE_VERSION."</h2><p class='lead'>Please, complete the security step to access <span class='badge badge-primary'>{$_SERVER['SERVER_NAME']}</span></p></div><div class='card border-primary mb-3'><div class='card-header'>Security check</div><div class='card-body text-center'><form method='POST'><input type='hidden' name='rayID' value='{$this->createNewToken()}'><h4 class='card-title'>Anti-Bot Verification Step</h4>";
		if($this->Challenge == "reCAPTCHA")
			$code = $code."<p class='card-text'>Complete reCAPTCHA challenge to access web server properties.</p><center><div class='g-recaptcha' data-sitekey='{$this->reCAPTCHA_KEY}'></div></center><br><button type='submit' name='submit' class='btn btn-primary'>Continue</button>";
		if($this->Challenge == "5s")
			$code = $code."<p class='card-text'>Please wait 5 seconds to continue browsing.</p><img src='https://i.imgur.com/8WHAIqS.gif' width='120'></img><script>setTimeout(function(){location.reload();},5100);</script>";
		$code = $code."</form></div></div><hr><h5 class='text-center'>Token ID: <span class='badge badge-primary'>{$this->createNewToken()}</span></h5><p class='text-center'>If you have troubleshoot, please <a href='mailto:".VCHALLENGE_ADMIN."'>contact owner</a>.</p><hr><div class='card'><div class='card-body'><a href='".VCHALLENGE_SOURCE."' class='card-link'>Github</a><p class='card-text'>@ vChallenge ".VCHALLENGE_VERSION." - Open source PHP Bot Challenge.</p></div></div></div></div></body></html>";
		die($code); // show challenge page.
	}
	private function checkWhitelistedIPs(){
		$IPs = array("127.0.0.5"); // IP addresses allowed to access web server properies without challenge.
		return in_array($_SERVER[$this->IPHeader], $IPs) ? true : false;
	}
	private function checkToken(){
		if(!$this->checkWhitelistedIPs()){
			switch(true){
				case !$this->isValid($this->getToken):
				case !$this->checkExpire():
				case !$this->checkClient():
					$this->deleteElement([
						$this->getToken,
						$this->getExpire
					]);
					return false;
				break;
				default:
					/* Token is valid, request accepted. */
					return true;
				break;
			}
		}
		return true;
	}
	private function setChallenge(){
		if(!$this->checkToken()){
			switch(strtoupper($this->Challenge)){
				case "5S":
					if($this->isValid('5S') && $this->getElement('5S') <= time()-5){
						$this->createElement([
							$this->getToken => $this->createNewToken(),
							$this->getExpire => time()
						]);
						setcookie($this->getCookie, password_hash($this->getElement($this->getToken), PASSWORD_DEFAULT), time()+$this->tokenTime, "/");
						$this->deleteElement([
							"5S"
						]);
						return true;
					} else {
						$this->createElement([
							"5S" => time()
						]);
					}
				break;
				case "RECAPTCHA":
					if(isset($_POST['submit'])){
						$secret = $this->reCAPTCHA_PRIVATE_KEY;
						$remoteip = $_SERVER[$this->IPHeader];
						$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
						. $secret
						. "&response=" . $_POST['g-recaptcha-response']
						. "&remoteip=" . $remoteip;
						$decode = json_decode(file_get_contents($api_url), true);
						if($decode['success']){
							$this->createElement([
								$this->getToken => $this->createNewToken(),
								$this->getExpire => time()
							]);
							setcookie($this->getCookie, password_hash($this->getElement($this->getToken), PASSWORD_DEFAULT), time()+$this->tokenTime, "/");
							return true;
						}
					}
				break;
			}
			$this->getHTML();
		}
	}
}

?>