Feature: Bookings page for starting, pausing, and editing bookings for venues.
  As a musician
  I want to paused, start, and edit my bookings
  So that I can control how my venues are being contacted

  Scenario: Header checkbox should select all checkboxes in table
    Given I click the table header checkbox
    Then all the checkboxes in the table should be checked

  Scenario: Header checkbox should un-select all selected checkboxes in table
    Given I click the table header checkbox
    And see that all the checkboxes are selected
    Then I click the table header checkbox
    And see that none of the checkboxes are selected

  Scenario: Un-selecting a checkbox in the table should un-select the header checkbox
    Given I click the table header checkbox
    And see that all the checkboxes are selected
    When I click a row's checkbox to deselect it
    Then the table header checkbox should be un-selected

  Scenario: Bulk pause all venues
    Given I select all venues
    And I select the bulk action Pause
    And I select Apply
    Then Both venues will show as paused
