Feature: User authentication
  In order to ensure access to the application for authorized users only
  Users should be able to log in to the system
  Which requires them to submit proper credentials to the application's authentication system via the login form

  Scenario: Login
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    # post-login you should be on the dashboard
    Then I should be on "/dashboard_controller"

  Scenario: Login without credentials
    Given I am on the Ilios home page
    And I log in as "" with password ""
    # submitting the form without creds should take you right back to it, with an error message being displayed on the page.
    Then I should be on "authentication_controller/login"
    And I should see "The user name and/or password entered were not valid."

  Scenario: Login with invalid credentials
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "invalid password"
    # submitting the form without creds should take you right back to it, with an error message being displayed on the page.
    Then I should be on "authentication_controller/login"
    And I should see "The user name and/or password entered were not valid."

  Scenario: Logout
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I follow "Logout"
    Then I should be on "/authentication_controller"
    # try to access the dashboard after logging out - see that you are taken back to the login page
    Given I go to "/ilios.php/dashboard_controller"
    Then I should be on "/authentication_controller"

