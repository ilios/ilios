Feature: Access, program creation, cohort creation, course creation (independent of those other two)
    In order to create the best possible curriculum for students
    Admins should be able engage in evidence-based curriculum management
    Which requires adding and interacting curricular content

Scenario: Add program
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I navigate to the "Programs" tab
    And I click the "Add New Program" link
    And I enter "Test Med Program" into "new_program_title"
    And I enter "TMP" into "new_short_title"
    And I click the "Done" button
    Then I should see "Test Med Program"
    And I click the "Add Academic Year" button 
    Then there is a "dirty_state" class
    And I click the "Publish All" button
    And I click the "Yes" button
    Then I should see "Test Med Program"
    And there is no "dirty_state" class
    And I click all expanded toggles
    And I click the "Add Academic Year" button
    And I click the "Edit" button for "Competencies"
    And I click "Medical Knowledge"
    And I click the "Done" button
    And I click the "Edit" button for "Stewarding Departments or School"
    And I click "Medicine"
    And I click the "Done" button
    And I click the "Add Objective" link
    And I set "eot_textarea_editor" to "Test Objective"
    And I set "eot_competency_pulldown" to "Inquiry and Discovery (Medical Knowledge"
    And I click the "Done" button
    And I click the "Publish All" button
    And I click the "Yes" button
    Then there is no "dirty_state" class
    And I navigate to the "Learner Groups" tab
    And I click the "Select Program and Cohort" link
    And I click "Test Med Program"
    And I click the first "Class of "
    And I click the "Add a New Student Group" button
    And I navigate to the "Courses and Sessions" tab
    And I click the "Add New Course" button
    And I set "new_course_title" to "Sample Course"
    And I click the "Done" button
    Then there is no "dirty_state" class
    And I should see "Sample Course"
    And I click the "Search" button
    And I set "course_search_terms" to "Sample Course"
    And I click the first element with class "search_icon_button"
    Then "course_search_results_list" should contain "Sample Course"