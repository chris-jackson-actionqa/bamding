from behave import *
from selenium.webdriver.common.by import By
from selenium.common.exceptions import NoSuchElementException

use_step_matcher("re")

@step("I have no templates")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    pass


@step("I go to the booking templates page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.get('http://localhost/wordpress/booking-templates/')


@then("I get a message to enter booking details")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.find_element(By.ID, 'templates_error')


@step("I cannot add a new template")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    try:
        context.driver.find_element(By.ID, "booking_template_add_button")
        is_present = True
    except NoSuchElementException:
        is_present = False

    assert not is_present