from behave import *
import bamding_web_test
from selenium.common.exceptions import NoSuchElementException
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