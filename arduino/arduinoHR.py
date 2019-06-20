from selenium import webdriver
from time import sleep
import serial

from selenium import webdriver

driver = webdriver.Chrome("C:/Github/open-collector/arduino/chromedriver") #Ant's laptop location
driver.get('https://www.open-collector.org/kitten/PsychoPhys.html');

ser = serial.Serial("COM3",9600)

while True:
  sleep(.001)
  cc = str(ser.readline())  
  heart_val = cc[2:][:-5]  
  try:
    driver.execute_script("all_data.clean_heart_val('" +  str(int(cc))  + "')") #Aimie's line
  except:
    print("it would have crashed here")
