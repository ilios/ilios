@settwo
Feature: Learner Groups
  In order to facilitate the association of students with courses
  Administrators should be to create learner groups

  Background:
    #
    # Log in
    #
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I create a test program "Test Learner Program"

  # issue #199 tagged @ignore until a fix is put in
  @javascript @insulated
  Scenario: Student added to one group appears in picker for other groups
    When I go to "/ilios.php/group_management"
    And I follow "Select Program and Cohort"
    And I wait for "cohort_pick_dialog" to be enabled
    And I expand "Test Learner Program" tree picker list in "cohort_pick_dialog_c" dialog
    And I click "Class of 2017" tree picker item in "cohort_pick_dialog_c" dialog
    And I press "Add New Members to Cohort"
    And I fill in "em_last_name" with "Smith"
    And I fill in "em_first_name" with "Jane"
    And I fill in "em_email" with "Jane.Smith@example.edu"
    And I fill in "em_uc_id" with "123456789"
    And I press "Add User"
    And I press the "Done" button in "add_new_members_dialog" dialog
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
    # Look for bug #199 where student would no longer appear in the next group's picker
    Then I should see "Smith, Jane" in the "#ugt__selected_item_list" element
    But I should not see "Smith, Jane" in the "#ugt_selector_tab" element

    When I press the "Done" button in "manage_member_pick_dialog" dialog
    And I follow "1_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    Then I should see "Smith, Jane"
    When I click on the text "Smith, Jane"
    And I press the "Done" button in "manage_member_pick_dialog" dialog
    And I follow "2_add_members"
    #make sure that student is only in the seelcted picker and not the to-be selected picker
    Then I should see "Smith, Jane" in the "#ugt__selected_item_list" element
    But I should not see "Smith, Jane" in the "#ugt_selector_tab" element
