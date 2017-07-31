# simple py code to read DHT22 temperature and humidity sensor
# Author: Thomas & Janice
# adapted from varous online sources.see README.md for references.
# License: Public Domain
#!/usr/bin/python2
#coding=utf-8

from utilities import *
import os
import sys
import datetime
from datetime import timedelta



def main():

	currentTime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

	configurations = getConfigurations()

	# how many sensors there is 1 or 2
	sensorsToRead = configurations["sensoramount"]
		
	# Sensor names to add to database, e.g. carage, outside
	sensor1 = configurations["sensors"][0]["sensor1"]
	# sensor2 = configurations["sensors"][0]["sensor2"]

	# temperature limits for triggering alarms
	sensor1lowlimit = configurations["dht22_temptriggers"][0]["sensor1_temp_low_limit"]
	# sensor2lowlimit = configurations["dht22_temptriggers"][0]["sensor1_temp_low_limit"]
	sensor1highlimit = configurations["dht22_temptriggers"][0]["sensor1_temp_high_limit"]
	# sensor2highlimit = configurations["dht22_temptriggers"][0]["sensor1_temp_high_limit"]

	# humidity limits for triggering alarms
	sensor1_humidity_low_limit = configurations["dht22_humiditytriggers"][0]["sensor1_humidity_low_limit"]
	sensor1_humidity_high_limit = configurations["dht22_humiditytriggers"][0]["sensor1_humidity_high_limit"]
	# sensor2_humidity_low_limit =  configurations["dht22_humiditytriggers"][0]["sensor2_humidity_low_limit"]
	# sensor2_humidity_high_limit = configurations["dht22_humiditytriggers"][0]["sensor2_humidity_high_limit"]

	# Sensor gpios
	gpioForSensor1 = configurations["sensorgpios"][0]["gpiosensor1"]
	# gpioForSensor2 = configurations["sensorgpios"][0]["gpiosensor2"]
	
	# Backup enabled
	backupEnabled = configurations["sqlBackupDump"][0]["backupDumpEnabled"]
	backupHour = configurations["sqlBackupDump"][0]["backupHour"]
	
	# Connection check enabled
	connectionCheckEnabled = configurations["connectionCheck"][0]["connectionCheckEnabled"]
	connectionCheckDay = configurations["connectionCheck"][0]["connectionCheckDay"]
	connectionCheckHour = configurations["connectionCheck"][0]["connectionCheckHour"]

	# type of the sensor used, e.g. DHT22 = 22
	sensorType = configurations["sensortype"]

	# Default value for message type, not configurable
	msgType = "Warning"

	d = datetime.date.weekday(datetime.datetime.now())
	h = datetime.datetime.now()

	# check if it is 5 o clock. If yes, take sql dump as backup
	if backupEnabled == "Y" or backupEnabled == "y":
		if h.hour == int(backupHour):
			databaseHelper("","Backup")

	# check if it is sunday, if yes send connection check on 23.00
	if connectionCheckEnabled == "Y" or connectionCheckEnabled == "y":
		okToUpdate = False
		if str(d) == str(connectionCheckDay) and str(h.hour) == str(connectionCheckHour):
			try:
				sensor1weeklyAverage = getWeeklyAverageTemp(sensor1)
				if sensor1weeklyAverage != None and sensor1weeklyAverage != '':
					checkSensor = sensor1+" conchck"
					okToUpdate, tempWarning = checkWarningLog(checkSensor,sensor1weeklyAverage)
					if okToUpdate == True:
						msgType = "Info"
						Message = "Connection check. Weekly average from {0} is {1}".format(sensor1,sensor1weeklyAverage)
						emailWarning(Message, msgType)
						sqlCommand = "INSERT INTO mailsendlog SET mailsendtime='%s', triggedsensor='%s', triggedlimit='%s' ,lasttemperature='%s'" % (currentTime,checkSensor,sensor1lowlimit,sensor1weeklyAverage)
						databaseHelper(sqlCommand,"Insert")
			except:
				emailWarning("Couldn't get average temperature to sensor: {0} from current week".format(sensor1),msgType)
				pass				

			if sensorsToRead != "1":
				okToUpdate = False
				try:
					sensor2weeklyAverage = getWeeklyAverageTemp(sensor2)
					if sensor2weeklyAverage != None and sensor2weeklyAverage != '':
						checkSensor = sensor2+" conchck"
						okToUpdate, tempWarning = checkWarningLog(checkSensor,sensor2weeklyAverage)
						if okToUpdate == True:
							msgType = "Info"	
							Message = "Connection check. Weekly average from {0} is {1}".format(sensor2,sensor2weeklyAverage)
							emailWarning(Message, msgType)
							sqlCommand = "INSERT INTO mailsendlog SET mailsendtime='%s', triggedsensor='%s', triggedlimit='%s' ,lasttemperature='%s'" % (currentTime,checkSensor,sensor2lowlimit,sensor2weeklyAverage)
							databaseHelper(sqlCommand,"Insert")
				except:
					emailWarning( "Couldn't get average temperature to sensor: {0} from current week".format(sensor2),msgType)
					pass			

	# default message type to send as email. DO NOT CHANGE
	msgType = "Warning"	

	sensor1error = 0
	okToUpdate = False
	# Sensor 1 readings and limit check
	try:
		sensor1temperature, sensor1humidity = sensorReadings(gpioForSensor1, sensorType)
		limitsOk,warningMessage = checkLimits(sensor1,sensor1temperature,sensor1humidity,sensor1highlimit,sensor1lowlimit,sensor1_humidity_high_limit,sensor1_humidity_low_limit)
	except:
		emailWarning("Failed to read {0} sensor".format(sensor1),msgType)
		sensor1error = 1
		pass
	
	#debug	
	#print ('After reading' ,currentTime,sensor1,sensor1temperature,sensor1humidity)
	
	if sensor1error == 0:
		try:
			# if limits were trigged
			if limitsOk == False:
				# check log when was last warning sended
				okToUpdate, tempWarning = checkWarningLog(sensor1,sensor1temperature)
		except: 
			# if limits were triggered but something caused error, send warning mail to indicate this
			emailWarning("Failed to check/insert log entry from mailsendlog. Sensor: {0}".format(sensor1),msgType)	
			sys.exit(0)

		if okToUpdate == True:
			# enough time has passed since last warning or temperature has increased/decreased by 5 degrees since last measurement
			warningMessage = warningMessage + "\n" + tempWarning
			# send warning
			emailWarning(warningMessage, msgType)
			try:
			# Insert line to database to indicate when warning was sent
				currentTime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
				sqlCommand = "INSERT INTO mailsendlog SET mailsendtime='%s', triggedsensor='%s', triggedlimit='%s' ,lasttemperature='%s'" % (currentTime,sensor1,sensor1lowlimit,sensor1temperature)
				databaseHelper(sqlCommand,"Insert")
			except:
				# if database insert failed, send warning to indicate that there is some issues with database
				emailWarning("Failed to insert from {0} to mailsendlog".format(sensor1),msgType)	
	
	# sensor 2 readings and limit check
	sensor2error = 0
	okToUpdate = False
	
	if sensorsToRead != "1":
		try:
			sensor2temperature, sensor2humidity = sensorReadings(gpioForSensor2, sensorType)
			limitsOk,warningMessage = checkLimits(sensor2,sensor2temperature,sensor2humidity,sensor2highlimit,sensor2lowlimit,sensor2_humidity_high_limit,sensor2_humidity_low_limit)
		except:
			emailWarning("Failed to read {0} sensor".format(sensor2),msgType)
			sensor2error = 1
			pass

		if sensor2error == 0:
			try:		
				if limitsOk == False:
					okToUpdate, tempWarning = checkWarningLog(sensor2,sensor2temperature)	

			except:
				emailWarning("Failed to check/insert log entry from mailsendlog. Sensor: {0}".format(sensor2),msgType)	
				sys.exit(0)

			if okToUpdate == True:
				warningMessage = warningMessage + "\n" + tempWarning
				emailWarning(warningMessage, msgType)
				try:
					# Insert line to database to indicate when warning was sent
			       		sqlCommand = "INSERT INTO mailsendlog SET mailsendtime='%s', triggedsensor='%s', triggedlimit='%s' ,lasttemperature='%s'" % (currentTime,sensor2,sensor2lowlimit,sensor2temperature)
					databaseHelper(sqlCommand,"Insert")
				except:
					emailWarning("Failed to insert entry from {0} to mailsendlog".format(sensor1),msgType)	


  # debug
	#print ('before insert', currentTime,sensor1,sensor1temperature,sensor1humidity)
	 
	# insert values to db
	try:
		if sensor1error == 0:
			sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', temperature='%s', humidity='%s'" % (currentTime,sensor1,sensor1temperature,sensor1humidity)
			# This row below sets temperature as fahrenheit instead of celsius. Comment above line and uncomment one below to take changes into use
			#sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', temperature='%s', humidity='%s'" % (currentTime,sensor1,(sensor1temperature*(9.0/5.0)+32),sensor1humidity)
			databaseHelper(sqlCommand,"Insert")
		if sensorsToRead != "1" and sensor2error == 0:
			sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', temperature='%s', humidity='%s'" % (currentTime,sensor2,sensor2temperature,sensor2humidity)		
			# This row below sets temperature as fahrenheit instead of celsius. Comment above line and uncomment one below to take changes into use
			#sqlCommand = "INSERT INTO sensordata SET dateandtime='%s', sensor='%s', temperature='%s', humidity='%s'" % (currentTime,sensor2,(sensor2temperature*(9.0/5.0)+32),sensor2humidity)
			databaseHelper(sqlCommand,"Insert")
   	except:
		sys.exit(0)

if __name__ == "__main__":
	main()
