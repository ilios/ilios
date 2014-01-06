Feature: Access, program creation, cohort creation, course creation (independent of those other two)
  In order to create the best possible curriculum for students
  Admins should be able engage in evidence-based curriculum management
  Which requires adding and interacting with curricular content

  @javascript @insulated
  Scenario: Add program
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I navigate to the "Programs" tab
    And I click the "Add Program" link
    And I fill in "Test Med Program" for "new_program_title"
    And I fill in "TMP" for "new_short_title"
    And I press "Done"
    And I wait 3 seconds
    Then I should see "Test Med Program"
    And I press "Add New Program Year"
    Then I should see a ".dirty_state" element
    # 'And I press "Publish"' does not work, hence the workaround of specifying the element id instead.
    And I press "1_child_publish"
    Then I should see "Test Med Program"
    And I wait 3 seconds
    And I should not see a ".dirty_state" element
    And I click all expanded toggles
    And I press "Add New Program Year"
    And I press the "Edit" button for "Competencies"
    And I click "Medical Knowledge"
    And I press "Done"
    And I press the "Edit" button for "Stewarding Departments or School"
    And I click "Medicine"
    And I press "Done"
    And I click the "Add Objective" link
    And I set "eot_textarea_editor" to "Test Objective"
    And I set "eot_competency_pulldown" to "Inquiry and Discovery (Medical Knowledge"
    And I press "Done"
    And I press "Publish"
    Then I should not see a ".dirty_state" element
    And I navigate to the "Learner Groups" tab
    And I click "Select Program and Cohort"
    And I click "Test Med Program"
    And I click the first "Class of "
    And I press "Add a New Student Group"
    And I navigate to the "Courses and Sessions" tab
    And I press "Add New Course"
    And I set "new_course_title" to "Sample Course"
    And I press "Done"
    Then I should not see a ".dirty_state" element
    And I should see "Sample Course"
    And I press "Search"
    And I set "course_search_terms" to "Sample Course"
    And I click the first element with class "search_icon_button"
    Then "course_search_results_list" should contain "Sample Course"
