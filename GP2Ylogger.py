# simple py code to read Sharp GP2Y1010 dust sensor 
# captures ADC voltage and computed dust sensor in a logging db
# Author: Thomas & Janice
# adapted from varous online sources.see README.md for references.
# License: Public Domain
#!/usr/bin/python2
#coding=utf-8

from utilities import *
import os
import time
import datetime
from datetime import timedelta


def main():

  sensorHelper = GPIOHelper()	
  configurations = getConfigurations()
  sensor3 = configurations["sensors"][0]["sensor3"]
	 # read GP2Y10 Sharp infrared sensor adc voltage (vo) and computed dust density (dd)
  vo, dd = sensorHelper.readSharpPM10Sensor()
  
  currentTime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
  
  # insert values in sensors.sensordata db table
  sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', pmindex='%s', pmdensity='%s'" % ( currentTime, sensor3, vo , "{0:.2f}".format(dd) )		
  databaseHelper(sqlCommand,"Insert")
  sys.exit(0)


if __name__ == "__main__":
  main()