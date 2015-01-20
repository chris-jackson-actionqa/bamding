from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as ec
from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.ui import Select
import time


class BamDingWebTest():
    """
    Test helper for the site.
    """
    def __init__(self, driver):
        """
        Construction
        :param driver: webdriver
        :return:
        """
        self.driver = driver

    def logout(self):
        """
        Log out if logged in
        :return:
        """
        self.go_home()
        try:
            admin_bar = self.driver.find_element_by_id('wpadminbar')
        except NoSuchElementException:
            admin_bar = None

        if None == admin_bar:
            return

    def login(self):
        """
        Log in to test account. If already logged in, log out and log back in as test account.
        :return:
        """
        self.logout()
        self.go_home()

        self.driver.get("http://localhost/wordpress/wp-login.php")

        wait = WebDriverWait(self.driver, 10)
        wait.until(ec.visibility_of_element_located((By.ID, 'user_login')))
        time.sleep(1)

        user_login = self.driver.find_element_by_id('user_login')
        user_login.send_keys('test_user')
        user_pass = self.driver.find_element_by_id('user_pass')
        user_pass.send_keys('a1234567')
        submit = self.driver.find_element_by_id('wp-submit')
        submit.click()

    def go_home(self):
        """
        Go to the BamDing home page
        :return:
        """
        self.driver.get("http://localhost/wordpress")

    def go_to_dates(self):
        """
        Go to the dates page
        :return:
        """
        dates_url = "http://localhost/wordpress/dates"
        if dates_url != self.driver.current_url:
            self.driver.get(dates_url)
        assert dates_url == self.driver.current_url

    def remove_all_venues(self):
        """
        Remove any existing venues for the user.
        :return:
        """
        self.go_to_my_venues()
        # throws exception if table doesn't exist. That means there's no venues. Just return
        try:
            bulk_action = Select(self.driver.find_element_by_id('bd_venues_bulk_action_top'))
        except NoSuchElementException:
            return

        # select all venues,  choose bulk action 'Remove', and hit Apply
        bulk_action.select_by_visible_text('Remove')
        self.driver.find_element_by_id('my_venues_header_checkbox').click()
        self.driver.find_element_by_id('btn_myven_apply_top').click()

        # wait for the confirmation page
        wait = WebDriverWait(self.driver, 10)
        wait.until(ec.visibility_of_element_located((By.ID, 'bd_btn_remove')))

        # hit Yes to remove them
        self.driver.find_element_by_id('bd_btn_remove').click()

        # wait for my venues page
        wait.until(ec.visibility_of_element_located((By.ID, 'bdAddMyVenueLink')))

    def go_to_my_venues(self):
        """
        Go to "My Venues" page
        :return:
        """
        url = "http://localhost/wordpress/myvenues/"
        if url != self.driver.current_url:
            self.driver.get(url)
        assert url == self.driver.current_url

    def go_to_bookings(self):
        """
        Go to the "Bookings" page
        :return:
        """
        url = "http://localhost/wordpress/bookings/"
        if url != self.driver.current_url:
            self.driver.get(url)
        assert url == self.driver.current_url

    def add_venue(self, venue_dictionary):
        """
        Add new venue.
        :param venue_dictionary: The field:value list for the new venue.
        :return:
        """
        self.go_to_my_venues()
        add_venue_button = self.driver.find_element_by_id('bdAddMyVenueLink')
        ActionChains(self.driver).move_to_element(add_venue_button).click().perform()

        # wait for add venue page
        wait = WebDriverWait(self.driver, 10)
        wait.until(ec.visibility_of_element_located((By.NAME, 'bd_venue_name')))

        # now on add venue page
        self.driver.find_element_by_name('bd_venue_name').send_keys(venue_dictionary['name'])
        self.driver.find_element_by_name('bd_venue_email').send_keys(venue_dictionary['email'])
        self.driver.find_element_by_name('bd_venue_city').send_keys(venue_dictionary['city'])
        self.driver.find_element_by_name('bd_venue_state').send_keys(venue_dictionary['state'])
        self.driver.find_element_by_name('bd_venue_country').send_keys(venue_dictionary['country'])

        submit_new_venue_button = self.driver.find_element_by_id('bd_venue_add_button')
        ActionChains(self.driver).move_to_element(submit_new_venue_button).click().perform()

        # wait for my venues to load
        wait.until(ec.visibility_of_element_located((By.ID, 'bdAddMyVenueLink')))

        # TODO: verify venue added
