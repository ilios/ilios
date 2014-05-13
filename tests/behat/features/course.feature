Feature: Course management
  In order to create the best possible curriculum for students
  Admins should be able engage in evidence-based curriculum management
  Which requires adding and interacting with curricular content

  Background:
    #phantomjs can't reach the right hand tabs in small windows
    Given I set the window size to "1200" x "1200"
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"
    And I create a test program "Test Program"
    And I create a test learner group for class of "2017" in "Test Program"
    And I navigate to the "Courses and Sessions" tab

  @javascript @insulated
  Scenario: Add Course and Session
    Then I should see "Course Management"
    When I press "Add New Course"
    And I fill in "Test Course" for "new_course_title"
    And I press the "Done" button in "course_add_dialog" dialog
    Then I should see "Test Course"
    When I reload the page
    Then I should see "Test Course"
    When I follow "show_more_or_less_link"
    Then I should see "Publish Course"

    #program cohorts
    When I follow "Select Program Cohorts for Course"
    Then I should see "Select Program Cohorts for Course" in the "#cohort_pick_dialog_c" element
    And I expand "Test Program" tree picker list in "scde_widget_div" dialog
    And I click "Class of 2017" tree picker item in "scde_widget_div" dialog
    And I press the "Done" button in "cohort_pick_dialog_c" dialog
    Then I should see "Class of 2017" in the "#cohort_level_table_div" element

    #add topics
    When I follow "disciplines_search_link"
    And I click on the text "Administrative" in the "discipline_picker_dialog" element
    And I click on the text "Behavior" in the "discipline_picker_dialog" element
    And I press the "Done" button in "discipline_picker_dialog" dialog
    Then I should see "Administrative" in the "#-1_discipline_picker_selected_text_list" element
    Then I should see "Behavior" in the "#-1_discipline_picker_selected_text_list" element

    #remove topics
    When I follow "disciplines_search_link"
    And I click on the text "Administrative" in the "discipline_picker_dialog" element
    And I press the "Done" button in "discipline_picker_dialog" dialog
    Then I should see "Behavior" in the "#-1_discipline_picker_selected_text_list" element
    But I should not see "Administrative" in the "#-1_discipline_picker_selected_text_list" element

    #add directors
    #for some reason just using a follow the "direcors_search_link" was ambigous or not
    #working in phantoms JS, so xpath had to be used
    When I click on the xpath "//*[@id='directors_search_link']"
    And I click on the text "User, Zero" in the "director_picker_dialog" element
    And I press the "Done" button in "director_picker_dialog" dialog
    Then I should see "User, Zero" in the "#-1_director_picker_selected_text_list" element

    #remove directors
    When I click on the xpath "//*[@id='directors_search_link']"
    And I click on the text "User, Zero" in the "director_picker_dialog" element
    And I press the "Done" button in "director_picker_dialog_c" dialog
    Then I should not see "User, Zero" in the "#-1_director_picker_selected_text_list" element
    #put director back so we can search for it on save
    When I click on the xpath "//*[@id='directors_search_link']"
    And I click on the text "User, Zero" in the "director_picker_dialog" element
    And I press the "Done" button in "director_picker_dialog" dialog

    #add MeSH terms
    When I follow "mesh_search_link"
    And I fill in "Ethics" for "mesh_search_terms"
    And I press the "Search" button in "ilios_mesh_picker" dialog
    Then I should see "Bioethics" in the "#ilios_mesh_picker" element
    When I click on the text "Bioethics" in the "ilios_mesh_picker" element
    And I click on the text "Codes of Ethics" in the "ilios_mesh_picker" element
    And I press the "Done" button in "ilios_mesh_picker" dialog
    Then I should see "Bioethics" in the "#-1_mesh_terms_picker_selected_text_list" element
    And I should see "Codes of Ethics" in the "#-1_mesh_terms_picker_selected_text_list" element

    #remove MeSH terms
    When I follow "mesh_search_link"
    And I click on the text "Codes of Ethics" in the "ilios_mesh_picker" element
    And I press the "Done" button in "ilios_mesh_picker" dialog
    Then I should see "Bioethics" in the "#-1_mesh_terms_picker_selected_text_list" element
    But I should not see "Codes of Ethics" in the "#-1_mesh_terms_picker_selected_text_list" element

    #add objectives
    When I follow "add_objective_link"
    Then I should see "Enter Learning Objective"
    When I fill the editor "ilios.cm.editCourseObjectiveDialog.ecoEditor" with "Test course objective 1"
    #Doesn't seem to be any other way to reach this input
    And I click on the xpath "//*[@id='eco_parent_objectives_div']//div[contains(., '(Treatment)')]/div/input"
    And I press the "Done" button in "edit_course_objective_dialog_c" dialog
    When I follow "add_objective_link"
    Then I should see "Enter Learning Objective"
    When I fill the editor "ilios.cm.editCourseObjectiveDialog.ecoEditor" with "Test course objective 2"
    And I click on the xpath "//*[@id='eco_parent_objectives_div']//div[contains(., '(Inquiry and Discovery)')]/div/input"
    And I press the "Done" button in "edit_course_objective_dialog_c" dialog
    Then I should see 2 ".objective_container" elements in the "-1_objectives_container" element
    And I should see "Test course objective 1" in the "#-1_objectives_container" element
    And I should see "Test course objective 2" in the "#-1_objectives_container" element

    #remove objective
    When I click on the xpath "//*[@id='-1_objectives_container']/div[2]//div[contains(@class, 'delete_widget')]"
    Then I press the "Yes" button in "ilios_inform_panel" dialog
    Then I should see 1 ".objective_container" elements in the "-1_objectives_container" element
    And I should see "Test course objective 1" in the "#-1_objectives_container" element
    But I should not see "Test course objective 2" in the "#-1_objectives_container" element
    When I follow "add_objective_link"
    When I fill the editor "ilios.cm.editCourseObjectiveDialog.ecoEditor" with "Test course objective 2"
    And I click on the xpath "//*[@id='eco_parent_objectives_div']//div[contains(., '(Inquiry and Discovery)')]/div/input"
    And I press the "Done" button in "edit_course_objective_dialog_c" dialog

    ##Now do all the same stuff for a session
    When I press "Add Session"
    And I fill in "Test Session" for "1_session_title"
    Then I should see dirty state
    When I press "Save All as Draft"
    Then I should see "This action will save all work in progress as DRAFT form"
    When I press the "Yes" button in "ilios_inform_panel" dialog
    Then I should not see dirty state

    #add offering by group
    When I press "Create Offerings by Group"
    And I follow "Select Groups"
    And I expand "Test Program - Class of 2017" tree picker list in "learner_tree_view_div" dialog
    And I click on the text "Default Group Number 1" in the "learner_tree_view_div" element
    And I press the "Done" button in "multipurpose_session_lightbox" dialog

    #add topics
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'Topics')]//a[text()='Edit']"
    And I click on the text "Administrative" in the "discipline_picker_dialog_c" element
    And I click on the text "Behavior" in the "discipline_picker_dialog_c" element
    And I press the "Done" button in "discipline_picker_dialog_c" dialog
    Then I should see "Administrative" in the "#1_discipline_picker_selected_text_list" element
    Then I should see "Behavior" in the "#1_discipline_picker_selected_text_list" element

    #remove topics
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'Topics')]//a[text()='Edit']"
    And I click on the text "Administrative" in the "discipline_picker_dialog_c" element
    And I press the "Done" button in "discipline_picker_dialog_c" dialog
    Then I should see "Behavior" in the "#1_discipline_picker_selected_text_list" element
    But I should not see "Administrative" in the "#1_discipline_picker_selected_text_list" element

    #add MeSH terms
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'MeSH Terms')]//a[text()='Edit']"
    And I fill in "Ethics" for "mesh_search_terms"
    And I press the "Search" button in "ilios_mesh_picker_c" dialog
    Then I should see "Bioethics" in the "#ilios_mesh_picker_c" element
    When I click on the text "Bioethics" in the "ilios_mesh_picker_c" element
    And I click on the text "Codes of Ethics" in the "ilios_mesh_picker_c" element
    And I press the "Done" button in "ilios_mesh_picker_c" dialog
    Then I should see "Bioethics" in the "#1_mesh_terms_picker_selected_text_list" element
    And I should see "Codes of Ethics" in the "#1_mesh_terms_picker_selected_text_list" element

    #remove MeSH terms
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'MeSH Terms')]//a[text()='Edit']"
    And I click on the text "Codes of Ethics" in the "ilios_mesh_picker_c" element
    And I press the "Done" button in "ilios_mesh_picker_c" dialog
    Then I should see "Bioethics" in the "#1_mesh_terms_picker_selected_text_list" element
    But I should not see "Codes of Ethics" in the "#1_mesh_terms_picker_selected_text_list" element

    #add objectives
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'Objectives')]//a[text()='Add Objective']"
    When I fill the editor "ilios.cm.session.editSessionObjectiveDialog.esoEditor" with "Test Session Objective 1"
    #Doesn't seem to be any other way to reach this input
    And I click on the xpath "//*[@id='eso_parent_objectives_div']//div[contains(., 'Test course objective 1')]/div/input"
    And I press the "Done" button in "edit_session_objective_dialog" dialog
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'Objectives')]//a[text()='Add Objective']"
    When I fill the editor "ilios.cm.session.editSessionObjectiveDialog.esoEditor" with "Test Session Objective 2"
    And I click on the xpath "//*[@id='eso_parent_objectives_div']//div[contains(., 'Test course objective 2')]/div/input"
    And I press the "Done" button in "edit_session_objective_dialog" dialog
    Then I should see 2 ".objective_container" elements in the "1_objectives_container" element
    And I should see "Test Session Objective 1" in the "#1_objectives_container" element
    And I should see "Test Session Objective 2" in the "#1_objectives_container" element

    #remove objective
    When I click on the xpath "//*[@id='1_objectives_container']/div[2]//div[contains(@class, 'delete_widget')]"
    Then I press the "Yes" button in "ilios_inform_panel" dialog
    Then I should see 1 ".objective_container" elements in the "1_objectives_container" element
    And I should see "Test Session Objective 1" in the "#1_objectives_container" element
    But I should not see "Test Session Objective 2" in the "#1_objectives_container" element
    When I click on the xpath "//*[@id='1_collapser']//div[@class='row' and contains(.,'Objectives')]//a[text()='Add Objective']"
    When I fill the editor "ilios.cm.session.editSessionObjectiveDialog.esoEditor" with "Test Session Objective 2"
    And I click on the xpath "//*[@id='eso_parent_objectives_div']//div[contains(., 'Test course objective 2')]/div/input"
    And I press the "Done" button in "edit_session_objective_dialog" dialog

    Then I should see dirty state
    And I press "Save All as Draft"
    Then I should see "This action will save all work in progress as DRAFT form"
    When I press the "Yes" button in "ilios_inform_panel" dialog
    Then I should not see dirty state

    When I press "Publish All"
    And I press the "Yes" button in "ilios_inform_panel" dialog
    Then I should see "Published" in the "#course_form" element
    And I should see "Published" in the "#1_child_draft_text" element
