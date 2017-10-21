<?php
// settings
// host, user and password settings

$host      = "localhost";
$user      = "logger";
$password  = "<your db password here>";
$database  = "sensors";
$sensortbl = "sensordata";
$sensor1   = "TempHumid";
$sensor2   = "AirQuality";  
$sensor3   = "DustSensor";   
$type1     = "Temp";
$type2     = "Humidity";
$type3     = "AQi";
$type4     = "CO2 ppm";
$type5     = "PMi";
$type6     = "PMD mg/m3";
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
    padding: 2px 5px 2px 5px;
    border: 1px solid #ccc;
    border-top: none;
}

/* Sensor data table display responsive table */
h1{
  font-size: 30px;
  color: #fff;
  text-transform: uppercase;
  font-weight: 300;
  text-align: center;
  margin-bottom: 15px;
}
table{
  width:100%;
  table-layout: fixed;
}
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:600px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
th{
  padding: 20px 15px;
  text-align: center;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
td{
  padding: 15px;
  text-align: center;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #fff;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}

@import url(https://fonts.googleapis.com/css?family=Roboto:400,500,300,700);
body{
  background: -webkit-linear-gradient(left, #25c481, #25b7c4);
  background: linear-gradient(to right, #25c481, #25b7c4);
  font-family: 'Roboto', sans-serif;
}
section{
  margin: 50px;
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
  <button class="tablinks" onclick="openTab(event, 'DustSensor')">Dust Sensor</button>  
</div>

<!-- Display the raw sensor data from all sensors as logged into the mysql table -->
<div id="SensorData" class="tabcontent">

<section>
    <h3 align="center">Sensor Data logged in last <?php echo $hours ?> hrs</h3>
  <div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
      <thead>
			<tr>
			<th>#</th>
			<th>Date Time</th>
			<th>Sensor</th>
			<th>Temperature &#8451</th>
			<th>Humidity %</th>
			<th>Air Qual Index</th>
			<th>CO2 ppm</th>			
			<th>PM index</th>	
			<th>Density mg/m3</th>						
			<tr>
      </thead>
    </table>
  </div>
  <div class="tbl-content">
    <table cellpadding="0" cellspacing="0" border="0">
      <tbody>
			<?php
		        // loop all the results that were read from database and "draw" to web page
		        while($sd=mysql_fetch_assoc($sdata_all)){
		                echo "<tr>";
		                echo "<td>".$sd['id']."</td>";
		                echo "<td>".$sd['dateandtime']."</td>";
		                echo "<td>".$sd['sensor']."</td>";
		                echo "<td>".$sd['temperature']."</td>";
		                echo "<td>".$sd['humidity']."</td>";
		                echo "<td>".$sd['airquality']."</td>";
		                echo "<td>".$sd['co2ppm']."</td>";	
		                echo "<td>".$sd['pmindex']."</td>";		
		                echo "<td>".$sd['pmdensity']."</td>";				                		                	                		                
		                echo "<tr>";
		        }
			?>
 			</tbody>
    </table>
  </div>
</section>
</div>		
<!-- End of raw data display tab -->


<!-- Plot a curve of temperature and humidity over date/time  -->
<div id="TemperatureHumdity" class="tabcontent">
  <h3 align="center">Temperature & Humidity monitoring </h3>
  <p align="center">( over last <?php echo $hours ?> hrs ) </p> 
  <div id="curve_chart_tm"></div>   
</div>

<!-- Plot a curve of air quality index and gases (Co2) over date/time  -->
<div id="AirQuality" class="tabcontent">
  <h3 align="center">Air Quality monitoring</h3>
  <p align="center">( over last <?php echo $hours ?> hrs ) </p>
  <div id="curve_chart_aqi"></div>     
</div>

<!-- Plot a curve of Dust sensor index and dust density over date/time  -->
<div id="DustSensor" class="tabcontent">
  <h3 align="center">Particulate matter index and density monitoring </h3>
  <p align="center">( over last <?php echo $hours ?> hrs ) </p>
  <div id="curve_chart_pm"></div>     
</div>


<!-- all javascript code here below  -->

<!-- JS function to display the Temperature- Humidity line chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartTM);
google.charts.setOnLoadCallback(drawChartAQI);
google.charts.setOnLoadCallback(drawChartPM);

function drawChartTM() {
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
		width: 1200,
		height: 600,
		curveType: 'function',
		legend: { position: 'bottom' },
		crosshair: { trigger: 'none' },
		series: {
            0 : {
            	  targetAxisIndex: 0,
                color : '#FF0000',
                visibleInLegend : true
            },
            1 : {
            	  targetAxisIndex: 1,
                color : '#000080',
                visibleInLegend : true
            }	
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
var chart = new google.visualization.LineChart(document.getElementById('curve_chart_tm'));
chart.draw(data, options);
}

function drawChartAQI() {
var data = new google.visualization.DataTable();
data.addColumn('datetime', 'Datetime');
data.addColumn('number', '<?php  echo $type3;  ?>');
data.addColumn('number', '<?php  echo $type4;  ?>');
data.addRows([
<?php
$sensor1   = "AirQuality";
$sql_one = "SELECT YEAR
	( t1.dateandtime ) AS year,
	MONTH ( t1.dateandtime ) AS month,
	DAY ( t1.dateandtime ) AS day,
	HOUR ( t1.dateandtime ) AS hour,
	MINUTE ( t1.dateandtime ) AS minute,
	t1.sensor AS sensor2,
	t1.airquality AS aqi,
	t1.co2ppm AS co2 
FROM
	 sensordata AS t1
WHERE
	t1.sensor = '" . $sensor2 . "'
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
    echo $row['aqi'] . ", ";
    echo $row['co2'] . ", ";
    echo "],\n";
}
?>
]);
var options = {
		title: 'Air Quality Index Monitor (AQI) - <?php echo $today; ?>',
		width: 1200,
		height: 600,
		curveType: 'function',
		legend: { position: 'bottom' },
		crosshair: { trigger: 'none' },
		series: {
            0 : {
            	  targetAxisIndex: 0,
                color : '#FF2D96',
                visibleInLegend : true
            },
            1 : {
            	  targetAxisIndex: 1,
                color : '#00CC33',
                visibleInLegend : true
            }	
				},
		vAxes: {
					0: {
					title: 'AQI',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 1023,
					min: 0
					}
			},
					1: {
					title: 'CO2 ppm',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 1023,
					min: 0
					}
				}
			},
			hAxis: { title: 'Time of Day' }
};
var chart = new google.visualization.LineChart(document.getElementById('curve_chart_aqi'));
chart.draw(data, options);
}



function drawChartPM() {
var data = new google.visualization.DataTable();
data.addColumn('datetime', 'Datetime');
data.addColumn('number', '<?php  echo $type5;  ?>');
data.addColumn('number', '<?php  echo $type6;  ?>');
data.addRows([
<?php
$sensor3   = "DustSensor";
$sql_one = "SELECT YEAR
	( t1.dateandtime ) AS year,
	MONTH ( t1.dateandtime ) AS month,
	DAY ( t1.dateandtime ) AS day,
	HOUR ( t1.dateandtime ) AS hour,
	MINUTE ( t1.dateandtime ) AS minute,
	t1.sensor AS sensor3,
	t1.pmindex AS pmi,
	t1.pmdensity AS pmd 
FROM
	 sensordata AS t1
WHERE
	t1.sensor = '" . $sensor3 . "'
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
    echo $row['pmi'] . ", ";
    echo $row['pmd'] . ", ";
    echo "],\n";
}
?>
]);
var options = {
		title: 'Dust, Haze and Particulate matter monitoring - <?php echo $today; ?>',
		width: 1200,
		height: 600,
		curveType: 'function',
		legend: { position: 'bottom' },
		crosshair: { trigger: 'none' },
		series: {
            0 : {
            	  targetAxisIndex: 0,
                color : '#FF2D96',
                visibleInLegend : true
            },
            1 : {
            	  targetAxisIndex: 1,
                color : '#00CC33',
                visibleInLegend : true
            }	
				},
		vAxes: {
					0: {
					title: 'PMI',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 1023,
					min: 0
					}
			},
					1: {
					title: 'PMD mg/m3',
					viewWindowMode: 'explicit',
					viewWindow: {
					max: 0.8,
					min: 0
					}
				}
			},
			hAxis: { title: 'Time of Day' }
};
var chart = new google.visualization.LineChart(document.getElementById('curve_chart_pm'));
chart.draw(data, options);
}

</script>

<!-- Javascript function to change tab based on user clicks  -->
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

<script>
	<!-- Javascript for the responsive table data with fixed header display -->
// '.tbl-content' consumed little space for vertical scrollbar, scrollbar width depend on browser/os/platfrom. Here calculate the scollbar width .
$(window).on("load resize ", function() {
  var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
  $('.tbl-header').css({'padding-right':scrollWidth});
}).resize();
</script>


     
</body>
</html> 
