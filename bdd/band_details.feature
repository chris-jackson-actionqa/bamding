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
    Given I already have my band details entered in
    And I go to the band details page
    When I change a field
    And hit "Submit"
    Then I will be returned to the band details page
    And a success message will be shown
    And I navigate back to the home page
    And I will see my saved changes when I return to the band details page

  Scenario: Required fields enforced before submitting
    Given I don't have any band details entered in
    And I go to the band details page
    When I leave a required field blank
    And try to hit submit
    Then the submit button is not enabled
    And the required field (or fields) are highlighted

  @wip
  Scenario Outline: Are expected fields present
    Given I check for <required_field>
    Then <required_field> is on the page
    And <required_field>'s <label> is on the page

    Examples: Required fields and labels
    | required_field | label |
    | solo project checkbox | Solo project? Check here: |
    | band name             | Band's Name:              |
    | genre                 | Main Genre of Music:      |
    | sounds like           | What popular bands do you sound like? |
    | booking email         | Email used for booking:   |
    | main website          | Main Website:             |
    | music                 | Where To Hear Your Music? |

