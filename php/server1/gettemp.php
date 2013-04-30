<?php
	require_once('/var/www/cass/lib/datastore.php');
	$ds = new XDatastore();

    $data = "[[";
    
	$end = 1367251713;
	$start = $end - 86400;
 
	$res = $ds->getPingRange(1,$start,$end);

	//var_dump($res);

	foreach($res as $point)
    {
        $data = $data.$point['temp'].",";
    }
	
	$data = substr($data,0,strlen($data)-1)."]]";
	echo $data;



?>
