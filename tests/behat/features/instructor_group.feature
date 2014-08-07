@setone
Feature: Instructor Groups
  In order to facilitate the association of instructors with courses
  Administrators should be to create instructor groups

  Background:
    And I clear all instructor groups in the "Medicine" school
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I navigate to the "Instructors" tab

  @javascript @insulated
  Scenario: New users must have valid data
    When I press "Add a New Instructor Group"
    And I wait for "1_collapse_summary_text" to be enabled
    And I follow "1_igm_edit_membership"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Last Name is Required"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "Last Name" for "em_last_name"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: First Name is Required"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "First Name" for "em_first_name"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Email is Required"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "badaddress" for "em_email"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Email address is not valid"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "root@example.com" for "em_email"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Campus ID is Required"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "1234567" for "em_uc_id"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Campus ID is not long enough"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "1234567890" for "em_uc_id"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Campus ID is too long"
    When I press the "Ok" button in "ilios_alert_panel" dialog
    When I fill in "123456789" for "em_uc_id"
    And I press "Add User"
    Then I should see "There was a problem adding the instructor: Sorry, a user with that email is already in the system: Please verify your information and try again."