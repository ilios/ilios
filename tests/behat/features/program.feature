Feature: Program management
  In order to create the best possible curriculum for students
  Admins should be able engage in evidence-based curriculum management
  Which requires adding and interacting with curricular content

  Background:
    #
    # Log in and navigate to program management page
    #
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I navigate to the "Programs" tab

  @javascript @insulated
  Scenario: Add program and program year
    #
    # Add a program
    #
    When I follow "Add Program"
    And I fill in "Test Med Program" for "new_program_title"
    And I fill in "TMP" for "new_short_title"
    And I press "Done"
    Then I should see "Test Med Program"

    #
    # Add a program year
    #
    When I press "Add New Program Year"
    Then I should see dirty state

    #
    # Publish program year
    #
    When I publish the 1st program year
    Then I should see "Test Med Program"
    And I should see "Matriculation Year:"
    But I should not see dirty state

    #
    # Pick a competency
    #
    When I click the "Edit" link for "Competencies"
    And I click "Medical Knowledge" tree picker item in "competency_pick_dialog" dialog
    And I press the "Done" button in "competency_pick_dialog" dialog
    Then I should see "Medical Knowledge"

    #
    # Pick a school
    #
    When I click the "Edit" link for "Stewarding Departments or School"
    And I click "Other" tree picker item in "steward_pick_dialog" dialog
    And I press the "Done" button in "steward_pick_dialog" dialog
    Then I should see "Other"

    #
    # Add a program objective
    #
    When I follow "Add Objective"
    And I set "eot_textarea_editor" to "Test Objective"
    And I select "Inquiry and Discovery (Medical Knowledge)" from "eot_competency_pulldown"
    And I press the "Done" button in "edit_objective_text_dialog" dialog
    And I publish the 1st program year
    Then I should see "Objectives (1)"
    But I should not see dirty state

    #
    # Add another program objective
    #
    When I follow "Add Objective"
    And I set "eot_textarea_editor" to "Test Objective"
    And I select "Inquiry and Discovery (Medical Knowledge)" from "eot_competency_pulldown"
    And I press the "Done" button in "edit_objective_text_dialog" dialog
    And I publish the 1st program year
    Then I should see "Objectives (2)"
    But I should not see dirty state

    #
    # Now reload the page and check that everything we entered previously is on there.
    #
    When I reload the page
    And I click all collapsed toggles
    Then I should see "Test Med Program"
    And I should see "Matriculation Year:"
    And I should see "Medical Knowledge"
    And I should see "Other"
    And I should see "Objectives (2)"
