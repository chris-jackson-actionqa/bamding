Feature: Band Details
  As a customer of BamDing trying to book my band
  I want to easily input my band's details
  So that I can generate booking templates to book shows

  Scenario: New band details
    Given I don't have any band details entered in
    When I fill out the band details form
    And hit "Submit"
    Then I will be returned to the band details page
    And a success message will be shown
    And I navigate back to the home page
    And I will see my band's details when I return to the band details page

  Scenario: Editing band details
    Given I don't have any band details entered in
    When I fill out the band details form
    And hit "Submit"
    And a success message will be shown
    And I navigate back to the home page
    And I will see my band's details when I return to the band details page
    When I change a field
    And hit "Submit"
    Then I will be returned to the band details page
    And a success message will be shown
    And I navigate back to the home page
    And I will see my band's details when I return to the band details page

  Scenario: Required fields enforced before submitting
    Given I don't have any band details entered in
    And I go to the band details page
    When I leave a required field blank
    Then the submit button is not enabled

  Scenario Outline: Are expected fields present
    Given I check for <required_field>
    Then <required_field> is on the page
    And <label> is above <required_field>

    Examples: Required fields and labels
    | required_field | label |
    | solo project checkbox | Solo Project? Check here: |
    | band name             | Band's Name:              |
    | genre                 | Main Genre of Music:      |
    | sounds like           | What popular bands do you sound like? |
    | booking email         | Email used for booking:   |
    | main website          | Main Website:             |
    | music                 | Where To Hear Your Music? |

    Examples: Optional fields and labels
    | required_field | label |
    | phone number   | Band's Booking Phone Number: |
    | local draw     | What's your local draw?      |
    | video          | Where are your live videos? (Optional, but highly recommended.) |
    | calendar       | Booking calendar or show list                                   |
    | more sites     | Additional social media or relevant sites for your band.        |

