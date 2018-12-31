<?php
 /*
 *  vChallenge 1.0 - PHP Bot Checker Open-Source
 *  ********************************
 *  Test File, Checking client.
 *  ********************************
 */

require_once(__DIR__ . '/class.php');
$vCHALL = new vChallenge\vCHALL();
$vCHALL->enable('5s'); // Mode: 5s (checking in 5 seconds) or reCAPTCHA (Google reCAPTCHA validation).

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
	