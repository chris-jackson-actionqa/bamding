from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.common.by import By
import time

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

        wait = WebDriverWait(self.driver, 10)
        wait.until(EC.visibility_of_element_located((By.ID, 'user_login')))
        time.sleep(1)

        user_login = self.driver.find_element_by_id('user_login')
        user_login.send_keys('test_user')
        user_pass = self.driver.find_element_by_id('user_pass')
        user_pass.send_keys('a1234567')
        submit = self.driver.find_element_by_id('wp-submit')
        submit.click()
        pass

    def go_home(self):
        self.driver.get("http://localhost/wordpress")

    def go_to_dates(self):
        dates_url = "http://localhost/wordpress/dates"
        if dates_url != self.driver.current_url:
            self.driver.get(dates_url)
        assert dates_url == self.driver.current_url