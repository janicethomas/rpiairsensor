<?php
// settings
// host, user and password settings
$host      = "localhost";
$user      = "logger";
$password  = "logger";
$database  = "sensors";
$sensortbl = "sensordata";
$sensor1   = "TempHumid";
//$sensor2 = "AirQuality";      -- TBD
$type1     = "Temp";
$type2     = "Humidity";
//how many hours backwards do you want results to be shown in web page.
$hours     = 168;
// make connection to database
$con       = mysql_connect($host, $user, $password);
// select db
mysql_select_db($database, $con);
// sql command that selects all entires from current time and X hours backwards
//NOTE: If you want to show all entries stored in the database, comment out the line that starts with
// "AND t1.dateandtime..." by inserting a "#" at the beginning of that line
//sql_all - fetches all sensor data from all sensors
//sql_one - fetches only the specified 'sensor1' data

$sql_all ="SELECT * FROM sensordata 
      WHERE dateandtime >= (NOW() - INTERVAL '" . $hours . "' HOUR) 
      ORDER by id desc";

// set query to variable
$sdata_all = mysql_query($sql_all);

if (!$sdata_all) {
    echo "Could not fetch data from database. Err : " . mysql_error();
    exit;
}

if (mysql_num_rows($sdata_all) == 0) {
    echo "No sensor data found, nothing to chart, so am exiting";
    exit;
}


// format current time for display
$today = date("D M j, Y G:i:s T");
// create content to web-page
?>

<!DOCTYPE html>
<html>
<head>
<style>
body {font-family: "Lato", sans-serif;}
/* style the chart */
.chart {
  width: 100%; 
  min-height: 450px;
}

/* Style the tab */
div.tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
div.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of buttons on hover */
div.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
div.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 2px 5px;
    border: 1px solid #ccc;
    border-top: none;
}
</style>



</head>

<!-- Display the html tabs and data for the tab selected by user -->
<body>

<h1> Air Quality Monitoring Station </h1>

<div class="tab">
  <button class="tablinks" onclick="openTab(event, 'SensorData')" id="defaultOpen">Sensor Data</button>
  <button class="tablinks" onclick="openTab(event, 'TemperatureHumdity')">Temperature & Humidity</button>
  <button class="tablinks" onclick="openTab(event, 'AirQuality')">Air Quality</button>
</div>

<!-- Display the row sensor data from all sensors as logged into the mysql table -->
<div id="SensorData" class="tabcontent">
  <h3 align="center">Sensor Data logged in last <?php echo $hours ?> hrs </h3>
  <p></p>
  <table width=600 border="1" cellpadding="1" cellspacing="1" align="center">
		<tr>
		<th>#</th>
		<th>Date Time</th>
		<th>Sensor</th>
		<th>Temperature &#8451</th>
		<th>Humidity %</th>
		<tr>
		<?php
		        // loop all the results that were read from database and "draw" to web page
		        while($sd=mysql_fetch_assoc($sdata_all)){
		                echo "<tr>";
		                echo "<td align='center'>".$sd['id']."</td>";
		                echo "<td align='center'>".$sd['dateandtime']."</td>";
		                echo "<td align='center'>".$sd['sensor']."</td>";
		                echo "<td align='center'>".$sd['temperature']."</td>";
		                echo "<td align='center'>".$sd['humidity']."</td>";
		                echo "<tr>";
		        }
		?>
		</table>
    
</div>
<!-- End of raw data display tab -->


<!-- Plot a curve of temperature and humidity over date/time  -->
<div id="TemperatureHumdity" class="tabcontent">
  <h3 align="center">Temperature & Humidity monitoring </h3>
  <p align="center">( over last <?php echo $hours ?> hrs ) </p> 
  <div id="curve_chart" style="width: 1200px; height: 500px"></div>
   
</div>

<div id="AirQuality" class="tabcontent">
  <h3 align="center">Air Quality monitoring</h3>
  <p align="center">( over last <?php echo $hours ?> hrs ) - TODO </p>
</div>

<!-- all javascript here on the bottom -->
<script type="text/javascript" src="js/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);
function drawChart() {
var data = new google.visualization.DataTable();
data.addColumn('datetime', 'Datetime');
data.addColumn('number', '<?php  echo $type1;  ?>');
data.addColumn('number', '<?php  echo $type2;  ?>');
data.addRows([
<?php
$sensor1   = "TempHumid";
$sql_one = "SELECT YEAR
	( t1.dateandtime ) AS year,
	MONTH ( t1.dateandtime ) AS month,
	DAY ( t1.dateandtime ) AS day,
	HOUR ( t1.dateandtime ) AS hour,
	MINUTE ( t1.dateandtime ) AS minute,
	t1.sensor AS sensor1,
	t1.temperature AS temp1,
	t1.humidity AS hum1 
FROM
	 sensordata AS t1
WHERE
	t1.sensor = '" . $sensor1 . "'
  AND t1.dateandtime >= ( NOW() - INTERVAL '" . $hours . "' HOUR ) 
ORDER BY
	t1.dateandtime";	

$sdata_one = mysql_query($sql_one);
// loop all the results that were read from database and "draw" to web page
while ($row = mysql_fetch_assoc($sdata_one)) {
    echo " [ ";
    echo "new Date(" . $row['year'] . ", ";
    echo ($row['month'] - 1) . ", "; // adjust month from mysql to javascript format
    echo $row['day'] . ", ";
    echo $row['hour'] . ", ";
    echo $row['minute'] . "), ";
    echo $row['temp1'] . ", ";
    echo $row['hum1'] . ", ";
    echo "],\n";
}
?>
]);
var options = {
		title: 'Air Quality Monitor - <?php echo $today; ?>',
		width: 1000,
		height: 600,
		curveType: 'function',
		legend: { position: 'bottom' },
		crosshair: { trigger: 'none' },
		series: {
				0: { targetAxisIndex: 0 },
				1: { targetAxisIndex: 1 }
				},
		vAxes: {
					0: {
					title: 'Centigrade',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 60,
					min: 0
					}
			},
					1: {
					title: '%Humidity',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 100,
					min: 0
					}
				}
			},
			hAxis: { title: 'Time of Day' }
};
var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
chart.draw(data, options);
}
</script>
<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
     
</body>
</html> 