from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException

class BamDingWebTest():
    def __init__(self, driver):
        self.driver = driver

    def logout(self):
        self.go_home()
        try:
            admin_bar = self.driver.find_element_by_id('wpadminbar')
        except NoSuchElementException:
            admin_bar = None

        if None == admin_bar:
            return

    def login(self):
        self.logout()
        self.go_home()
        self.driver.get("http://localhost/wordpress/wp-login.php")
        user_login = self.driver.find_element_by_id('user_login')
        user_login.send_keys('test_user')
        user_pass = self.driver.find_element_by_id('user_pass')
        user_pass.send_keys('a1234567')
        submit = self.driver.find_element_by_id('wp-submit')
        submit.click()
        pass




    def go_home(self):
        self.driver.get("http://localhost/wordpress")