## vChallenge - PHP Bot Challenge
<code>@ vChallenge - Open source, PHP Bot Checker.</code>
## Requirements
PHP version 5.x (+) is required for running this.
## Modes
- Google reCAPTCHA.<br>
<strong>Syntax:</strong> <code>$vCHALL->enable('reCAPTCHA');</code>
- Checking in 5 seconds.<br>
<strong>Syntax:</strong> <code>$vCHALL->enable('5s');</code>
## Integration
<pre>
/*
****************
* vChallenge PHP Bot Checker
* Global import (for all your files)
*
*****************
*/	
require_once(__DIR__ . '/class.php');
$vCHALL = new vChallenge\vCHALL();
$vCHALL->enable('reCAPTCHA'); // Mode: 5s (checking in 5 seconds) or reCAPTCHA (Google reCAPTCHA validation).
</pre>
## ReCAPTCHA settings.
To use the reCAPTCHA mode, you must generate a public key and a private key.
https://www.google.com/recaptcha/<br>
In <strong>class.php line 25 and 26 :</strong>
<pre>
$this->reCAPTCHA_KEY = "YOUR_KEY"; // Your reCAPTCHA key.
$this->reCAPTCHA_SECRET_KEY = "YOUR_PRIVATE_KEY"; // Your reCAPTCHA private key.
</pre>
## Whitelist
To allow specific IP addresses, add in array() <strong>line 117</strong>. Like this:<br>
<pre>
$ips = array(
'127.0.0.1',
'1.1.1.1'
);
</pre>
Added IP addresses will not be checked. All requests will be approved.
