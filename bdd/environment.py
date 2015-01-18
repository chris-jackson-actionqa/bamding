from selenium import webdriver
from selenium.webdriver.common.keys import Keys
import BamDingWebTest

def before_all(context):
    context.driver = webdriver.Firefox()
    context.bamding = BamDingWebTest.BamDingWebTest(context.driver)
    context.bamding.login()

def after_all(context):
    context.driver.close()
