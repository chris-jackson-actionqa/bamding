from selenium import webdriver
from selenium.webdriver.common.keys import Keys
import bamding_web_test

def before_all(context):
    context.driver = webdriver.Firefox()
    context.bamding = bamding_web_test.BamDingWebTest(context.driver)
    context.bamding.login()

def after_all(context):
    context.driver.close()
