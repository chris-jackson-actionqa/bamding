Feature: Dates and time frames
  In order to see and change dates for booking venues
  As a musician booking shows
  I want to change dates for all my venues and all the way down to 
    categories, countries, states, cities, and even individual venues


  Scenario: Clicking on date type options hide other date options
    Given we're on the dates and timeframe page
    When a user clicks on an date type option
    Then all other options' details are hidden
    And the clicked on option's details are revealed
