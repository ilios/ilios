@setthree
Feature: Change School
  In order to provide multiple school support
  Users should be able to change their school

  Background:
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I have access in the "Medicine" school
    And I have access in the "Pharmacy" school

  @javascript
  Scenario: Home Tab
    When I navigate to the "Home" tab
    And I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element
    When I select "Pharmacy" from "view-switch"
    Then I should see "Pharmacy" in the "#view-current" element
    When I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element

  @javascript
  Scenario: Calendar Controller
    When I go to "ilios.php/calendar_controller"
    And I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element
    When I select "Pharmacy" from "view-switch"
    Then I should see "Pharmacy" in the "#view-current" element
    When I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element

  @javascript
  Scenario: Management Console
    When I go to "ilios.php/management_console"
    And I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element
    When I select "Pharmacy" from "view-switch"
    Then I should see "Pharmacy" in the "#view-current" element
    When I select "Medicine" from "view-switch"
    Then I should see "Medicine" in the "#view-current" element
