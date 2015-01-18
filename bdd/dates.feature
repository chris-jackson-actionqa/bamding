# Created by Seth at 1/12/2015
Feature: Dates and Time Frames Page
  As a musician booking shows,
  I want to set the dates or time frames I want to book
  so that the booker knows when I want to be booked

  Scenario: Add a time frame of from month to month
    Given we are logged in
    And we have an active venue
    When we go to the Dates page
    And select "All Venues"
    And select "Time-Frame"
    And select from month as "January"
    And select to month as "February"
    And hit "Submit"
    Then