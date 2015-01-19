from selenium import webdriver
import bamding_web_test

def before_all(context):
    context.driver = webdriver.Firefox()
    context.bamding = bamding_web_test.BamDingWebTest(context.driver)
    context.driver.maximize_window()
    context.bamding.login()
    context.bamding.remove_all_venues()

    # add two venues
    venue_one = {'name': 'Funhouse', 'email': 'fun@house.com',
                 'country': 'United States', 'state': 'WA', 'city': 'Seattle'}
    context.bamding.add_venue(venue_one)

    venue_two = {'name': "Bob's Burgers", 'email': 'bob@burgers.com',
                 'country': 'United States', 'state': 'ME', 'city': "Seymour's Bay"}
    context.bamding.add_venue(venue_two)

def before_scenario(context, scenario):
    # set those venues to start booking
    context.bamding.go_to_bookings()
    checkboxes = context.driver.find_elements_by_xpath("//table[@id='bookings_table']//input[@type='checkbox']")
    for box in checkboxes:
        if box.is_selected():
            box.click()
        if box.is_selected():
            raise RuntimeError("Could not deselect checkbox.")

def after_all(context):
    context.bamding.remove_all_venues()
    context.driver.close()
