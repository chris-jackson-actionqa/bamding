Feature: Booking Templates
  As a musician booking shows
  I want to create booking templates
  So that I can send customized booking requests

  Scenario: Add a new template
    Given I have no booking templates
    And I go to the booking templates page
    When I click to add a new template
    And I am on the new template page
    And I fill in the Name of the template
    And I fill in the From name
    And I fill in the Subject field
    And I fill in the Body text
    And I click Submit
    Then I am returned to the Templates page
    And the template is listed

  Scenario: Edit a template
    Given I create a template
    When I choose that template from the list
    And I change the template name
    And I change the From field
    And I change the Subject field
    And I change the body text
    And I click Submit
    Then I am returned to the Templates page
    And the template is listed
    And I choose that template from the list
    And I see the template name is expected
    And I see the From field is expected
    And I see the Subject field is expected
    And I see the body text is expected

  Scenario: Delete all templates
    Given I have 3 templates
    When I choose all three
    And I choose bulk action to Remove
    And I click Apply
    Then
