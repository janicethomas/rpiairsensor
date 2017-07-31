# simple py code to read MQ135 CO2 ( in ppm ) and raw A2D reading
# Author: Thomas & Janice
# adapted from varous online sources.see README.md for references.
# License: Public Domain
#!/usr/bin/python2
#coding=utf-8

from utilities import *
import os
import time
import sys
import datetime
from datetime import timedelta

# Import SPI library (for hardware SPI) and MCP3008 library.
import Adafruit_GPIO.SPI as SPI
import Adafruit_MCP3008

def main():

  configurations = getConfigurations()
      
  # Sensor names to add to database,
  currentTime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
  sensor2 = configurations["sensors"][0]["sensor2"]
  aqi_value = 0;
  co2_value = 0;


  # Software SPI configuration: -- uncomment to use S/w SPI ( bit mashed )
  #CLK  = 18
  #MISO = 23
  #MOSI = 24
  #CS   = 25
  #mcp = Adafruit_MCP3008.MCP3008(clk=CLK, cs=CS, miso=MISO, mosi=MOSI)

  # Hardware SPI configuration:
  SPI_PORT   =  int (configurations["sensorgpios"][0]["gpiospiport"] )
  SPI_DEVICE =  int ( configurations["sensorgpios"][0]["gpiospidevice"] )
  SPI_CHANNEL = int ( configurations["sensorgpios"][0]["gpiospichannel"] )


  mcp = Adafruit_MCP3008.MCP3008(spi=SPI.SpiDev(SPI_PORT, SPI_DEVICE))
  aqi_value = mcp.read_adc(SPI_CHANNEL)

  # derive the CO2 in ppm from above raw AQI ( direct A2D value from MCP3008 )
  # TODO -- Algorithn still not correct. atmospheric CO2 should be in the range of 400-450ppm
  RO = float ( configurations["mq135_refvalues"][0]["r0"] )
  RL = float ( configurations["mq135_refvalues"][0]["rl"] )
  VOUT = float(aqi_value) * 3.3 / 1023
  RS = (( 5 * RL) - ( RL * VOUT))/ VOUT
  RATIO = 0.3611* RS / RO 
  co2_value = ( 146.15 * (2.868 - RATIO) + 10)

  # print aqi_value , "{0:.2f}".format(co2_value) 


  # insert data into mysql database
  # insert values to db
  sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', airquality='%s', co2ppm='%s'" % (currentTime,sensor2,aqi_value, "{0:.2f}".format(co2_value))    
  databaseHelper(sqlCommand,"Insert")
  sys.exit(0)

if __name__ == "__main__":
  main()

