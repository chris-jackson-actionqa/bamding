from behave import *
from selenium.webdriver.common.by import By
from selenium.common.exceptions import NoSuchElementException
import os
import subprocess
use_step_matcher("re")

@step("I have no templates")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    script = 'c:\\xampp\\htdocs\\bamding\\wpmain\\wp-content\\tardis\\testlib\\delete-templates.php'
    assert os.path.exists(script)
    subprocess.check_output(['c:\\xampp\\php\\php.exe', script], shell=True)
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


@given("I have no booking templates")
def step_impl(context):
    """
    Delete any booking templates if present
    :type context behave.runner.Context
    """
    script = 'c:\\xampp\\htdocs\\bamding\\wpmain\\wp-content\\tardis\\testlib\\delete-templates.php'
    assert os.path.exists(script)
    subprocess.check_output(['c:\\xampp\\php\\php.exe', script], shell=True)


@when("I click to add a new template")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    button_add = context.driver.find_element(By.ID, 'booking_template_add_button')
    button_add.click()



@step("I am on the new template page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    url = context.driver.current_url
    expected = r'http://localhost/wordpress/edit-template/?taction=add_new'
    assert url == expected, 'Not at add new template page: {}'.format(url)


@step("I fill in the Name of the template")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    title_input = context.driver.find_element(By.ID, 'template_title')
    title_input.clear()
    title_input.send_keys('test title')
    context.title_input = 'test title'


@step("I fill in the From name")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    from_name = context.driver.find_element(By.ID, 'booking_template_from_name')
    from_name.clear()
    from_name.send_keys("Test User's Band")
    context.from_name = "Test User's Band"


@step("I fill in the Subject field")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    subject = context.driver.find_element(By.ID, 'booking_template_subject')
    context.subject = 'test subject'
    subject.clear()
    subject.send_keys(context.subject)


@step("I fill in the Body text")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    message = context.driver.find_element(By.ID, 'booking_template_message')
    context.message = 'test message'
    message.clear()
    message.send_keys(context.message)


@step("I click Submit")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    save = context.driver.find_element(By.ID, 'template_save')
    save.click()


@then("I am returned to the Templates page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    url = context.driver.current_url
    expected = r'http://localhost/wordpress/booking-templates/'
    assert url == expected, 'Not at templates page. At {}'.format(url)


@step("the template is listed")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    cells = context.driver.find_elements(By.TAG_NAME, 'td')
    found_template = False
    for cell in cells:
        if cell.text == context.title_input:
            found_template = True
            break

    assert found_template, 'Did not find template {}'.format(context.title_input)


@step("I create a template")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    create_template(context)
    pass


def create_template(context):
    context.driver.get('http://localhost/wordpress/booking-templates/')
    button_add = context.driver.find_element(By.ID, 'booking_template_add_button')
    button_add.click()

    title_input = context.driver.find_element(By.ID, 'template_title')
    title_input.clear()
    title_input.send_keys('test title')
    context.title_input = 'test title'

    from_name = context.driver.find_element(By.ID, 'booking_template_from_name')
    from_name.clear()
    from_name.send_keys("Test User's Band")
    context.from_name = "Test User's Band"

    subject = context.driver.find_element(By.ID, 'booking_template_subject')
    context.subject = 'test subject'
    subject.clear()
    subject.send_keys(context.subject)

    message = context.driver.find_element(By.ID, 'booking_template_message')
    context.message = 'test message'
    message.clear()
    message.send_keys(context.message)

    save = context.driver.find_element(By.ID, 'template_save')
    save.click()


@when("I choose that template from the list")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    template = context.driver.find_element(By.LINK_TEXT, context.title_input)
    template.click()


@step("I change the template name")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    title_input = context.driver.find_element(By.ID, 'template_title')
    title_input.clear()
    title_input.send_keys('changed title')
    context.title_input = 'changed title'


@step("I change the From field")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    from_name = context.driver.find_element(By.ID, 'booking_template_from_name')
    from_name.clear()
    from_name.send_keys("Change Test User's Band")
    context.from_name = "Change Test User's Band"


@step("I change the Subject field")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    subject = context.driver.find_element(By.ID, 'booking_template_subject')
    context.subject = 'test subject'
    subject.clear()
    subject.send_keys(context.subject)


@step("I change the body text")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    message = context.driver.find_element(By.ID, 'booking_template_message')
    context.message = 'test message'
    message.clear()
    message.send_keys(context.message)


@step("I see the template name is expected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    title = context.driver.find_element(By.ID, 'template_title')
    assert title.get_attribute('value') == context.title_input


@step("I see the From field is expected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    from_name = context.driver.find_element(By.ID, 'booking_template_from_name')
    assert from_name.get_attribute('value') == context.from_name


@step("I see the Subject field is expected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    subject = context.driver.find_element(By.ID, 'booking_template_subject')
    assert subject.get_attribute('value') == context.subject


@step("I see the body text is expected")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    message = context.driver.find_element(By.ID, 'booking_template_message')
    assert message.text == context.message