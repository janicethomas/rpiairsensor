# rpiairsensor

This is a prototype air sensor which monitors 
         - Temperature
         - Humidity
         - Air gases ( index and some gas levels CO2, CO etc )
         - Dust (particulate) matter
        
        
DHT22 sensor is used for Temperature and Humidity measurements. MQ135 sensor is hooked via MCP3008 ( A2D convertor) to monitor certain pollutant gases. Currently only an index (dac value) and CO2 are computed. Similarly the  Sharp’s GP2Y1010AU0F infrared dust and haze sensor is used to approximate the PM matter in the air. These are interefaced with Raspberry Pi 2/2+/3. MQ135 and GP2Y1010AU0F are analog sensors and needs interfacing with MCP3008 and approximate algorithm using the datasheet to determine the pollutant factors. DHT22 give digal output which can be directly read. 

This is a project proposed by me and my colleagues to be submitted in our Science Exhibition at Harvest International School, Bangalore. Each sensor has a python program which inserts the current sensor value with a timestamp to the database. A crontab presently triggers each of these jobs. The prototype is constructed on breadboards and the sensor data is logged into a MYSQL database. A frontend Apache webpage displays the raw data and charts the above sensor values using Google chart.
The future scope is to link all such sensors spread across cities and other schools on to a common platform and provide an overall geographic live heatmap of the air quality around us. A front end APP and backend server program will collate all these sensor node(s) readings and present it on a map.

This project draws upon references of the  work by innumerable Raspberry and Arduino enthusiasts online. We have adapted the python libraries as well as calculation logic derived from sensor datasheets to put together this project. All the works here are licensed to public domain and anyone is free to copy and modify the codes for their own personal use. We will appreciate if anyone wishes to contribute back any enhacements or bug fixes. We will publish a complete documentation of bills of material, resources, wiring sketch and other reference materials which can be useful for other students or enthusiasts here.

Below are the useful list of online resources which inspired and provided us guidelines for this project.

General info for DHT22, MCP3008 and MQ135
----------------------------------------------------------------------------------
http://www.instructables.com/id/Raspberry-PI-and-DHT22-temperature-and-humidity-lo/
http://www.raspberrypi-spy.co.uk/2013/10/analogue-sensors-on-the-raspberry-pi-using-an-mcp3008/
https://grapeot.me/smart-air-purifier.html
http://aqicn.org/sensor/
https://learn.adafruit.com/raspberry-pi-analog-to-digital-converters/mcp3008
https://tutorials-raspberrypi.com/configure-and-read-out-the-raspberry-pi-gas-sensor-mq-x/
http://angeloloza.blogspot.in/2016/06/android-arduino-air-quality-monitor.html

Info on particulate matters :
---------------------------------------------------------------------------------
http://www.howmuchsnow.com/arduino/airquality/
https://www.pocketmagic.net/sharp-gp2y1010-dust-sensor/
http://aqicn.org/city/india/bangalore/bwssb/
https://en.wikipedia.org/wiki/Particulates
https://www.epa.gov/sites/production/files/2014-05/documents/huff-particle.pdf
http://www.airveda.com/understanding_data.html
https://www.co2.earth/


      
