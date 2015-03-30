from behave import *
from selenium.webdriver.common.keys import Keys
import subprocess
import os

use_step_matcher("re")

@given("I don't have any band details entered in")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    all_inputs = context.driver.find_elements_by_tag_name("input")
    for input in all_inputs:
        if input.get_attribute("type") == "text":
            input.clear()

    all_textareas = context.driver.find_elements_by_tag_name("textarea")
    for textarea in all_textareas:
        textarea.clear()

    elem = context.driver.find_element_by_name('band_details_name')
    elem.send_keys("a")
    elem.send_keys(Keys.BACK_SPACE)


@when("I fill out the band details form")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.input_elems = {
        "band_details_name": "Super Silly String Band",
        "band_details_genre": "Stoner Rock",
        "band_details_sounds_like": "Red Fang",
        "band_details_email": "silly@string.com",
        "band_details_website": "www.20minutesongs.com",
        "band_details_music": "www.20minutesongs.com/listen",
        "band_details_phone": "206-123-4465",
        "band_details_draw": "10",
        "band_details_video": "www.sillyband.com/music",
        "band_details_calendar": "www.somecalendar.com/nope",
        "band_details_sites": "{0}{1}{2}{1}{3}{1}".format(
            "https://facebook.com/sillyband",
            Keys.ENTER,
            "twitter.com/sillyband",
            "instagram.com/sillyband"
        )
    }

    for key, value in context.input_elems.items():
        input = context.driver.find_element_by_name(key)
        input.send_keys(value)


@step('hit "Submit"')
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    submit = context.driver.find_element_by_id('band_details_submit')
    assert submit.is_enabled(), "Submit button not enabled."
    submit.click()


@then("I will be returned to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert "http://localhost/wordpress/band-details/" == context.driver.current_url, "Not on band details page"


@step("a success message will be shown")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    message = context.driver.find_element_by_id('band_details_status')
    lines = message.text.splitlines()
    assert "Successfully updated your band details." == lines[0].strip(),\
           "Missing success message. Received: {0}".format(message.text)
    assert "You rock!!!" == lines[1].strip(),\
           "Missing success message. Received: {0}".format(message.text)


@step("I navigate back to the home page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.get("http://localhost/wordpress")


@step("I will see my band's details when I return to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.get("http://localhost/wordpress/band-details/")
    for key, value in context.input_elems.items():
        input_elem = context.driver.find_element_by_name(key)
        elem_value = input_elem.get_attribute('value')
        if key == 'band_details_sites':
            received = elem_value.splitlines()
            expected = value.split('\ue007')
            for i, line in enumerate(expected):
                if line == '':
                    continue
                assert received[i].split() == expected[i].split(), "Expected '{0}'. Received '{1}'".format(
                    expected, received
                )
        else:
            assert elem_value == value,\
                "{0} has unexpected value. Expected '{1}'. Received '{2}'".format(
                    key, value, input_elem.text
                )


@given("I already have my band details entered in")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@step("I go to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.driver.get('http://localhost/wordpress/band-details/')


@when("I change a field")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    context.input_elems['band_details_name'] = "My Crazy Pants On Fire"
    elem = context.driver.find_element_by_name('band_details_name')
    elem.clear()
    elem.send_keys(context.input_elems['band_details_name'])


@step("I will see my saved changes when I return to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@when("I leave a required field blank")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    elem = context.driver.find_element_by_name('band_details_sounds_like')
    elem.clear()
    elem.send_keys('a')
    elem.send_keys(Keys.BACKSPACE)


@step("try to hit submit")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@then("the submit button is not enabled")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    submit = context.driver.find_element_by_id('band_details_submit')
    assert not submit.is_enabled(), "Submit button is enabled when should be disabled"


@step("the required field \(or fields\) are highlighted")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@given("I check for (?P<required_field>.+)")
def step_impl(context, required_field):
    """
    :type context behave.runner.Context
    :type required_field str
    """
    field_id_dict = {"solo project checkbox": "band_details_solo",
                     "band name": "band_details_name",
                     "genre": "band_details_genre",
                     "sounds like": "band_details_sounds_like",
                     "booking email": "band_details_email",
                     "main website": "band_details_website",
                     "music": "band_details_music",
                     "phone number": "band_details_phone",
                     "local draw": "band_details_draw",
                     "video": "band_details_video",
                     "calendar": "band_details_calendar",
                     "more sites": "band_details_sites"}
    context.field_element = context.driver.find_element_by_name(field_id_dict[required_field])
    context.field_name = field_id_dict[required_field]


@then("(?P<required_field>.+) is on the page")
def step_impl(context, required_field):
    """
    :type context behave.runner.Context
    :type required_field str
    """
    assert context.field_element.is_displayed(), "Required field not displayed: {0}".format(required_field)


@step("(?P<label>.+) is above (?P<required_field>.+)")
def step_impl(context, label, required_field):
    """
    :type context behave.runner.Context
    :type label str
    :type required_field str
    """
    label_elem = context.driver.find_element_by_xpath("//*[@name='{0}']/preceding::label[1]".format(context.field_name))
    elem_text = label_elem.text
    assert elem_text == label


@given("I have no band details entered")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    delete_band_details()


def delete_band_details():
    """
    Call the delete band details script. Deletes test_user band details.
    :return:
    """
    script = 'c:\\xampp\\htdocs\\bamding\\wpmain\\wp-content\\tardis\\testlib\\delete-band-details.php'
    assert os.path.exists(script)
    subprocess.check_output(['c:\\xampp\\php\\php.exe', script], shell=True)


@step("I have entered my band details")
def step_impl(context):
    """
    Enter generic band details
    :type context behave.runner.Context
    """
    script = 'c:\\xampp\\htdocs\\bamding\\wpmain\\wp-content\\tardis\\testlib\\generic-band-details.php'
    assert os.path.exists(script)
    subprocess.check_output(['c:\\xampp\\php\\php.exe', script], shell=True)