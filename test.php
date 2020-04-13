<?php
 /*
 *  vChallenge 2.0 - PHP Bot Checker Open-Source
 *  ********************************
 *  Test File, Checking client.
 *  ********************************
 */

require_once(__DIR__ . '/vchallenge.php');
$vCHALL = new vChallenge('5s'); // 5 seconds challenge.
// reCAPTCHA : $vCHALL = new vChallenge('reCAPTCHA', 'YOUR_KEY', 'YOUR_PRIVATE_KEY');

?>
<DOCTYPE html>
<html lang="en" xmlns="//www.w3.org/1999/xhtml">
    <head>
	    <meta charset="utf-8">
	    <title>File test</title>
	</head>
	<body>
	    <h1>Verified user </h1>
		<p>You have successfully passed this challenge.</p>
	</body>
</html>
	