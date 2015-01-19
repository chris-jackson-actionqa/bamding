# Created by Seth at 1/12/2015
Feature: Home page navigation
  As a musician,
  I want to navigate the home page of the site
  so that I can find where I want to go

  Scenario: Go to home page
    Given we go to the home page
    Then we are on the home page

  Scenario: Go to dates page from menu
    Given we go to the home page
    And we click on the menu dropdown
    When we click on "Dates"
    Then we go to the "Dates" page