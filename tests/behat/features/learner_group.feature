Feature: Learner Groups
  In order to facilitate the association of students with courses
  Administrators should be to create learner groups

  Background:
    #
    # Log in
    #
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"

  # issue #199 tagged @ignore until a fix is put in
  @ignore @javascript @insulated
  Scenario: Student added to one group appears in picker for other groups
    When I navigate to the "Programs" tab
    And I follow "Add Program"
    And I fill in "new_program_title" with "Foo"
    And I fill in "new_short_title" with "Foo"
    And I press "Done"
    And I wait for "expand_program_years_link" to be enabled
    And I wait 2 seconds
    And I press "Add New Program Year"
    And I wait for "1_child_publish" to be enabled
    And I wait 2 seconds
    And I press "1_child_publish"
    And I follow "show_more_or_less_link"
    And I press "Publish Now"
    And I wait 2 seconds
    And I go to "/ilios.php/group_management"
    And I follow "Select Program and Cohort"
    And I wait for "cohort_pick_dialog" to be enabled
    And I wait 2 seconds
    And I click on the text "Foo"
    And I click on the text starting with "Class of "
    And I wait 2 seconds
    And I press "Add New Members to Cohort"
    And I wait for "em_last_name" to be visible
    And I fill in "em_last_name" with "Smith"
    And I fill in "em_first_name" with "Jane"
    And I fill in "em_email" with "Jane.Smith@example.edu"
    And I fill in "em_uc_id" with "123456789"
    And I press "Add User"
    And I wait for "em_transaction_status" to be enabled
    And I press "Done"
    And I press "Add a New Student Group"
    And I wait for "1_collapse_summary_text" to be enabled
    And I click on the text "Default Group Number 1"
    And I follow "1_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    # remove Jane Smith
    Then I should see "Smith, Jane"
    And I click on the text "Smith, Jane"
    And I press the "Done" button in "manage_member_pick_dialog" dialog
    And I follow "1_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    # now re-add Jane Smith
    Then I should see "Smith, Jane"
    And I click on the text "Smith, Jane"
    And I press the "Done" button in "manage_member_pick_dialog" dialog

    And I press "Add a New Student Group"
    And I wait for "2_collapse_summary_text" to be enabled
    And I click on the text "Default Group Number 2"
    And I follow "2_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    # Look for bug #199 where student would no longer appear in the next group's picker
    Then I should see "Smith, Jane"
