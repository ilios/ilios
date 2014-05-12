Feature: Learner Groups
  In order to facilitate the association of students with courses
  Administrators should be to create learner groups

  Background:
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I create a test program "Test Learner Program"
    And the following learners exist in the "2017" "Test Learner Program" program:
    | first  | last  | email | ucid |
    | Jane   | Smith | first@example.com | 123456 |
    | John   | Smith | second@example.com | 123457 |
    | Greg   | Smith | third@example.com | 123458 |
    And I clear all learner groups in the "2017" "Test Learner Program" program
    And I navigate to the "Learner Groups" tab
    And I follow "Select Program and Cohort"
    And I wait for "cohort_pick_dialog" to be enabled
    And I expand "Test Learner Program" tree picker list in "cohort_pick_dialog_c" dialog
    And I click "Class of 2017" tree picker item in "cohort_pick_dialog_c" dialog

  @javascript @insulated
  Scenario: New groups added should contain the entire cohort
    When I press "Add a New Student Group"
    And I wait for "1_collapse_summary_text" to be enabled
    And I click on the text "Default Group Number 1"
    And I follow "1_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    Then I should see "Smith, Jane" in the "#ugt__selected_item_list" element
    And I should see "Smith, John" in the "#ugt__selected_item_list" element
    And I should see "Smith, Greg" in the "#ugt__selected_item_list" element
    But I should not see "Smith, Jane" in the "#ugt_selector_tab" element
    And I should not see "Smith, John" in the "#ugt_selector_tab" element
    And I should not see "Smith, Greg" in the "#ugt_selector_tab" element

  @javascript @insulated
  Scenario: Subgroups should not contain the owning group members
    When I press "Add a New Student Group"
    And I wait for "1_collapse_summary_text" to be enabled
    And I click on the text "Default Group Number 1"
    And I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(., 'Sub-Groups:')]//a[text() = 'Edit']"
    And I press "Add a New Sub-Group"
    And I wait for "1_collapse_summary_text" to be enabled
    And I click on the text "Default Group Number 1 1"
    And I follow "1_add_members"
    And I wait for "manage_member_pick_dialog" to be enabled
    Then I should see "Smith, Jane" in the "#ugt_selector_tab" element
    And I should see "Smith, John" in the "#ugt_selector_tab" element
    And I should see "Smith, Greg" in the "#ugt_selector_tab" element
    But I should not see "Smith, Jane" in the "#ugt__selected_item_list" element
    And I should not see "Smith, John" in the "#ugt__selected_item_list" element
    And I should not see "Smith, Greg" in the "#ugt__selected_item_list" element

  @javascript @insulated
  Scenario: Students selected in one group should not disapear from the picker for other groups
    When I press "Add a New Student Group"
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
