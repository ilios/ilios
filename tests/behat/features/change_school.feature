Feature: Change School
  In order to provide multiple school support
  Users should be able to change their school

  Background:
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"

  @javascript @insulated
  Scenario: Change Selected School
    Given I have access in the "Medicine" school
    And I have access in the "Pharmacy" school
    When I change to the "Medicine" school
    Then I am in the "Medicine" school
    When I change to the "Pharmacy" school
    Then I am in the "Pharmacy" school
    When I change to the "Medicine" school
    Then I am in the "Medicine" school

