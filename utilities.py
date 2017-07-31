# utilities.py -- contains all common classes and functions used by AirSensor for ALL sensors
# Author: Thomas & Janice
# adapted from varous online sources.see README.md for references.
# License: Public Domain
# TODO : GP2Y10 Sharp infrared sensor cannot determine particle size and needs A2D
#        calculations and trend fitting to determine approx density. Consider Nova laser
#        sensor and directly read digital PM2.5 and PM10 readings..
#!/usr/bin/python2
#coding=utf-8

import os
import sys
import re
import datetime
import json
import time
import RPi.GPIO as GPIO
import spidev
import wiringpi
import struct
import serial
import MySQLdb
import subprocess
import smtplib
from email.MIMEMultipart import MIMEMultipart
from email.MIMEText import MIMEText
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), '.'))

def json_serial(obj):
    """JSON serializer for objects not serializable by default json code"""
    if isinstance(obj, datetime):
        serial = obj.isoformat()
        return serial
    raise TypeError ("Type not serializable")


def getConfigurations():

	path = os.path.dirname(os.path.realpath(sys.argv[0]))

	#get configs
	#configurationFile = '/home/pi/AirSensor/config.json'	
	configurationFile = path + '/config.json'
	configurations = json.loads(open(configurationFile).read())

	return configurations


# helper function for database actions. Handles select, insert and sqldumpings. Update te be added later
def databaseHelper(sqlCommand,sqloperation):

	configurations = getConfigurations()

	host = configurations["mysql"][0]["host"]
	user = configurations["mysql"][0]["user"]
	password = configurations["mysql"][0]["password"]
	database = configurations["mysql"][0]["database"]
	backuppath = configurations["sqlbackuppath"]
	
	data = ""
	
	db = MySQLdb.connect(host,user,password,database)
        cursor=db.cursor()

	if sqloperation == "Select":
		try:
			cursor.execute(sqlCommand)
			data = cursor.fetchone()
  		except:
			db.rollback()
	elif sqloperation == "Insert":
        	try:
			cursor.execute(sqlCommand)
                	db.commit()
        	except:
                	db.rollback()
                	emailWarning("Database insert failed", "")
			sys.exit(0)
    
	return data

# function for reading DHT22 sensors
def sensorReadings(gpio, sensor):
	
	configurations = getConfigurations()	
	adafruit = configurations["adafruitpath"]

	sensorReadings = subprocess.check_output(['sudo',adafruit,sensor,gpio])

	try:
		# try to read neagtive numbers
		temperature = re.findall(r"Temp=(-\d+.\d+)", sensorReadings)[0]
	except: 
		# if negative numbers caused exception, they are supposed to be positive
		try:
			temperature = re.findall(r"Temp=(\d+.\d+)", sensorReadings)[0]
		except:
			pass
	humidity = re.findall(r"Humidity=(\d+.\d+)", sensorReadings)[0]
	intTemp = float(temperature)
	intHumidity = float(humidity)

	return intTemp, intHumidity

# function for getting weekly average temperatures.
def getWeeklyAverageTemp(sensor):

	weekAverageTemp = ""	
	
	date = 	datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
	delta = (datetime.date.today() - timedelta(days=7)).strftime("%Y-%m-%d 00:00:00")

    	try:
        	sqlCommand = "SELECT AVG(temperature) FROM sensordata WHERE dateandtime BETWEEN '%s' AND '%s' AND sensor='%s'" % (delta,date,sensor)
		data = databaseHelper(sqlCommand,"Select")
		weekAverageTemp = "%.2f" % data
   	except:
		pass
	
	return weekAverageTemp

# function that sends emails, either warning or weekly averages in order to see that pi is alive
def emailWarning(msg, msgType):
	
	configurations = getConfigurations()
	
	fromaddr = configurations["mailinfo"][0]["senderaddress"]
	toaddrs = configurations["mailinfo"][0]["receiveraddress"]
	username = configurations["mailinfo"][0]["username"]
	password = configurations["mailinfo"][0]["password"]
	subj = configurations["mailinfo"][0]["subjectwarning"]
		
	if msgType is 'Info':
		subj = configurations["mailinfo"][0]["subjectmessage"]
	
	# Message to be sended with subject field
	message = 'Subject: %s\n\n%s' % (subj,msg)

	# The actual mail sending
	server = smtplib.SMTP('smtp.gmail.com',587)
	server.starttls()
	server.login(username,password)
	server.sendmail(fromaddr, toaddrs, message)
	server.quit()

	return
	
# helper function for database actions. Handles select, insert and sqldumpings. Update te be added later
def databaseHelper(sqlCommand,sqloperation):

	configurations = getConfigurations()

	host = configurations["mysql"][0]["host"]
	user = configurations["mysql"][0]["user"]
	password = configurations["mysql"][0]["password"]
	database = configurations["mysql"][0]["database"]
	backuppath = configurations["sqlbackuppath"]
	
	data = ""
	
	db = MySQLdb.connect(host,user,password,database)
        cursor=db.cursor()

	if sqloperation == "Select":
		try:
			cursor.execute(sqlCommand)
			data = cursor.fetchone()
  		except:
			db.rollback()
	elif sqloperation == "Insert":
        	try:
			cursor.execute(sqlCommand)
                	db.commit()
        	except:
                	db.rollback()
                	emailWarning("Database insert failed", "")
			sys.exit(0)
    
	elif sqloperation == "Backup":	
		# Getting current datetime to create seprate backup folder like "12012013-071334".
		date = datetime.date.today().strftime("%Y-%m-%d")
		backupbathoftheday = backuppath + date

		# Checking if backup folder already exists or not. If not exists will create it.
		if not os.path.exists(backupbathoftheday):
			os.makedirs(backupbathoftheday)

		# Dump database
		db = database
		dumpcmd = "mysqldump -u " + user + " -p" + password + " " + db + " > " + backupbathoftheday + "/" + db + ".sql"
		os.system(dumpcmd)

	return data
	
# function for checking log that when last warning was sended, also inserts new entry to log if warning is sent
def checkWarningLog(sensor, sensortemp):

	currentTime = datetime.datetime.now()
	currentTimeAsString = datetime.datetime.strftime(currentTime,"%Y-%m-%d %H:%M:%S")
	lastLoggedTime = ""
	lastSensor = ""
	triggedLimit = ""
	lastTemperature = ""
	warning = ""
	okToUpdate = False
	# sql command for selecting last send time for sensor that trigged the warning

	sqlCommand = "select * from mailsendlog where triggedsensor='%s' and mailsendtime IN (SELECT max(mailsendtime)FROM mailsendlog where triggedsensor='%s')" % (sensor,sensor)
	data = databaseHelper(sqlCommand,"Select")

	# If there weren't any entries in database, then it is assumed that this is fresh database and first entry is needed
	if data == None:
	       	sqlCommand = "INSERT INTO mailsendlog SET mailsendtime='%s', triggedsensor='%s', triggedlimit='%s' ,lasttemperature='%s'" % (currentTimeAsString,sensor,"0.0",sensortemp)
		databaseHelper(sqlCommand,"Insert")
		lastLoggedTime = currentTimeAsString
		lastTemperature = sensortemp
		okToUpdate = True
	else:
		lastLoggedTime = data[0]
		lastSensor = data[1]
		triggedLimit = data[2]
		lastTemperature = data[3]

	# check that has couple of hours passed from the time that last warning was sended.
	# this check is done so you don't get warning everytime that sensor is trigged. E.g. sensor is checked every 5 minutes, temperature is lower than trigger -> you get warning every 5 minutes and mail is flooded.
	try:
		delta = (currentTime - lastLoggedTime).total_seconds()
		passedTime = delta // 3600

		if passedTime > 2:
			okToUpdate = True
		else:
			pass
	except:
		pass

	# another check. If enough time were not passed, but if temperature has for some reason increased or dropped 5 degrees since last alarm, something might be wrong and warning mail is needed
	# TODO: Add humidity increase / decrease check as well...requires change to database as well.
	if okToUpdate == False:
		if "conchck" not in sensor:
			if sensortemp > float(lastTemperature) + 5.0:
				okToUpdate = True
				warning = "NOTE: Temperature increased 5 degrees"
			if sensortemp < float(lastTemperature) - 5.0:
				okToUpdate = True
				warning = "NOTE: Temperature decreased 5 degrees"
			
	return okToUpdate, warning

	# Function for checking limits. If temperature is lower or greater than limit -> do something
def checkLimits(sensor,sensorTemperature,sensorHumidity,sensorhighlimit,sensorlowlimit,humidityHighLimit,humidityLowLimit):
	
	check = True
	warningmsg = ""

	# check temperature measurements against limits
	if float(sensorTemperature) < float(sensorlowlimit):
		warningmsg = "Temperature low on sensor: {0}\nTemperature: {1}\nTemperature limit: {2}\nHumidity: {3}".format(sensor,sensorTemperature,sensorlowlimit,sensorHumidity)
		check = False
	elif float(sensorTemperature) > float(sensorhighlimit):
		warningmsg = "Temperature high on sensor: {0}\nTemperature: {1}\nTemperature limit: {2}\nHumidity: {3}".format(sensor,sensorTemperature,sensorhighlimit,sensorHumidity)
		check = False

	# check humidity measurements against limits
	elif float(sensorHumidity) < float(humidityLowLimit):
		warningmsg = "Humidity low on sensor: {0}\nTemperature: {1}\nHumidity limit: {2}\nHumidity: {3}".format(sensor,sensorTemperature,humidityLowLimit,sensorHumidity)
		check = False
        elif float(sensorHumidity) > float(humidityHighLimit):
       	        warningmsg = "Humidity high on sensor: {0}\nTemperature: {1}\nHumidity limit: {2}\nHumidity: {3}".format(sensor,sensorTemperature,humidityHighLimit,sensorHumidity)
                check = False

	return check,warningmsg
	

class GPIOHelper:
    def __init__(self):
        configurations = getConfigurations()
        #self.mq135Pin = 0
        self.pm10Pin =  int (configurations["sensorgpios"][0]["gpiopm10pin"] )
        self.ILEDPin =  int (configurations["sensorgpios"][0]["gpioiledpin"] )
        self.samplingTime = 280
        self.deltaTime = 40
        self.sleepTime = 9680
        self.spi = spidev.SpiDev()
        self.spi.open(0,0)
        # Uncomment this when you need to read from the Nova PM25 sensor.
        # self.serial = serial.Serial(port='/dev/serial0')
        
        # Initialize wiringpi
        wiringpi.wiringPiSetupGpio() 
        wiringpi.pinMode(self.ILEDPin, 1)
        wiringpi.digitalWrite(self.ILEDPin, 0) # turn the LED off

# read SPI data from MCP3008 chip, 8 possible adc's (0 thru 7)
    def readadc(self, adcnum):
        if ((adcnum > 7) or (adcnum < 0)):
            return -1
        r = self.spi.xfer2([1,(8+adcnum)<<4,0])
        adcout = ((r[1]&3) << 8) + r[2]
        return adcout

    def readNovaPM25Sensor(self):
        data = self.serial.read(10)
        # Parse the data and convert it to the unit of ug/m^3
        pm25 = (data[3] * 256 + data[2]) / 10
        pm10 = (data[5] * 256 + data[4]) / 10
        return { 'pm25': pm25, 'pm10': pm10 }
     
    def readSharpPM10Sensor(self):
        voMeasured = 0
        sum_voMeasured = 0
        avg_voMeasured = 0
        testcycle = 10
        for i in range(testcycle):
            wiringpi.digitalWrite(self.ILEDPin, 1) # power on the LED
            wiringpi.delayMicroseconds(self.samplingTime)
            wiringpi.delayMicroseconds(self.deltaTime)
            voMeasured = self.readadc(self.pm10Pin) # read the dust value
            wiringpi.digitalWrite(self.ILEDPin, 0) # turn the LED off
            wiringpi.delayMicroseconds(self.sleepTime)

            # 0 - 5V mapped to 0 - 1023 integer values
            # recover voltage
            # calcVoltage = voMeasured * (5.0 / 1024)
            
            # sum the VoMeasured for averaging
            sum_voMeasured += voMeasured
            
        avg_voMeasured = int ( sum_voMeasured / testcycle )  
        
        # linear eqaution taken from http://www.howmuchsnow.com/arduino/airquality/
        # Chris Nafis (c) 2012  
        avg_calcVoltage =  avg_voMeasured * (5.0 / 1024)
        if avg_calcVoltage < 0.583:
           dustDensity = 0
        else:
            dustDensity = 0.172 * avg_calcVoltage - 0.0999	
        #print("{0}, {1}, {2}".format( avg_voMeasured, avg_calcVoltage, dustDensity))
        return avg_voMeasured, dustDensity
     
    def readSensors(self):
        #mq135 = self.readadc(self.mq135Pin)
        #mq138 = self.readadc(self.mq138Pin)
        pm10 = self.readSharpPM10Sensor()
        return { 'pm10': pm10 }

if __name__ == '__main__':
    helper = GPIOHelper()
    while True:
        wiringpi.delay(1000)
        print(helper.readSharpPM10Sensor())
