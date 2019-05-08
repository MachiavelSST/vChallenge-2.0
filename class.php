<?php
 /*
 *  vChallenge 1.0 - PHP Bot Checker Open-Source
 *  ********************************
 *  @Author: Machiavel
 *  @Telegram: @Machiavelisme
 *  @Version: 1.0
 *  ********************************
 */

namespace vChallenge;
define('VCHALLENGE_VERSION', '1.0');
define('VCHALLENGE_ADMIN', 'kingzion@protonmail.com');
define('VCHALLENGE_SOURCE', 'https://github.com/Machiavelisme');

class vCHALL {
	
	private static $type = "5s"; // Default challenge type ("reCAPTCHA" or "5s").
	private static $cookieName = "__vcheck"; // Name session and cookie for checked user.
	
	function __construct(){
		$this->rootDomain = preg_replace('/^www/i', '', $_SERVER['SERVER_NAME']);
		$this->setHeader = 'REMOTE_ADDR';
		$this->tokenTime = 3600; // Session duration (1 hours by default).
		$this->reCAPTCHA_KEY = "YOUR_KEY"; // Your reCAPTCHA key.
		$this->reCAPTCHA_SECRET_KEY = "YOUR_PRIVATE_KEY"; // Your reCAPTCHA private key.
		@session_name('__vCHALL');
		@session_set_cookie_params($this->tokenTime, '/', $this->rootDomain);
		@session_start();
		@ob_start();
	}
	
	function allowChallenge(){
		$types = array('reCAPTCHA','5s'); // Challenge types allowed.
		return in_array(self::$type, $types) ? true : false;
	}
	
	function proxyIPHeader(){
		$headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR'];
		foreach($headers as $header){
			!empty($_SERVER[$header]) ? $this->setHeader = $header : $this->setHeader = 'REMOTE_ADDR';
			if(filter_var($_SERVER[$this->setHeader], FILTER_VALIDATE_IP))
				return $_SERVER[$this->setHeader];
		}
	}
	
	function getID(){
		$httpheader = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		$httpheader = $httpheader. isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : null;
		$httpheader = $httpheader. isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
		$httpheader = $httpheader. isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$date = date('l, F jS Y @ H:i:s');
		$time = time();
		$rayID = hash('md5', $_SERVER[$this->setHeader] . $httpheader . $date . $time. self::$type);
		return $rayID;
	}
	
	function returnHeader($code){
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $code);
	}
	
	function getUA(){
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$UA = preg_replace('/[^A-Za-z0-9\-]/', '', $_SERVER['HTTP_USER_AGENT']);
			return $UA;
		} else {
			return md5(rand());
		}
	}

	function showHTML(){
		$this->returnHeader('503 Service Temporarily Unavailable');
		if($this->allowChallenge()){
			$code = "<DOCTYPE html><html lang='en' xmlns='//www.w3.org/1999/xhtml'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><meta http-equiv='X-UA-Compatible' content='ie=edge'><title>vChallenge ".VCHALLENGE_VERSION."</title><link href='https://bootswatch.com/4/flatly/bootstrap.css' rel='stylesheet' type='text/css'/><script src='https://www.google.com/recaptcha/api.js'></script></head><body><div class='container'><div id='banner' class='page-header'><div class='jumbotron'><h2 class='display-5'>vChallenge ".VCHALLENGE_VERSION."</h2><p class='lead'>Please, complete the security step to access <span class='badge badge-primary'>{$_SERVER['SERVER_NAME']}</span></p></div><div class='card border-primary mb-3'><div class='card-header'>Security check</div><div class='card-body text-center'><form method='POST'><input type='hidden' name='rayID' value='{$this->getID()}'><h4 class='card-title'>Anti-Bot Verification Step</h4>"; if(self::$type == "reCAPTCHA") $code = $code."<p class='card-text'>Complete reCAPTCHA challenge to access web server properties.</p><center><div class='g-recaptcha' data-sitekey='{$this->reCAPTCHA_KEY}'></div></center><br><button type='submit' name='submit' class='btn btn-primary'>Continue</button>"; if(self::$type == "5s") $code = $code."<p class='card-text'>Please wait 5 seconds to continue browsing.</p><img src='https://i.imgur.com/8WHAIqS.gif' width='120'></img><script>setTimeout(function(){location.reload();},5100);</script>";
			$code = $code."</form></div></div><hr><h5 class='text-center'>Token ID: <span class='badge badge-primary'>{$this->getID()}</span></h5><p class='text-center'>If you have troubleshoot, please <a href='mailto:".VCHALLENGE_ADMIN."'>contact owner</a>.</p><hr><div class='card'><div class='card-body'><a href='".VCHALLENGE_SOURCE."' class='card-link'>Github</a><p class='card-text'>@ vChallenge ".VCHALLENGE_VERSION." - Open source PHP Bot Challenge.</p></div></div></div></div></body></html>";
			die($code); // show challenge page.
		} else {
			die('Unknow challenge mode.'); // incorrect challenge mode.
		}
	}
	
	function whitelist(){
		$ips = array("1.1.1.1"); // IP addresses allowed to access web server properies without challenge.
		return in_array($_SERVER[$this->setHeader], $ips) ? true : false;
	}
	
	function checkData(){
		if($this->whitelist()):
			return true;
		else:
			if(isset($_COOKIE[self::$cookieName]) && isset($_SESSION[self::$cookieName])){
				if($_SESSION[self::$cookieName] == $_COOKIE[self::$cookieName]){
					if($_SESSION['VCHALL-TIME'] >= time()-$this->tokenTime){
						if(!isset($_SESSION['VCHALL-IP'])){
							$_SESSION['VCHALL-IP'] = $_SERVER[$this->setHeader];
						} else {
							if ($_SESSION['VCHALL-IP'] != $_SERVER[$this->setHeader]){
								$this->killSession = true;
								return false;
							}
						}
						if (isset($_SERVER['HTTP_USER_AGENT'])){
							if($_SESSION['VCHALL-UA'] != $this->getUA()){
								$this->killSession = true;
								return false;
							}
						}
						return true;
					} else {
						$this->killSession = true;
						return false;
					}
				} else {
					$this->killSession = true;
					return false;
				}
			}
			if(isset($this->killSession)){
				if($this->killSession){
					setcookie(self::$cookieName, "", time()-$this->tokenTime, "/", $this->rootDomain);
					@session_destroy();
					unset($_SESSION[self::$cookieName]);
					unset($_SESSION['VCHALL-TIME']);
					unset($_SESSION['VCHALL-IP']);
					unset($_SESSION['VCHALL-UA']);
					unset($_SESSION['VCHALL-5S']);
				}
			}
		endif;
	}
	
	function showChallenge(){
		if(!$this->checkData()):
			if(self::$type == "reCAPTCHA") {
				if(isset($_POST['submit'])) {
					$secret = $this->reCAPTCHA_SECRET_KEY;
					$remoteip = $_SERVER[$this->setHeader];
					$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
					. $secret
					. "&response=" . $_POST['g-recaptcha-response']
					. "&remoteip=" . $remoteip;
					$decode = json_decode(file_get_contents($api_url), true);
					if($decode['success']){
						setcookie(self::$cookieName, $_SESSION[self::$cookieName], time()+$this->tokenTime, "/", $this->rootDomain);
						$_SESSION['VCHALL-TIME'] = time();
						$_SESSION['VCHALL-IP'] = $_SERVER[$this->setHeader];
						$_SESSION['VCHALL-UA'] = $this->getUA();
						return true;
					}
				}
			}
			if(self::$type == "5s"){
				if(isset($_SESSION['VCHALL-5S']) && $_SESSION['VCHALL-5S'] <= time()-5) {
					setcookie(self::$cookieName, $_SESSION[self::$cookieName], time()+$this->tokenTime, "/", $this->rootDomain);
					$_SESSION['VCHALL-TIME'] = time();
					$_SESSION['VCHALL-IP'] = $_SERVER[$this->setHeader];
					$_SESSION['VCHALL-UA'] = $this->getUA();
					if(isset($_SESSION['VCHALL-5S']))
						unset($_SESSION['VCHALL-5S']);
					return true;
				} else {
					$_SESSION['VCHALL-5S'] = time();
				}
			}
			setcookie('__vuid', hash('sha512', $this->getID()), time()+$this->tokenTime, "/", $this->rootDomain);
			$_SESSION[self::$cookieName] = $this->getID();
			$this->showHTML();
		endif;
	}
	
	function enable($type = '5s'){
		self::$type = $type;
		$_SERVER['REMOTE_ADDR'] = $this->proxyIPHeader();
		$this->showChallenge();
	}
}	

?>
