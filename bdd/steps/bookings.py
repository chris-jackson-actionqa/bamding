from behave import *
import bamding_web_test
from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.support.ui import Select
import time

use_step_matcher("re")

@given("I have 2 active venues")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.bamding.remove_all_venues()

    # add two venues
    venue_one = {'name': 'Funhouse', 'email': 'fun@house.com',
                 'country': 'United States', 'state': 'WA', 'city': 'Seattle'}
    context.bamding.add_venue(venue_one)

    venue_two = {'name': "Bob's Burgers", 'email': 'bob@burgers.com',
                 'country': 'United States', 'state': 'ME', 'city': "Seymour's Bay"}
    context.bamding.add_venue(venue_two)

    # set those venues to start booking
    context.bamding.go_to_bookings()


@given("I click the table header checkbox")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.find_element_by_id("bookings_header_checkbox").click()
    pass


@then("all the checkboxes in the table should be checked")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    for box in checkboxes:
        assert box.is_selected()
    pass


@step("see that all the checkboxes are selected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    for box in checkboxes:
        assert box.is_selected()
    pass


@then("I click the table header checkbox")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.find_element_by_id("bookings_header_checkbox").click()
    pass


@step("see that none of the checkboxes are selected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    for box in checkboxes:
        assert not box.is_selected()
    pass


@when("I click a row's checkbox to deselect it")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    checkboxes[1].click()
    pass


@then("the table header checkbox should be un-selected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    assert not checkboxes[0].is_selected()
    pass


@given("I (?P<check_venues>.+) check all venues")
def step_impl(context, check_venues):
    """
    :type context behave.runner.Context
    :type check_venues str
    """
    if "do" == check_venues:
        checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
        checkboxes[0].click()
    pass


@when("I select (?P<bulk_action>.+)")
def step_impl(context, bulk_action):
    """
    :type context behave.runner.Context
    :type bulk_action str
    """
    bulk_select = Select(context.driver.find_element_by_id('bd_bookings_bulk_action_top'))

    options = {'pause': "Pause Booking", 'start': "Start Booking", 'bulk': "Bulk Action"}
    bulk_select.select_by_visible_text(options[bulk_action])
    pass


@then("the Apply button should be (?P<apply_enabled>.+)")
def step_impl(context, apply_enabled):
    """
    :type context behave.runner.Context
    :type apply_enabled str
    """
    apply = context.driver.find_element_by_id('btn_bookings_apply_top')
    state = apply.get_attribute('disabled')
    if apply_enabled == 'enabled':
        assert not state
    else:
        assert state

@given("I select all venues")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    checkboxes[0].click()

@step("I select the bulk action Pause")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    bulk_select = Select(context.driver.find_element_by_id('bd_bookings_bulk_action_top'))
    bulk_select.select_by_visible_text("Pause Booking")

@step("I select Apply")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.find_element_by_id('btn_bookings_apply_top').click()

@step("I select Start for all venues")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    bulk_select = Select(context.driver.find_element_by_id('bd_bookings_bulk_action_top'))
    bulk_select.select_by_visible_text("Start Booking")

@then("All venues will show as paused")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    rows = context.driver.find_elements_by_xpath("//table//tr")
    # first row is header. Don't need it
    rows.pop[0]

    for row in rows:
        cells = row.find_elements_by_tag_name('td')
        assert cells[1].text == "Paused"


@then("All venues will show as active")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    rows = context.driver.find_elements_by_xpath("//table//tr")
    for row in rows:
        cells = row.text
        if "Status" in cells:
            continue

        assert "Active" in cells
    pass