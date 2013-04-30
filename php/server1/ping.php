<?php

	require_once('lib/datastore.php');
		
	$ds = new XDataStore();

	if(isset($_POST["newid"]))
	{
		$newid = $_POST["newid"];
		$pingtime = $_POST["pingtime"];
		$note = $_POST["note"];
		$temp = $_POST["temp"];

		$note = $note . " " . $newid;
		echo "POST:".$newid;
		$ds->insertPing(1,$pingtime,$note,$temp);		
	}

?>
