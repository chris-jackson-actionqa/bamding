# Created by Seth at 1/12/2015
Feature: Dates and Time Frames Page
  As a musician booking shows,
  I want to set the dates or time frames I want to book
  so that the booker knows when I want to be booked

  Scenario: Default selections of dates page
    Given we go to the Dates page
    Then All Venues is selected
    And Time-Frame option is selected
    And Time-Frame's "Month From" is visible
    And Time-Frame's "Month To" is visible
    And "Update" is visible

