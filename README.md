## vChallenge - PHP Bot Challenge
<code>@ vChallenge 2.0 - Open source, PHP Bot Checker.</code>
## Requirements
PHP version 5.x (+) is required for running this.
## Call vChallenge PHP Class
```php
require_once(__DIR__ . '/vchallenge.php');
```
## Google ReCAPTCHA Challenge
To use the reCAPTCHA mode, you must generate a public key and a private key.
https://www.google.com/recaptcha/<br>
Define the class like this:
```php
$vCHALL = new vChallenge('reCAPTCHA', 'YOUR_KEY', 'YOUR_PRIVATE_KEY');
```
## 5 Seconds Challenge
To check a client for 5 seconds, just do this:
```php
$vCHALL = new vChallenge('5s');
```
## IP Whitelist
To allow specific IP addresses, add in array() <strong>line 1222</strong>. Like this:<br>
```php
$IPs = array(
'127.0.0.1',
'1.1.1.1'
);
```
Added IP addresses will not be checked. All requests will be approved.
