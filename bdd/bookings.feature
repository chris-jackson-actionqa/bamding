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

  Scenario Outline: Selecting different bulk actions should enable or disable the "Apply" button
    Given I <check_venues> check all venues
    When I select <bulk_action>
    Then the Apply button should be <apply_enabled>

    Examples:
    | check_venues | bulk_action | apply_enabled |
    | do          | pause       | enabled        |
    | do          | start       | enabled        |
    | do          | bulk        | disabled       |
    | do not      | pause       | disabled       |
    | do not      | start       | disabled       |
    | do not      | bulk        | disabled       |

  Scenario: Bulk pause all venues
    Given I select all venues
    And I select the bulk action Pause
    And I click Apply
    When all venues are selected
    And I choose Start for all venues
    And I click Apply
    Then All venues will show as active

  Scenario: Bulk activate all venues
    Given I select all venues
    And I select Start for all venues
    And I select Apply
    When all venues are selected
    And I choose the bulk action Pause
    And I click Apply
    Then All venues will show as paused

  Scenario: Active venues show before paused venues
    Given I select all venues
    And I select the bulk action Pause
    And I click Apply
    When I choose the second venue
    And I choose Start Booking
    And I click Apply
    Then the second venue should be at the top of the table below the header

  @wip
  Scenario: Filter venues by state
    Given The "By State" filter is chosen
    And a venue with the state "WA" exists
    When I type in "WA" into the filter
    Then only "WA" state venues are in the table

  Scenario: Filter venues by city
    Given the "By City" filter is chosen
    And a venue with the city "Seattle" exists
    When I type in "Sea" into the filter
    Then only cities with "sea" in their name are in the table

  Scenario: Filter venues by name
    Given the "By name" filter is chosen
    And a venue with the name "Funhouse" exists
    When I type in "fun" into the filter
    Then only venues with "fun" in their name are in the table

  Scenario: Filter venues by category
    Given the "By Category" filter is chosen
    And a venue with the category "Dive Bar" exists
    When I type in "dive bar" into the filter
    Then only venues with "dive bar" in their category are in the table

  Scenario: Filter on all venues
    Given the "By All" filter is chosen
    And a venue has "xay" in their name
    And a venue has "xay" in their city
    And a venue has "xay" in their category
    When I type in "xay" into the filter
    Then those 3 venues are in the table
