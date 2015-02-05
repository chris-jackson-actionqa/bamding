from selenium import webdriver
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as ec
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.common.exceptions import NoSuchElementException

import bamding_web_test


def before_all(context):
    """
    Initialize tests
    :param context:
    :return:
    """
    context.driver = webdriver.Firefox()
    context.bamding = bamding_web_test.BamDingWebTest(context.driver)
    context.driver.maximize_window()
    context.bamding.login()
    context.bamding.remove_all_venues()

    context.feature_bookings = "Bookings page for starting, pausing, and editing bookings for venues."
    context.feature_band_details = "Band Details"


def after_all(context):
    """
    Remove all the venues and  close the browser.
    :param context:
    :return:
    """
    context.bamding.remove_all_venues()
    context.driver.close()


def before_feature(context, feature):
    bookings_feature = "Bookings page for starting, pausing, and editing bookings for venues."
    if feature.name == bookings_feature:
        add_venues_for_bookings_tests(context)
    pass


def after_feature(context, feature):
    context.bamding.remove_all_venues()
    pass


def before_scenario(context, scenario):
    """
    Go to bookings page. Clear any checked venues
    :param context:
    :param scenario:
    :return:
    """
    if context.feature.name == context.feature_bookings:
        # set those venues to start booking
        context.bamding.go_to_bookings()
        checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
        for box in checkboxes:
            if box.is_selected():
                box.click()
            if box.is_selected():
                raise RuntimeError("Could not deselect checkbox.")

    elif context.feature.name == context.feature_band_details:
        context.bamding.go_to_band_details()


def after_scenario(context, scenario):
    """
    Clean up after scenarios
    :param context:
    :param scenario:
    :return:
    """
    # if on bookings page, clear out filter input field
    # Use backspace to trigger javascript changes to generate the table
    # Just using the .clear method doesn't trigger the required javascript.
    try:
        if context.driver.current_url == "http://localhost/wordpress/bookings/":
            filter_input = context.driver.find_element_by_id("filter_bookings_input")
            text_len = len(filter_input.get_attribute('value'))
            backspace_counts = range(text_len)
            for count in backspace_counts:
                filter_input.send_keys(Keys.BACK_SPACE)
            filter_input.clear()
    except NoSuchElementException:
        print("Probably no venues. Keep on moving")


def add_venues_for_bookings_tests(context):
    # add some venues
    venue_one = {'name': 'Funhouse', 'email': 'fun@house.com',
                 'country': '', 'state': 'WA', 'city': 'Seattle'}
    context.bamding.add_venue(venue_one)

    venue_two = {'name': "Bob's Burgers", 'email': 'bob@burgers.com',
                 'country': '', 'state': 'ME', 'city': "Seymour's Bay"}
    context.bamding.add_venue(venue_two)

    venue_three = {'name': "Enterprise", 'email': 'jean@picard.com',
                   'country': '', 'state': 'NY', 'city': "Alpha"}
    context.bamding.add_venue(venue_three)

    # add a category to one of the venues
    # go to venues page
    context.bamding.go_to_my_venues()

    # select first venue row
    context.driver.find_element_by_xpath("//table[@id='venues_table']/tbody/tr[2]/td[1]/input").click()

    # select category
    # hit apply
    bulk_action = Select(context.driver.find_element_by_id('bd_venues_bulk_action_top'))
    bulk_action.select_by_visible_text('Set Category')
    context.driver.find_element_by_id('btn_myven_apply_top').click()

    # type in category
    wait = WebDriverWait(context.driver, 10)
    wait.until(ec.visibility_of_element_located((By.ID, 'select_category')))
    category_select = Select(context.driver.find_element_by_id("select_category"))
    category_select.select_by_visible_text("Add New Category")
    context.driver.find_element_by_id("txt_new_category").send_keys("dive bar")
    context.driver.find_element_by_id("btn_add_category").click()

    # apply
    context.driver.find_element_by_id("btn_category_submit").click()

    # verify
    pass
