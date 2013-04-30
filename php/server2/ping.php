<?php

    if(isset($_POST["hostname"]))
    {
        echo "HI!";
        $host = $_POST["hostname"];
        if(isset($_POST["note"]))
            $note = $_POST["note"];
        else
            $note = "NONE";
        $pingtime = time();

	    $ch = curl_init("http://w1.weather.gov/xml/current_obs/KLAX.xml");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    $res = curl_exec($ch);

		$curtemp = 0;
		$xml = new XMLReader();
		if(!$xml->XML($res))
		{
			echo "XML ERROR";
			$curtemp = "XML ERROR";
		}
		else
		{
			while($xml->read())
			{
				if($xml->name == 'temp_f' && $xml->nodeType == XMLReader::ELEMENT) // check if tag name equals something
				{
					$xml->read();
					$curtemp = $xml->value;
				}
			}
		}

		$note = "TEMP: ".$curtemp;
		$conn = mysql_connect('localhost','','') or die('NO');
		mysql_select_db('ping');
		$convertdate = date('Y-m-d H:i:s',$pingtime); 
		$res = mysql_query("INSERT INTO Ping (Host,Note,Pingdate,Temp) VALUES ('$host','$note','$convertdate',$curtemp)",$conn);

		echo mysql_error($conn);
	
		$newid = mysql_insert_id($conn);

	 	$postdata2 = array();	
    	$postdata2["newid"] = $newid;
  		$postdata2["pingtime"] = $pingtime;
		$postdata2["note"] = $note;
		$postdata2["temp"] = $curtemp;

		$ch2 = curl_init("http://server1/cass/ping.php");
		curl_setopt($ch2, CURLOPT_POST,1);
  	    curl_setopt($ch2, CURLOPT_POSTFIELDS, $postdata2);
  
 	    $res = curl_exec($ch2);			

    }
    else
    {
        echo "RA!<br><br>";
		$time = time();
		echo $time;
    }

?>
