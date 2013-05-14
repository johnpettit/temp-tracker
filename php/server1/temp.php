<?php

	$DB_NAME = 'ping';
	$DB_HOST = '192.168.2.89';
	$DB_USER = '';
	$DB_PASS = '';

	$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

	if (mysqli_connect_errno()) 
	{
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	$query = "SELECT * FROM `Ping` ORDER BY PingDate DESC LIMIT 24";
	$result = $mysqli->query($query) or die($mysqli->error.__LINE__);

	$data = "";
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc()) 
		{
			$data = $data.date("n-j-Y, G:i", strtotime($row['PingDate']))." -- ".$row['Temp']."<br>";	
		}
	}
	else 
	{
		$data = 'NO RESULTS';	
	}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
	<title>Temperature Data</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">	
	<!-- Bootstrap -->

	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

	<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
    <script src="http://code.jquery.com/jquery.js"></script>

</head>
<body>
	<div class="row">
		<div class="span6"><h1>Hello LAX Temp!</h1></div>
	</div>
	<div class="row">
		<div class="span6">
			<div id="chart1" style="height:300px; width:500px;"></div>
		</div>
	</div>
	<div class="row">
		<div class="span6">
		<?php echo $data; ?>
		</div>
	</div>

<script class="code" type="text/javascript">
$(document).ready(function(){
  // Our ajax data renderer which here retrieves a text file.
  // it could contact any source and pull data, however.
  // The options argument isn't used in this renderer.
  var ajaxDataRenderer = function(url, plot, options) {
    var ret = null;
    $.ajax({
      // have to use synchronous here, else the function 
      // will return before the data is fetched
      async: false,
      url: url,
      dataType:"json",
      success: function(data) {
        ret = data;
      }
    });
    return ret;
  };
 
  // The url for our json data
  var jsonurl = "gettemp.php";
 
  // passing in the url string as the jqPlot data argument is a handy
  // shortcut for our renderer.  You could also have used the
  // "dataRendererOptions" option to pass in the url.
  var plot2 = $.jqplot('chart1', jsonurl,{
    title: "LAX Temp Last 24 Hours",
	axes:{
        xaxis:{
        	label:'DateTime'
        },
        yaxis:{
        	label:'Temperature (F)'
    	}
    },
    dataRenderer: ajaxDataRenderer,
    dataRendererOptions: {
      unusedOptionalUrl: jsonurl
    }
  });
});

</script>

	<script class="include" type="text/javascript" src="dist/jquery.jqplot.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="dist/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="dist/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script type="text/javascript" src="dist/plugins/jqplot.json2.min.js"></script>
	<script type="text/javascript" src="dist/plugins/jqplot.dateAxisRenderer.min.js"></script>
</body>
</html>
<?php mysqli_close($mysqli); ?>
