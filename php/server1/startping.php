<?php

	$postdata = array();
	$postdata["hostname"] = gethostname();
	$postdata["note"] = "RA!";

	$ch = curl_init("http://192.168.2.89/ping.php");
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);	

	$res = curl_exec($ch);

	echo $res;
?>
