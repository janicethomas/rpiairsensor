<?php
// settings
// host, user and password settings
$host = "localhost";
$user = "logger";
$password = "logger";
$database = "sensors";
$sensor1 = "Outside";
// $sensor2 = "In-Room";
$type1 = "Temp";
$type2 = "Humidity";
//how many hours backwards do you want results to be shown in web page.
$hours = 168;
// make connection to database
$con = mysql_connect($host,$user,$password);
// select db
mysql_select_db($database,$con);
// sql command that selects all entires from current time and X hours backwards
//NOTE: If you want to show all entries stored in the database, comment out the line that starts with
// "AND t1.dateandtime..." by inserting a "#" at the beginning of that line

// For Dual Sensor
//$sql="SELECT year(t1.dateandtime) AS year, month(t1.dateandtime) AS month, day(t1.dateandtime) AS day,
//hour(t1.dateandtime) AS hour, minute(t1.dateandtime) AS minute,
//t1.sensor AS sensor1, t1.temperature AS temp1, t1.humidity AS hum1,
//t2.sensor AS sensor2, t2.temperature AS temp2, t2.humidity AS hum2
//FROM temperaturedata AS t1
//INNER join temperaturedata AS t2
//ON t1.dateandtime = t2.dateandtime AND t2.sensor = '" . $sensor1 . "'
//WHERE t1.sensor = '" . $sensor2 . "'
//AND t1.dateandtime >= (NOW() - INTERVAL $hours HOUR)
//ORDER BY t1.dateandtime";

// For Single sensor only

$sel = "SELECT YEAR
	( t1.dateandtime ) AS YEAR,
	MONTH ( t1.dateandtime ) AS MONTH,
	DAY ( t1.dateandtime ) AS DAY,
	HOUR ( t1.dateandtime ) AS HOUR,
	MINUTE ( t1.dateandtime ) AS MINUTE,
	t1.sensor AS sensor1,
	t1.temperature AS temp1,
	t1.humidity AS hum1 
FROM
	temperaturedata AS t1
WHERE
	t1.sensor = '" . $sensor1 . "'
	AND t1.dateandtime >= ( NOW( ) - INTERVAL $hours HOUR ) 
ORDER BY
	t1.dateandtime";
	
// set query to variable
$data = mysql_query($sql);
// format current time for display
$today = date("D M j, Y G:i:s T");
// create content to web-page
?>
<html>
  <head>
    <!-- Comment out the following line to disable auto-refresh every 15 minutes -->
    <meta http-equiv="refresh" content="900"> 
    <!-- Refresh every 15 minutes -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js">
    </script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages':['corechart']}
                        );
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('datetime', 'Datetime');
        data.addColumn('number', '<?php echo $sensor1 . " " . $type1 ?>');
        data.addColumn('number', '<?php echo $sensor1 . " " . $type2 ?>');
        data.addColumn('number', '<?php echo $sensor2 . " " . $type1 ?>');
        data.addColumn('number', '<?php echo $sensor2 . " " . $type2 ?>');
        data.addRows([
          <?php
          // loop all the results that were read from database and "draw" to web page
          while ($row=mysql_fetch_assoc($data)) {
          echo " [ ";
          echo "new Date(".$row['year'].", ";
                     echo ($row['month']-1).", ";
        // adjust month from mysql to javascript format
        echo $row['day'].", ";
        echo $row['hour'].", ";
        echo $row['minute']."), ";
        echo $row['temp1'].", ";
//        echo $row['temp2'].", ";
        echo $row['hum1'].", ";
//        echo $row['hum2']." ";
        echo "],\n";
      }
      ?>
        ]);
      var options = {
        title: 'Bakersfield Home - <?php echo $today ?>',
        width: 1000,
        height: 600,
        curveType: 'function',
        legend: {
          position: 'bottom' }
        ,
        crosshair: {
          trigger: 'both' }
        ,
        series: {
          0: {
            targetAxisIndex: 0 }
          ,
          1: {
            targetAxisIndex: 0 }
          ,
          2: {
            targetAxisIndex: 1 }
          ,
          3: {
            targetAxisIndex: 1 }
        }
        ,
        vAxes: {
          0: {
            title: '\u00B0Fahrenheit',
            viewWindowMode: 'explicit',
            viewWindow: {
              max: 135,
              min: 35
            }
          }
          ,
          1: {
            title: '%Humidity',
            viewWindowMode: 'explicit',
            viewWindow: {
              max: 100,
              min: 0
            }
          }
        }
        ,
        hAxis: {
          title: 'Time of Day' }
      };
      var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
      chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="curve_chart" style="width: 1200px; height: 500px">
    </div>
  </body>
</html>
