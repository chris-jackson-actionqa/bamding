from behave import *

use_step_matcher("re")

@given("I don't have any band details entered in")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@when("I fill out the band details form")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@step('hit "Submit"')
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@then("I will be returned to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@step("a success message will be shown")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@step("I navigate back to the home page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


@step("I will see my band's details when I return to the band details page")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


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
    assert False, "Not implemented"


@when("I change a field")
def step_impl(context):
    """
    :type context behave.runner.Context
    """
    assert False, "Not implemented"


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
    assert False, "Not implemented"


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
    assert False, "Not implemented"


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
    if required_field == "solo project checkbox":
        context.field_element = context.driver.find_element_by_name("band_details_solo")
    else:
        assert False, "Unknown field: {0}".format(required_field)


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
    assert False, "Not implemented"