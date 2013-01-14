<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) for the report table.
 * Provides CRUD functionality for reports in Ilios.
 * @todo optimize report queries
 */
class Report extends Abstract_Ilios_Model
{
    const REPORT_NOUN_COURSE = 'course';
    const REPORT_NOUN_SESSION = 'session';
    const REPORT_NOUN_PROGRAM = 'program';
    const REPORT_NOUN_PROGRAM_YEAR = 'program year';
    const REPORT_NOUN_INSTRUCTOR = 'instructor';
    const REPORT_NOUN_INSTRUCTOR_GROUP = 'instructor group';
    const REPORT_NOUN_COMPETENCY = 'competency';
    const REPORT_NOUN_TOPIC = 'topic';
    const REPORT_NOUN_LEARNING_MATERIAL = 'learning material';
    const REPORT_NOUN_MESH_TERM = 'mesh term';
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('report', array('report_id'));
        $this->createDBHandle();
        $this->load->model('Program', 'program', TRUE);
        $this->load->model('Program_Year', 'programYear', TRUE);
    }

    /**
     * Saves a new report.
     * @param string $subjectTable
     * @param string $prepositionalObjectTable
     * @param array $poValues
     * @return int the id of the newly created report record
     * @todo flesh out code docs
     */
    public function saveReport ($subjectTable, $prepositionalObjectTable, $poValues, $title)
    {
        $DB = $this->dbHandle;

        $newRow = array();
        $newRow['report_id'] = null;

        $newRow['user_id'] = $this->session->userdata('uid');
        $dtCreationDate = new DateTime('now', new DateTimeZone('UTC'));
        $newRow['creation_date'] = $dtCreationDate->format('Y-m-d H:i:s');
        $newRow['subject'] = $subjectTable;
        $newRow['deleted'] = 0;
        $newRow['title'] = null;

        if ((! is_null($prepositionalObjectTable)) && (strlen($prepositionalObjectTable) > 0)) {
            $newRow['prepositional_object'] = $prepositionalObjectTable;
        }
        else {
            $newRow['prepositional_object'] = null;
        }

        if ((! is_null($title)) && (strlen($title) >0)) {
            $newRow['title'] = $title;
        }

        $DB->insert($this->databaseTableName, $newRow);

        $newRowId = $DB->insert_id();

        if (($newRowId != -1) && (! is_null($prepositionalObjectTable))) {
            foreach ($poValues as $rowId) {
                $newRow = array();

                $newRow['report_id'] = $newRowId;
                $newRow['prepositional_object_table_row_id'] = $rowId;
                $newRow['deleted'] = 0;

                $DB->insert('report_po_value', $newRow);
            }
        }

        return $newRowId;
    }

    /**
     * Deletes a given report.
     * @param int $reportId the id of the report to delete.
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function deleteReport ($reportId)
    {
        $DB = $this->dbHandle;

        $updateRow = array();
        $updateRow['deleted'] = 1;

        $DB->where('report_id', $reportId);
        $DB->update($this->databaseTableName, $updateRow);

        $row = $this->getRowForPrimaryKeyId($reportId);

        return ($row->deleted == 1);
    }

    /**
     * Gets all saved reports for a user.
     * @todo refactor userdata out into function arguments
     * @return array a nested array of arrays, each sub-array representing a report record.
     */
    public function getAllReports ($userId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('user_id', $userId);
        $DB->where('deleted', 0);
        $queryResults = $DB->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            $report = array();
            $reportId = $row['report_id'];
            $report['report_id'] = $reportId;
            $report['subject'] = $row['subject'];
            $report['prepositional_object'] = $row['prepositional_object'];
            $report['po_values'] = $this->getReportPrepositionalObjectValuesForReport($reportId);
            $report['po_display_values'] = $this->getReportPrepositionalObjectDisplayValues(
                $row['prepositional_object'], $reportId);
            $report['title'] = $row['title'];

            array_push($rhett, $report);
        }

        return $rhett;
    }

    /**
     * Retrieves all "prepositional object (PO)" values for a given report.
     * NOTE: POs are essentially database table names.
     * @param int $reportId the report id
     * @return array an array of strings, each string representing a PO.
     */
    protected function getReportPrepositionalObjectValuesForReport ($reportId)
    {
        $rhett = array();
        $DB = $this->dbHandle;
        $DB->where('report_id', $reportId);
        $queryResults = $DB->get('report_po_value');
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row['prepositional_object_table_row_id']);
        }
        return $rhett;
    }

    /**
     * Retrieves all "prepositional object (PO)" display values for a given report.
     *
     * @param $po
     * @param $reportId
     * @return array
     */
    protected function getReportPrepositionalObjectDisplayValues($po, $reportId)
    {
        $rhett = array();
        $DB = $this->dbHandle;
        $DB->where('report_id', $reportId);
        $queryResults = $DB->get('report_po_value');
        foreach ($queryResults->result_array() as $row) {
            $poValue = $row['prepositional_object_table_row_id'];
            $poDisplayValue = 'undefined';
            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValue);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValue);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM :
                    $programRow = $this->program->getRowForPrimaryKeyId($poValue);
                    if ($programRow) {
                        $poDisplayValue = $programRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM_YEAR :
                    $programYearTitle = $this->_getDisplayForProgramYear($poValue);
                    if (false !== $programYearTitle) {
                        $poDisplayValue = $programYearTitle;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $userName = $this->user->getFormattedUserName($poValue, true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValue);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValue);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_COMPETENCY :
                    $competencyRow = $this->competency->getRowForPrimaryKeyId($poValue);
                    if ($competencyRow) {
                        $poDisplayValue = $competencyRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValue);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    $meshRow = $this->mesh->getRowForPrimaryKeyId($poValue);
                    if ($meshRow) {
                        $poDisplayValue = $meshRow->name;
                    }
                    break;
                default :
                    $poDisplayValue = 'not mapped';
            }
            array_push($rhett, $poDisplayValue);
        }
        return $rhett;
    }

    /**
     * Runs a given report in the context of a given school.
     * @param int $reportId the report id
     * @param int $schoolId the school id
     * @return array an associative array containing data keyed off by
     *     'report_subject'  the subject ("type") of the report
     *     'list_type'       rendering type of the retrieved data, either 'link' or 'text'
     *     'link_items'      a nested array of arrays, each sub-array representing a row of report results
     */
    public function runReport ($reportId, $schoolId)
    {
        $rhett = array();
        $rhett['po_display_value'] = 'not found';

        $reportRow = $this->getRowForPrimaryKeyId($reportId);
        if (! is_null($reportRow)) {
            $poValues = null;

            if (! is_null($reportRow->prepositional_object)) {
                $poValues = $this->getReportPrepositionalObjectValuesForReport($reportId);
            }

            $rhett['report_subject'] = $reportRow->subject;

            switch ($reportRow->subject) {
                case self::REPORT_NOUN_COURSE :
                    $rhett['list_type'] = 'link';
                    $rhett['link_items'] = $this->handleReportForCourse($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_SESSION :
                    $rhett['list_type'] = 'link';
                    $rhett['link_items'] = $this->handleReportForSession($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_PROGRAM :
                    $rhett['list_type'] = 'link';
                    $rhett['link_items'] = $this->handleReportForProgram($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_PROGRAM_YEAR :
                    $rhett['list_type'] = 'link';
                    $rhett['link_items'] = $this->handleReportForProgramYear($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForInstructor($reportRow, $poValues, $rhett['po_display_value']);
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForInstructorGroup($reportRow, $poValues, $rhett['po_display_value']);
                    break;
                case self::REPORT_NOUN_COMPETENCY :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForCompetency($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForTopic($reportRow, $poValues, $rhett['po_display_value'], $schoolId);
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForLearningMaterial($reportRow, $poValues, $rhett['po_display_value']);
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    $rhett['list_type'] = 'text';
                    $rhett['text_items'] = $this->handleReportForMeSHTerms($reportRow, $poValues, $rhett['po_display_value']);
                    break;
                default :
                    // do nothing
            }
        }
        return $rhett;
    }

    /**
     * Runs a course-report for a given report record and and filter values.
     * The courses returned are deleted and restricted by $schoolId
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @param int $schoolId the school id
     * @return array a nested array of arrays. Each sub-array contains the course title and admin link.
     */
    protected function handleReportForCourse ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryResults = null;

        if (is_null($poValues)) {
            $DB->where('deleted', 0);
            $DB->where('owning_school_id', $schoolId);
            $DB->order_by('title');
            $DB->order_by('start_date');
            $DB->order_by('end_date');

            $queryResults = $DB->get($this->course->getTableName());
        } else {
            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] = $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT `course`.`course_id`
                                     FROM `course`, `session`
                                    WHERE `course`.`course_id` = `session`.`course_id`
                                      AND `session`.`session_id` = ' . $clean['id'] . '
                                      AND `course`.`deleted` = 0
                                      AND `course`.`owning_school_id` = ' . $schoolId . '
                                 ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM :
                    $queryString = 'SELECT DISTINCT `course`.`course_id`
                                      FROM `course_x_cohort`, `cohort`, `program_year`, `course`
                                     WHERE `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `cohort`.`program_year_id` = `program_year`.`program_year_id`
                                       AND `program_year`.`program_id` = ' . $clean['id'] . '
                                       AND `course_x_cohort`.`course_id` = `course`.`course_id`
                                       AND `course`.`deleted` = 0
                                       AND `course`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';

                    $programRow = $this->program->getRowForPrimaryKeyId($poValues[0]);
                    if ($programRow) {
                        $poDisplayValue = $programRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM_YEAR :
                    $queryString = 'SELECT DISTINCT `course`.`course_id`
                                      FROM `course_x_cohort`, `cohort`, `course`
                                     WHERE `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `cohort`.`program_year_id` = ' . $clean['id'] .'
                                       AND `course_x_cohort`.`course_id` = `course`.`course_id`
                                       AND `course`.`deleted` = 0
                                       AND `course`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $programYearTitle = $this->_getDisplayForProgramYear($poValues[0]);
                    if (false !== $programYearTitle) {
                        $poDisplayValue = $programYearTitle;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    // offering instructor, offering instructor group
                    // default instructor for learning group, default instructor group for learner group
                    // ilm instructor, ilm instructor group
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `offering_instructor`, `session`
                                             WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                               AND `offering_instructor`.`user_id` =  ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `offering_instructor`, `instructor_group_x_user`,
                                                   `session`
                                             WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                               AND `offering`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`, `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `offering`.`session_id` = `session`.`session_id`
                                               AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`,
                                                   `instructor_group_x_user`, `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `offering`.`session_id` = `session`.`session_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_instructor`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id`
                                                                                 = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                                   `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                          ) `session_courses`
                                      WHERE `course`.`course_id` = `session_courses`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $userName = $this->user->getFormattedUserName($poValues[0], true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    // offering instructor group, default instructor group for learner group, ilm instructor group
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `session`, `offering_instructor`
                                             WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                               AND `offering_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `offering`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`,
                                                   `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `offering`.`session_id` = `session`.`session_id`
                                               AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_instructor`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                           UNION
                                             SELECT DISTINCT `session`.`course_id`
                                              FROM `ilm_session_facet_learner`,`session`,
                                                   `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = false
                                          ) `session_courses`
                                      WHERE `course`.`course_id` = `session_courses`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValues[0]);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    // Need to retrieve learning material for course and sessions of this course
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT `course_id`
                                              FROM `course_learning_material`
                                             WHERE `learning_material_id` = ' . $clean['id'] . '
                                           UNION
                                            SELECT `session`.`course_id`
                                              FROM `session_learning_material`, `session`
                                             WHERE `learning_material_id` = ' . $clean['id'] . '
                                               AND `session`.`session_id` = `session_learning_material`.`session_id`
                                               AND `session`.`deleted` = false
                                          ) `clm_courses`
                                     WHERE `course`.`course_id` = `clm_courses`.`course_id`
                                       AND `course`.`deleted` = 0
                                       AND `course`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_COMPETENCY :
                    //infers courses based on objective hierarchy
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT DISTINCT `cxo`.`course_id`
                                              FROM `competency` `com`,
                                                   `objective` `po`,
                                                   `objective_x_objective` `oxo`,
                                                   `objective` `o`,
                                                   `course_x_objective` `cxo`
                                             WHERE `po`.`competency_id` = `com`.`competency_id`
                                               AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                               AND `o`.`objective_id` = `oxo`.`objective_id`
                                               AND `cxo`.`objective_id` = `o`.`objective_id`
                                               AND `com`.`competency_id` = ' . $clean['id'] . '
                                           UNION
                                            SELECT DISTINCT `cxo`.`course_id`
                                              FROM `competency` `com`,
                                                   `competency` `com2`,
                                                   `objective` `po`,
                                                   `objective_x_objective` `oxo`,
                                                   `objective` `o`,
                                                   `course_x_objective` `cxo`
                                             WHERE `po`.`competency_id` = `com`.`competency_id`
                                               AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                               AND `o`.`objective_id` = `oxo`.`objective_id`
                                               AND `cxo`.`objective_id` = `o`.`objective_id`
                                               AND `com`.`parent_competency_id` = `com2`.`competency_id`
                                               AND `com2`.`competency_id` = ' . $clean['id'] . '
                                          ) `comp_courses`
                                      WHERE `course`.`course_id` = `comp_courses`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';

                    $competencyRow = $this->competency->getRowForPrimaryKeyId($poValues[0]);
                    if ($competencyRow) {
                        $poDisplayValue = $competencyRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    // retrieve courses with sessions associated with this discipline as well
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT `course_id`
                                              FROM `course_x_discipline`
                                             WHERE `discipline_id` = ' . $clean['id'] . '
                                           UNION
                                            SELECT distinct `s`.`course_id`
                                              FROM `session_x_discipline` `sxd`, `session` `s`
                                             WHERE `s`.`session_id` = sxd.`session_id`
                                               AND `s`.`deleted` = false
                                               AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                          ) `disp_courses`
                                      WHERE `course`.`course_id` = `disp_courses`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    //mesh terms assoc with this course, course learning material, course objective,
                    //sessions of this course, session learning material, session objective
                    $queryString = 'SELECT `course`.`course_id`
                                      FROM `course`,
                                          ( SELECT `course_id`
                                              FROM `course_x_mesh`
                                             WHERE `mesh_descriptor_uid` = ' . $clean['id'] . '
                                           UNION
                                            SELECT `course_learning_material`.`course_id`
                                              FROM `course_learning_material_x_mesh`,
                                                   `course_learning_material`
                                             WHERE `course_learning_material_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `course_learning_material_x_mesh`.`course_learning_material_id`
                                                               = `course_learning_material`.`course_learning_material_id`
                                           UNION
                                            SELECT `course_x_objective`.`course_id`
                                              FROM `course_x_objective`,
                                                   `objective_x_mesh`
                                             WHERE `objective_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `objective_x_mesh`.`objective_id` = `course_x_objective`.`objective_id`
                                           UNION
                                            SELECT `session`.`course_id`
                                              FROM `session`, `session_x_mesh`
                                             WHERE `session_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `session_x_mesh`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT `session`.`course_id`
                                              FROM `session`,
                                                   `session_learning_material_x_mesh`,
                                                   `session_learning_material`
                                             WHERE `session_learning_material_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `session_learning_material_x_mesh`.`session_learning_material_id`
                                                             = `session_learning_material`.`session_learning_material_id`
                                               AND `session_learning_material`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                           UNION
                                            SELECT `session`.`course_id`
                                              FROM `session`,
                                                   `session_x_objective`,
                                                   `objective_x_mesh`
                                             WHERE `objective_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `objective_x_mesh`.`objective_id` = `session_x_objective`.`objective_id`
                                               AND `session_x_objective`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                          ) `mesh_courses`
                                      WHERE `course`.`course_id` = `mesh_courses`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`';
                    $meshRow = $this->mesh->getRowForPrimaryKeyId($poValues[0]);
                    if ($meshRow) {
                        $poDisplayValue = $meshRow->name;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }
        }

        if (! is_null($queryResults)) {
            foreach ($queryResults->result_array() as $row) {
                $linkObject = array();

                $courseRow = $this->course->getRowForPrimaryKeyId($row['course_id']);
                $start_date = new DateTime($courseRow->start_date);
                $end_date = new DateTime($courseRow->end_date);
                $year = $courseRow->year;
                $year1 = $year + 1;

                if (! is_null($courseRow)) {
                    if ($courseRow->external_id != null) {
                        $linkObject['text'] = $courseRow->title
                            . '  [' . $courseRow->external_id . ']'
                            . ' ' . $year . ' - ' . $year1
                            . '  (' . $start_date->format('m/d/Y') . ' - '. $end_date->format('m/d/Y') . ')';
                    } else {
                        $linkObject['text'] = $courseRow->title
                            . ' ' . $year . ' - ' . $year1
                            . '  (' . $start_date->format('m/d/Y') . ' - '. $end_date->format('m/d/Y') . ')';
                    }
                    $linkObject['link'] = base_url() . 'ilios.php/course_management'
                        . '?course_id=' . $row['course_id'];

                    array_push($rhett, $linkObject);
                }
            }
        }

        return $rhett;
    }

    /**
     * Runs a session-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array a nested array of arrays. Each sub-array contains the session title and admin link.
     */
    protected function handleReportForSession ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryString = '';
        $queryResults = null;

        if (is_null($poValues)) {

            $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                   `course`.`start_date`, `course`.`end_date`,
                                   `session`.`title` AS  `session_title`, `session`.`session_id`
                              FROM `course`, `session`
                             WHERE `course`.`course_id` = `session`.`course_id`
                               AND `course`.`deleted` = 0
                               AND `session`.`deleted` = 0
                               AND `course`.`owning_school_id` = ' . $schoolId . '
                          ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`, `session`.`title`';
        } else {
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] = $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `session`.`title` AS  `session_title`, `session`.`session_id`
                                      FROM `course`, `session`
                                     WHERE `course`.`course_id` = `session`.`course_id`
                                       AND `course`.`deleted` = 0
                                       AND `session`.`deleted` = 0
                                       AND `course`.`course_id` = ' . $clean['id'] .'
                                       AND `course`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`, `session`.`title`';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `program_session`.`title` AS  `session_title`,
                                           `program_session`.`session_id`
                                      FROM `course`,
                                           (SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `session`, `course_x_cohort`, `cohort`, `program_year`
                                             WHERE `session`.`course_id` = `course_x_cohort`.`course_id`
                                               AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                               AND `cohort`.`program_year_id` = `program_year`.`program_year_id`
                                               AND `session`.`deleted` = 0
                                               AND `program_year`.`program_id` = ' . $clean['id'] . '
                                           ) program_session
                                      WHERE `course`.`course_id` = `program_session`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                            `program_session`.`title`';
                    $programRow = $this->program->getRowForPrimaryKeyId($poValues[0]);
                    if ($programRow) {
                        $poDisplayValue = $programRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM_YEAR :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `program_year_session`.`title` AS  `session_title`,
                                           `program_year_session`.`session_id`
                                      FROM `course`,
                                           (SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `session`, `course_x_cohort`, `cohort`
                                             WHERE `session`.`course_id` = `course_x_cohort`.`course_id`
                                               AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                               AND `cohort`.`program_year_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                            ) program_year_session
                                      WHERE `course`.`course_id` = `program_year_session`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                            `program_year_session`.`title`';
                    $programYearTitle = $this->_getDisplayForProgramYear($poValues[0]);
                    if (false !== $programYearTitle) {
                        $poDisplayValue = $programYearTitle;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `instructor_session`.`title` AS  `session_title`,
                                           `instructor_session`.`session_id`
                                      FROM `course`,
                                           (SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `offering_instructor`, `session`
                                             WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                               AND `offering_instructor`.`user_id` =  ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`
                                             WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`, `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`,
                                                   `instructor_group_x_user`, `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_instructor`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                                   `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                               AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                            ) instructor_session
                                      WHERE `course`.`course_id` = `instructor_session`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                            `instructor_session`.`title`';
                    $userName = $this->user->getFormattedUserName($poValues[0], true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `instr_grp_session`.`title` AS  `session_title`,
                                           `instr_grp_session`.`session_id`
                                      FROM `course`,
                                           (SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `session`, `offering_instructor`
                                             WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                               AND `offering_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `offering`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `offering`, `offering_learner`, `group_default_instructor`,
                                                   `session`
                                             WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                               AND `offering_learner`.`group_id` = `group_default_instructor`.group_id
                                               AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`session_id` = `offering`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_instructor`, `session`
                                             WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_instructor`.`instructor_group_id` = ' . $clean['id'] .'
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT DISTINCT `session`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `ilm_session_facet_learner`, `session`,
                                                   `group_default_instructor`
                                             WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                 = `session`.`ilm_session_facet_id`
                                               AND `ilm_session_facet_learner`.`group_id`
                                                                                 = `group_default_instructor`.`group_id`
                                               AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                               AND `session`.`deleted` = 0
                                            ) instr_grp_session
                                        WHERE `course`.`course_id` = `instr_grp_session`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                            `instr_grp_session`.`title`';
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValues[0]);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `session`.`title` AS  `session_title`,
                                           `session`.`session_id`
                                      FROM `session_learning_material`, `session`, `course`
                                     WHERE `session_learning_material`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session_learning_material`.`session_id` = `session`.`session_id`
                                       AND `course`.`course_id` = `session`.`course_id`
                                       AND `session`.`deleted` = 0
                                       AND `course`.`deleted` = 0
                                       AND `course`.`owning_school_id` = ' . $schoolId;
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_COMPETENCY :
                    //infers courses based on objective hierarchy
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `competency_session`.`title` AS  `session_title`,
                                           `competency_session`.`session_id`
                                      FROM `course`,
                                           (SELECT distinct `sxo`.`session_id`, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `competency` `com`,
                                                   `objective` `po`,
                                                   `objective_x_objective` `oxo`,
                                                   `objective` `o`,
                                                   `objective_x_objective` `coxo`,
                                                   `objective` `co`,
                                                   `session_x_objective` `sxo`,
                                                   `session`
                                             WHERE `po`.`competency_id` = `com`.`competency_id`
                                               AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                               AND `o`.`objective_id` = `oxo`.`objective_id`
                                               AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                               AND `coxo`.`objective_id` = `co`.`objective_id`
                                               AND `co`.`objective_id` = `sxo`.`objective_id`
                                               AND `com`.`competency_id` = ' . $clean['id'] . '
                                               AND `sxo`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = 0
                                           UNION
                                            SELECT distinct `sxo`.session_id, `session`.`course_id`,
                                                   `session`.`title`
                                              FROM `competency` `com`,
                                                   `competency` `com2`,
                                                   `objective` `po`,
                                                   `objective_x_objective` `oxo`,
                                                   `objective` `o`,
                                                   `objective_x_objective` `coxo`,
                                                   `objective` `co`,
                                                   `session_x_objective` `sxo`,
                                                   `session`
                                             WHERE `po`.`competency_id` = `com`.`competency_id`
                                               AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                               AND `o`.`objective_id` = `oxo`.`objective_id`
                                               AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                               AND `coxo`.`objective_id` = `co`.`objective_id`
                                               AND `co`.`objective_id` = `sxo`.`objective_id`
                                               AND `com`.`parent_competency_id` = `com2`.`competency_id`
                                               AND `com2`.`competency_id` = ' . $clean['id'] . '
                                               AND `sxo`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = 0
                                            ) competency_session
                                      WHERE `course`.`course_id` = `competency_session`.`course_id`
                                        AND `course`.`deleted` = 0
                                        AND `course`.`owning_school_id` = ' . $schoolId . '
                                   ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                            `competency_session`.`title`';
                    $competencyRow = $this->competency->getRowForPrimaryKeyId($poValues[0]);
                    if ($competencyRow) {
                        $poDisplayValue = $competencyRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $queryString = 'SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
                                           `course`.`start_date`, `course`.`end_date`,
                                           `session`.`title` AS  `session_title`,
                                           `session`.`session_id`
                                     FROM `session_x_discipline`, `session`, `course`
                                    WHERE `discipline_id` = ' . $clean['id'] . '
                                      AND `session_x_discipline`.`session_id` = `session`.`session_id`
                                      AND `course`.`course_id` = `session`.`course_id`
                                      AND `session`.`deleted` = 0
                                      AND `course`.`deleted` = 0
                                      AND `course`.`owning_school_id` = ' . $schoolId . '
                                 ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`,
                                          `session`.`title`';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    $queryString =<<<EOL
 SELECT `course`.`course_id`, `course`.`title` AS `course_title`,
        `course`.`start_date`, `course`.`end_date`,
        `mesh_session`.`title` AS  `session_title`,
        `mesh_session`.`session_id`
   FROM `course`,
        (SELECT `session_x_mesh`.`session_id`, `session`.`course_id`, `session`.`title`
           FROM `session_x_mesh`, `session`
          WHERE `session_x_mesh`.`mesh_descriptor_uid` = {$clean['id']}
            AND `session_x_mesh`.`session_id` = `session`.`session_id`
            AND `session`.`deleted` = 0

        UNION

         SELECT `session_learning_material`.`session_id`, `session`.`course_id`, `session`.`title`
           FROM `session_learning_material`, `session_learning_material_x_mesh`, `session`
          WHERE `session_learning_material_x_mesh`.`session_learning_material_id`
                                         = `session_learning_material`.`session_learning_material_id`
            AND `session_learning_material_x_mesh`.`mesh_descriptor_uid` = {$clean['id']}
            AND `session_learning_material`.`session_id` = `session`.`session_id`
            AND `session`.`deleted` = 0

        UNION

         SELECT `session_x_objective`.`session_id`, `session`.`course_id`, `session`.`title`
           FROM `session_x_objective`, `objective_x_mesh`, `session`
          WHERE `objective_x_mesh`.`objective_id` = `session_x_objective`.`objective_id`
            AND `objective_x_mesh`.`mesh_descriptor_uid` = {$clean['id']}
            AND `session_x_objective`.`session_id` = `session`.`session_id`
            AND `session`.`deleted` = 0
         ) mesh_session
   WHERE `course`.`course_id` = `mesh_session`.`course_id`
     AND `course`.`deleted` = 0
     AND `course`.`owning_school_id` = {$schoolId}
ORDER BY `course`.`title`, `course`.`start_date`, `course`.`end_date`, `mesh_session`.`title`
EOL;
                    $meshRow = $this->mesh->getRowForPrimaryKeyId($poValues[0]);
                    if ($meshRow) {
                        $poDisplayValue = $meshRow->name;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            } // end switch
        }
        if (strlen($queryString) > 0) {
            $queryResults = $DB->query($queryString);
        }


        if (! is_null($queryResults)) {
            foreach ($queryResults->result_array() as $row) {
                $linkObject = array();

                $startDate = new DateTime($row['start_date']);
                $startDate = date_format($startDate, 'm/d/Y');
                $endDate = new DateTime($row['end_date']);
                $endDate = date_format($endDate, 'm/d/Y');

                $linkObject['text'] = $row['course_title'] . ' (' . $startDate . ' - ' . $endDate.') ' .' - ' . $row['session_title'];
                $linkObject['link'] = base_url() . 'ilios.php/course_management'
                    . '?course_id=' . $row['course_id']
                    . '&session_id=' . $row['session_id'];

                array_push($rhett, $linkObject);
            }
        }

        return $rhett;
    }

    /**
     * Runs a program-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array a nested array of arrays. Each sub-array contains the program title and admin link.
     */
    protected function handleReportForProgram ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryResults = null;

        if (is_null($poValues)) {
            $DB->where('deleted', 0);
            $DB->where('owning_school_id', $schoolId);
            $DB->order_by('title');

            $queryResults = $DB->get($this->program->getTableName());
        }  else {
            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] = $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT DISTINCT `program_year`.`program_id`, `program`.`title`
                                      FROM `course_x_cohort`, `cohort`, `program_year`, `program`
                                     WHERE `course_x_cohort`.`course_id` = ' . $clean['id'] . '
                                       AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `program_year`.`program_year_id` = `cohort`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT DISTINCT `program_year`.`program_id`, `program`.`title`
                                      FROM `session`, `course_x_cohort`, `cohort`, `program_year`, `program`
                                     WHERE `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`course_id` = `course_x_cohort`.`course_id`
                                       AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `program_year`.`program_year_id` = `cohort`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $queryString = 'SELECT DISTINCT `program_year`.`program_id`, `program`.`title`
                                      FROM `program_year_x_discipline`, `program_year`, `program`
                                     WHERE `program_year_x_discipline`.`discipline_id` = ' . $clean['id'] . '
                                       AND `program_year`.`program_year_id` = `program_year_x_discipline`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }
        }

        if (! is_null($queryResults)) {
            foreach ($queryResults->result_array() as $row) {
                $linkObject = array();

                $linkObject['text'] = $row['title'];
                $linkObject['link'] = base_url() . 'ilios.php/program_management'
                    . '?program_id=' . $row['program_id'];

                array_push($rhett, $linkObject);

            }
        }

        return $rhett;
    }

    /**
     * Runs a program-year-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array a nested array of arrays. Each sub-array contains the program title and admin link.
     */
    protected function handleReportForProgramYear ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryString = '';
        $queryResults = null;

        if (is_null($poValues)) {
            $queryString = 'SELECT `program`.`program_id`,`program_year`.`program_year_id`
                              FROM `program`, `program_year`
                             WHERE `program`.`program_id` = `program_year`.`program_id`
                               AND `program_year`.`deleted` = 0
                               AND `program`.`deleted` = 0
                               AND `program`.`owning_school_id` = ' . $schoolId . '
                          ORDER BY `program`.`title`, `program_year`.`start_year`';
        } else {
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] = $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT `program`.`program_id`,`program_year`.`program_year_id`
                                      FROM `course_x_cohort`, `cohort`, `program_year`, `program`
                                     WHERE `course_x_cohort`.`course_id` = ' . $clean['id'] . '
                                       AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `program_year`.`program_year_id` = `cohort`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program_year`.`deleted` = 0
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`, `program_year`.`start_year`';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT `program`.`program_id`,`program_year`.`program_year_id`
                                      FROM `session`, `course_x_cohort`, `cohort`, `program_year`, `program`
                                     WHERE `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`course_id` = `course_x_cohort`.`course_id`
                                       AND `course_x_cohort`.`cohort_id` = `cohort`.`cohort_id`
                                       AND `program_year`.`program_year_id` = `cohort`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program_year`.`deleted` = 0
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`, `program_year`.`start_year`';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $queryString = 'SELECT `program`.`program_id`,`program_year`.`program_year_id`
                                      FROM `program_year_x_discipline`, `program_year`, `program`
                                     WHERE `program_year_x_discipline`.`discipline_id` = ' . $clean['id'] . '
                                       AND `program_year`.`program_year_id` = `program_year_x_discipline`.`program_year_id`
                                       AND `program`.`program_id` = `program_year`.`program_id`
                                       AND `program_year`.`deleted` = 0
                                       AND `program`.`deleted` = 0
                                       AND `program`.`owning_school_id` = ' . $schoolId . '
                                  ORDER BY `program`.`title`, `program_year`.`start_year`';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }
        }
        if (strlen($queryString) > 0) {
            $queryResults = $DB->query($queryString);
        }

        if (! is_null($queryResults)) {
            foreach ($queryResults->result_array() as $row) {
                $linkObject = array();
                $programYearTitle = $this->_getDisplayForProgramYear($row['program_year_id']);
                if (false !== $programYearTitle) {
                    $linkObject['text'] = $programYearTitle;
                    $linkObject['link'] = base_url() . 'ilios.php/program_management' . '?program_id=' . $row['program_id'];
                }
                array_push($rhett, $linkObject);
            }
        }

        return $rhett;
    }

    /**
     * Runs instructor-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array an array of instructor titles
     */
    protected function handleReportForInstructor ($reportRow, $poValues, &$poDisplayValue)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        if (! is_null($poValues)) {

            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] = $DB->escape($poValues[0]);

            $queryResults = null;

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString ='SELECT DISTINCT `offering_instructor`.`user_id`
                                      FROM `offering`, `offering_instructor`, `session`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`deleted` = false
                                       AND `offering_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `ilm_session_facet_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`course_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`user_id`
                                      FROM `offering`, `offering_instructor`, `session`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`deleted` = false
                                       AND `offering_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `ilm_session_facet_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $queryString = 'SELECT `user_id`
                                    FROM `instructor_group_x_user`
                                    WHERE `instructor_group_id` = ' . $clean['id'];
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValues[0]);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`user_id`
                                      FROM `offering`, `offering_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `offering_instructor`.`user_id` is not null
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`, `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `session`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `ilm_session_facet_instructor`.`user_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                   UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`user_id`
                                      FROM `offering`, `offering_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `offering_instructor`.`user_id` is not null
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`, `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `session`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `ilm_session_facet_instructor`.`user_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                   UNION
                                    SELECT DISTINCT `group_default_instructor`.`user_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                       AND `group_default_instructor`.`user_id` IS NOT NULL
                                  UNION
                                    SELECT DISTINCT `instructor_group_x_user`.`user_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }

            if (! is_null($queryResults)) {
                foreach ($queryResults->result_array() as $row) {
                    $userName = $this->user->getFormattedUserName($row['user_id'], true);
                    if (false !== $userName) {
                        $rhett[] = $userName;
                    }
                }
            }
            usort($rhett, 'strcasecmp');
        }

        return $rhett;
    }

    /**
     * Runs instructor-group-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array an array of instructor-group titles
     */
    protected function handleReportForInstructorGroup ($reportRow, $poValues, &$poDisplayValue)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryResults = null;

        if (is_null($poValues)) {
            $queryResults = $DB->get($this->instructorGroup->getTableName());
        } else {
            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] =  $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`instructor_group_id`
                                      FROM `offering`, `session`, `offering_instructor`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`course_id` = ' . $clean['id']  . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`course_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_instructor`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`course_id` = ' . $clean['id']  . '
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`course_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`instructor_group_id`
                                      FROM `offering`, `session`, `offering_instructor`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = ' . $clean['id']  . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_instructor`, `session`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = ' . $clean['id']  . '
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $queryString = 'SELECT `instructor_group_id`
                                    FROM `instructor_group_x_user`
                                    WHERE `user_id` = ' . $clean['id'];
                    $userName = $this->user->getFormattedUserName($poValues[0], true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`instructor_group_id`
                                      FROM `offering`, `session`, `offering_instructor`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id']  . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`, `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id']  . '
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `slm`.`learning_material_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false';
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_TOPIC :
                    $queryString = 'SELECT DISTINCT `offering_instructor`.`instructor_group_id`
                                      FROM `offering`, `session`, `offering_instructor`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id']  . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`, `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `ilm_session_facet_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id']  . '
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `group_default_instructor`.`instructor_group_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `sxd`.`discipline_id` = ' . $clean['id']  . '
                                       AND `group_default_instructor`.`instructor_group_id` IS NOT NULL
                                       AND `session`.`deleted` = false';
                    $disciplineRow = $this->discipline->getRowForPrimaryKeyId($poValues[0]);
                    if ($disciplineRow) {
                        $poDisplayValue = $disciplineRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }
        }

        if (! is_null($queryResults)) {
            foreach ($queryResults->result_array() as $row) {
                $instructorGroupRow
                       = $this->instructorGroup->getRowForPrimaryKeyId($row['instructor_group_id']);

                array_push($rhett, $instructorGroupRow->title);
            }
            usort($rhett, 'strcasecmp');
        }

        return $rhett;
    }

    /**
     * Runs competency-report for a given report record and and filter values in the context of a given school.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @param int $schoolId the school id
     * @return array an array of competency titles
     */
    protected function handleReportForCompetency ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;
        $schoolId = $this->session->userdata('school_id');

        $queryResults = null;
        $queryString = '';

        if (is_null($poValues)) {

            $competencyTree = $this->competency->getCompetencyTree($schoolId);
            return $this->_printCompetencyTree( $competencyTree );
        } else {
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] =  $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = '(SELECT DISTINCT `com`.`competency_id`, `com`.`parent_competency_id`
                                      FROM `competency` `com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `course_x_objective` `cxo`
                                      WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `cxo`.`objective_id` = `o`.`objective_id`
                                       AND `cxo`.`course_id` = ' . $clean['id'] . '
                                       AND `com`.`owning_school_id` = ' . $schoolId . '
                                       AND `com`.`parent_competency_id` IS NULL
                                      ORDER BY `com`.`title`) ';
                    $queryString .= 'UNION ';
                    $queryString .= '(SELECT DISTINCT `com`.`competency_id`, `com`.`parent_competency_id`
                                      FROM `competency` `com`,
                                           `competency` `parent_com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `course_x_objective` `cxo`
                                      WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `cxo`.`objective_id` = `o`.`objective_id`
                                       AND `cxo`.`course_id` = ' . $clean['id'] . '
                                       AND `com`.`owning_school_id` = ' . $schoolId . '
                                       AND `com`.`parent_competency_id` = `parent_com`.`competency_id`
                                      ORDER BY `parent_com`.`title`, `com`.`title`)';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = '(SELECT distinct `com`.`competency_id`, `com`.`parent_competency_id`
                                      FROM `competency` `com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `objective_x_objective` `coxo`,
                                           `objective` `co`,
                                           `session_x_objective` `sxo`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                       AND `coxo`.`objective_id` = `co`.`objective_id`
                                       AND `co`.`objective_id` = `sxo`.`objective_id`
                                       AND `sxo`.`session_id` = ' . $clean['id'] . '
                                       AND `com`.`owning_school_id` = ' . $schoolId . '
                                       AND `com`.`parent_competency_id` IS NULL
                                  ORDER BY `com`.`title`) ';
                    $queryString .= 'UNION ';
                    $queryString .= '(SELECT distinct `com`.`competency_id`, `com`.`parent_competency_id`
                                      FROM `competency` `com`,
                                           `competency` `parent_com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `objective_x_objective` `coxo`,
                                           `objective` `co`,
                                           `session_x_objective` `sxo`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                       AND `coxo`.`objective_id` = `co`.`objective_id`
                                       AND `co`.`objective_id` = `sxo`.`objective_id`
                                       AND `sxo`.`session_id` = ' . $clean['id'] . '
                                       AND `com`.`owning_school_id` = ' . $schoolId . '
                                       AND `com`.`parent_competency_id` = `parent_com`.`competency_id`
                                  ORDER BY `parent_com`.`title`, `com`.`title`)';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Cannot match prepositional object - " . $po . ", contact developer.";
            }
        }

        if (strlen($queryString) > 0) {
            $queryResults = $DB->query($queryString);
        }


        if (! is_null($queryResults)) {
            $parentCompetencyId = null;
            $parentCompetencyRow = null;
            $listitems = '';
            foreach ($queryResults->result_array() as $row) {
                $competencyRow = $this->competency->getRowForPrimaryKeyId($row['competency_id']);
                if (null == $row['parent_competency_id']) {
                    array_push($rhett, $competencyRow->title);
                } else {
                    if ($parentCompetencyId != $row['parent_competency_id']) {
                        if (!empty($listitems)) {
                            array_push($rhett, $listitems . '</ul>');
                        }
                        $parentCompetencyId = $row['parent_competency_id'];
                        $parentCompetencyRow = $this->competency->getRowForPrimaryKeyId($row['parent_competency_id']);
                        $listitems = $parentCompetencyRow->title . '<ul>';
                    }
                    $listitems .= '<li>' . $competencyRow->title . '</li>';
                }
            }
            if (!empty($listitems)) {
                array_push($rhett, $listitems . '</ul>');
            }
        }

        return $rhett;
    }

    /**
     * Runs topic-report for a given report record and and filter values in the context of a given school.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @param int $schoolId the school id
     * @return array an array of discipline titles
     */
    protected function handleReportForTopic ($reportRow, $poValues, &$poDisplayValue, $schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryString = '';
        $queryResults = null;

        if (is_null($poValues)) {
            $queryResults = $DB->get($this->discipline->getTableName());
        } else {
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] =  $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT `discipline_id`
                                      FROM `course_x_discipline`
                                     WHERE `course_id` = ' . $clean['id'] . '
                                  UNION
                                    SELECT `sxd`.`discipline_id`
                                      FROM `session_x_discipline` `sxd`, `session` `s`
                                     WHERE `s`.`session_id` = sxd.`session_id`
                                       AND `s`.`deleted` = false
                                       AND `s`.`course_id` = ' . $clean['id'];
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT `sxd`.`discipline_id`
                                    FROM `session_x_discipline` `sxd`, `session` `s`
                                    WHERE `s`.`session_id` = sxd.`session_id`
                                      AND `s`.`deleted` = false
                                      AND `s`.`session_id` = ' . $clean['id'];
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM :
                    $queryString = 'SELECT DISTINCT `program_year_x_discipline`.`discipline_id`
                                      FROM `program_year`, `program_year_x_discipline`
                                     WHERE `program_year`.`program_id` = ' . $clean['id'] . '
                                       AND `program_year_x_discipline`.`program_year_id`
                                                            = `program_year`.`program_year_id`';
                    $programRow = $this->program->getRowForPrimaryKeyId($poValues[0]);
                    if ($programRow) {
                        $poDisplayValue = $programRow->title;
                    }
                    break;
                case self::REPORT_NOUN_PROGRAM_YEAR :
                    $queryString = 'SELECT `discipline_id`
                                      FROM `program_year_x_discipline`
                                     WHERE `program_year_id` = ' . $clean['id'];
                    $programYearTitle = $this->_getDisplayForProgramYear($poValues[0]);
                    if (false !== $programYearTitle) {
                        $poDisplayValue = $programYearTitle;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $queryString = 'SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `offering_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `offering_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`, `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_instructor`, `session`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `ilm_session_facet_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                   UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $userName = $this->user->getFormattedUserName($poValues[0], true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $queryString = 'SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `session`, `offering_instructor`,
                                           `session_x_discipline` `sxd`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`, `session_x_discipline` `sxd`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_instructor`, `session`,
                                           `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `sxd`.`discipline_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`, `session_x_discipline` `sxd`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `sxd`.`session_id`
                                       AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValues[0]);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $queryString = 'SELECT DISTINCT `course_x_discipline`.`discipline_id`
				                      FROM `course_x_discipline`, `course_learning_material`, `course`
				                     WHERE `course_learning_material`.`learning_material_id` = ' . $clean['id'] . '
				                       AND `course_learning_material`.`course_id` = `course_x_discipline`.`course_id`
				                       AND `course_learning_material`.`course_id` = `course`.`course_id`
				                       AND `course`.`deleted` = false
                                  UNION
			                        SELECT DISTINCT `session_x_discipline`.`discipline_id`
				                      FROM `session_x_discipline`, `session_learning_material`, `session`
				                     WHERE `session_learning_material`.`learning_material_id` = ' . $clean['id'] . '
				                       AND `session_learning_material`.`session_id` = `session_x_discipline`.`session_id`
				                       AND `session_learning_material`.`session_id` = `session`.`session_id`
				                       AND `session`.`deleted` = false';
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                case self::REPORT_NOUN_COMPETENCY :
                    $queryString = 'SELECT DISTINCT `cxd`.`discipline_id`
                                      FROM `competency` `com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `course_x_objective` `cxo`,
                                           `course_x_discipline` `cxd`,
                                           `course`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `cxo`.`objective_id` = `o`.`objective_id`
                                       AND `com`.`competency_id` = ' . $clean['id'] . '
                                       AND `cxo`.`course_id` = `cxd`.`course_id`
                                       AND `cxo`.`course_id` = `course`.`course_id`
                                       AND `course`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `cxd`.`discipline_id`
                                      FROM `competency` `com`,
                                           `competency` `com2`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `course_x_objective` `cxo`,
                                           `course_x_discipline` `cxd`,
                                           `course`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `cxo`.`objective_id` = `o`.`objective_id`
                                       AND `cxo`.`course_id` = `cxd`.`course_id`
                                       AND `cxo`.`course_id` = `course`.`course_id`
                                       AND `course`.`deleted` = false
                                       AND `com`.`parent_competency_id` = `com2`.`competency_id`
                                       AND `com2`.`competency_id` = ' . $clean['id'] . '
                                  UNION
                                    SELECT distinct `sxd`.discipline_id
                                      FROM `competency` `com`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `objective_x_objective` `coxo`,
                                           `objective` `co`,
                                           `session_x_objective` `sxo`,
                                           `session_x_discipline` `sxd`,
                                           `session`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                       AND `coxo`.`objective_id` = `co`.`objective_id`
                                       AND `co`.`objective_id` = `sxo`.`objective_id`
                                       AND `com`.`competency_id` = ' . $clean['id'] . '
                                       AND `sxo`.`session_id` = `sxd`.`session_id`
                                       AND `sxo`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT distinct `sxd`.discipline_id
                                      FROM `competency` `com`,
                                           `competency` `com2`,
                                           `objective` `po`,
                                           `objective_x_objective` `oxo`,
                                           `objective` `o`,
                                           `objective_x_objective` `coxo`,
                                           `objective` `co`,
                                           `session_x_objective` `sxo`,
                                           `session_x_discipline` `sxd`,
                                           `session`
                                     WHERE `po`.`competency_id` = `com`.`competency_id`
                                       AND `oxo`.`parent_objective_id` = `po`.`objective_id`
                                       AND `o`.`objective_id` = `oxo`.`objective_id`
                                       AND `o`.`objective_id` = `coxo`.`parent_objective_id`
                                       AND `coxo`.`objective_id` = `co`.`objective_id`
                                       AND `co`.`objective_id` = `sxo`.`objective_id`
                                       AND `sxo`.`session_id` = `sxd`.`session_id`
                                       AND `sxo`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                       AND `com`.`parent_competency_id` = `com2`.`competency_id`
                                       AND `com2`.`competency_id` = ' . $clean['id'];
                    $competencyRow = $this->competency->getRowForPrimaryKeyId($poValues[0]);
                    if ($competencyRow) {
                        $poDisplayValue = $competencyRow->title;
                    }
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    $queryString = 'SELECT `course_x_discipline`.`discipline_id`
                                     FROM (SELECT `course`.`course_id`
                                             FROM `course`, `course_x_mesh`
                                            WHERE `course_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                              AND `course_x_mesh`.`course_id` = `course`.`course_id`
                                              AND `course`.`deleted` = false
                                         UNION
                                           SELECT `course`.`course_id`
                                             FROM `course`, `course_learning_material_x_mesh`,`course_learning_material`
                                            WHERE `course_learning_material_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                              AND `course_learning_material_x_mesh`.`course_learning_material_id`
                                                              = `course_learning_material`.`course_learning_material_id`
                                              AND `course_learning_material`.`course_id` = `course`.`course_id`
                                              AND `course`.`deleted` = false
                                         UNION
                                           SELECT `course`.`course_id`
                                             FROM `course`, `course_x_objective`, `objective_x_mesh`
                                            WHERE `objective_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id']  . '
                                              AND `objective_x_mesh`.`objective_id` = `course_x_objective`.`objective_id`
                                              AND `course_x_objective`.`course_id` = `course`.`course_id`
                                              AND `course`.`deleted` = false
                                          ) AS `mesh_course`,
                                          `course_x_discipline`
                                     WHERE `mesh_course`.`course_id` = `course_x_discipline`.`course_id`
                                  UNION
                                    SELECT `session_x_discipline`.`discipline_id`
                                      FROM (SELECT `session`.`session_id`
                                              FROM `session`, `session_x_mesh`
                                             WHERE `session_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id']  . '
                                               AND `session_x_mesh`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                          UNION
                                            SELECT `session`.`session_id`
                                              FROM `session`, `session_learning_material_x_mesh`,`session_learning_material`
                                             WHERE `session_learning_material_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `session_learning_material_x_mesh`.`session_learning_material_id`
                                                              = `session_learning_material`.`session_learning_material_id`
                                               AND `session_learning_material`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                          UNION
                                            SELECT `session`.`session_id`
                                              FROM `session`, `session_x_objective`, `objective_x_mesh`
                                             WHERE `objective_x_mesh`.`mesh_descriptor_uid` = ' . $clean['id'] . '
                                               AND `objective_x_mesh`.`objective_id` = `session_x_objective`.`objective_id`
                                               AND `session_x_objective`.`session_id` = `session`.`session_id`
                                               AND `session`.`deleted` = false
                                           ) AS `mesh_session`,
                                           `session_x_discipline`
                                     WHERE `mesh_session`.`session_id` = `session_x_discipline`.`session_id`;';
                    $meshRow = $this->mesh->getRowForPrimaryKeyId($poValues[0]);
                    if ($meshRow) {
                        $poDisplayValue = $meshRow->name;
                    }
                    break;
                default:
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }
        }

        if (strlen($queryString) > 0) {
            $queryResults = $DB->query($queryString);
        }


        if (! is_null($queryResults)) {
            $idArray = array();

            foreach ($queryResults->result_array() as $row) {
                array_push($idArray, $row['discipline_id']);
            }

            $this->reallyFreeQueryResults($queryResults);

            foreach ($idArray as $id) {
                $disciplineRow = $this->discipline->getRowForPrimaryKeyId($id, false, $schoolId);
                if ($disciplineRow != null) {
                    array_push($rhett, $disciplineRow->title);
                }
            }
            usort($rhett, 'strcasecmp');
        }

        return $rhett;
    }

    /**
     * Runs learning-materials-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array an array of learning-material titles
     */
    protected function handleReportForLearningMaterial ($reportRow, $poValues, &$poDisplayValue)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryResults = null;

        if (is_null($poValues)) {
            $queryResults = $DB->get($this->learningMaterial->getTableName());
        } else {
            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] =  $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT `learning_material_id`
                                    FROM `course_learning_material`
                                    WHERE `course_id` = ' . $clean['id'] .'
                                  UNION
                                    SELECT `learning_material_id`
                                    FROM `session_learning_material`, `session`
                                    WHERE `session_learning_material`.`session_id` = `session`.`session_id`
                                      AND `session`.`course_id` = ' . $clean['id'];
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT learning_material_id
                                    FROM session_learning_material
                                    WHERE session_id = ' . $clean['id'];
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR :
                    $queryString = 'SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `offering_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `offering`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `offering_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `offering_instructor`, `instructor_group_x_user`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering`.`offering_id` = `offering_instructor`.`offering_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `offering_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `instructor_group_x_user`, `session`, `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_instructor`, `session`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `ilm_session_facet_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_instructor`, `instructor_group_x_user`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                   UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_learner`, `session`, `group_default_instructor`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                                      = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `group_default_instructor`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_learner`, `instructor_group_x_user`, `session`,
                                           `group_default_instructor`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `group_default_instructor`.`instructor_group_id`
                                                                       = `instructor_group_x_user`.`instructor_group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `instructor_group_x_user`.`user_id` = ' . $clean['id'] . '
                                       AND `session`.`deleted` = false';
                    $userName = $this->user->getFormattedUserName($poValues[0], true);
                    if (false !== $userName) {
                        $poDisplayValue = $userName;
                    }
                    break;
                case self::REPORT_NOUN_INSTRUCTOR_GROUP :
                    $queryString = 'SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `session`, `offering_instructor`,
                                           `session_learning_material` `slm`
                                     WHERE `offering_instructor`.`offering_id` = `offering`.`offering_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `offering_instructor`.`instructor_group_id` = ' . $clean['id']  . '
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `offering`, `offering_learner`, `group_default_instructor`,
                                           `session`, `session_learning_material` `slm`
                                     WHERE `offering`.`offering_id` = `offering_learner`.`offering_id`
                                       AND `offering_learner`.`group_id` = `group_default_instructor`.`group_id`
                                       AND `offering`.`session_id` = `session`.`session_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id']  . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_instructor`, `session`,
                                           `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_instructor`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `ilm_session_facet_instructor`.`instructor_group_id` = ' . $clean['id']  . '
                                       AND `session`.`deleted` = false
                                  UNION
                                    SELECT DISTINCT `slm`.`learning_material_id`
                                      FROM `ilm_session_facet_learner`, `session`,
                                           `group_default_instructor`, `session_learning_material` `slm`
                                     WHERE `ilm_session_facet_learner`.`ilm_session_facet_id`
                                                                       = `session`.`ilm_session_facet_id`
                                       AND `ilm_session_facet_learner`.`group_id`
                                                                       = `group_default_instructor`.`group_id`
                                       AND `session`.`session_id` = `slm`.`session_id`
                                       AND `group_default_instructor`.`instructor_group_id` = ' . $clean['id']  . '
                                       AND `session`.`deleted` = false';
                    $instructorGroupRow = $this->instructorGroup->getRowForPrimaryKeyId($poValues[0]);
                    if ($instructorGroupRow) {
                        $poDisplayValue = $instructorGroupRow->title;
                    }
                    break;
                case self::REPORT_NOUN_MESH_TERM :
                    $queryString='SELECT `c`.`learning_material_id`
                                    FROM `course_learning_material` `c`, `course_learning_material_x_mesh` `cxm`
                                    WHERE `c`.`course_learning_material_id` = `cxm`.`course_learning_material_id`
                                      AND `cxm`.`mesh_descriptor_uid` = ' . $clean['id'] .'
                                  UNION
                                    SELECT `s`.`learning_material_id`
                                    FROM `session_learning_material` `s`, `session_learning_material_x_mesh` `sxm`
                                    WHERE `s`.`session_learning_material_id` = `sxm`.`session_learning_material_id`
                                      AND `sxm`.`mesh_descriptor_uid` = ' . $clean['id'];
                    $meshRow = $this->mesh->getRowforPrimaryKeyId($poValues[0]);
                    if ($meshRow) {
                        $poDisplayValue = $meshRow->name;
                    }
                    break;
                default:
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }
        }

        if (! is_null($queryResults)) {
            $idArray = array();

            foreach ($queryResults->result_array() as $row) {
                array_push($idArray, $row['learning_material_id']);
            }

            $this->reallyFreeQueryResults($queryResults);

            foreach ($idArray as $id) {
                $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($id);

                array_push($rhett, trim($learningMaterialRow->title));
            }
            usort($rhett, 'strcasecmp');
        }

        return $rhett;
    }

    /**
     * Runs MeSH-term-report for a given report record and and filter values.
     * @param stdClass $reportRow the report record.
     * @param array $poValues the filter values
     * @param string $poDisplayValue the filter value label
     * @return array an array of MeSH-term names
     */
    protected function handleReportForMeSHTerms ($reportRow, $poValues, &$poDisplayValue)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $queryResults = null;

        if (! is_null($poValues)) {
            $queryString = '';
            $po = $reportRow->prepositional_object;

            $clean = array();
            $clean['id'] =  $DB->escape($poValues[0]);

            switch ($po) {
                case self::REPORT_NOUN_COURSE :
                    $queryString = 'SELECT `course_x_mesh`.`mesh_descriptor_uid`
                                      FROM `course_x_mesh`, `course`
                                     WHERE `course`.`course_id` = ' . $clean['id'] .'
                                       AND `course`.`deleted` = FALSE
                                       AND `course`.`course_id` = `course_x_mesh`.`course_id`
                                  UNION
                                    SELECT `course_learning_material_x_mesh`.`mesh_descriptor_uid`
                                      FROM `course_learning_material_x_mesh`,
                                           `course_learning_material`,
                                           `course`
                                     WHERE `course`.`course_id` = ' . $clean['id'] .'
                                       AND `course`.`deleted` = FALSE
                                       AND `course`.`course_id` = `course_learning_material`.`course_id`
                                       AND `course_learning_material_x_mesh`.`course_learning_material_id`
                                                               = `course_learning_material`.`course_learning_material_id`
                                  UNION
                                    SELECT `objective_x_mesh`.`mesh_descriptor_uid`
                                      FROM `course_x_objective`, `objective_x_mesh`, `course`
                                     WHERE `course`.`course_id` = ' . $clean['id'] .'
                                       AND `course`.`deleted` = FALSE
                                       AND `course`.`course_id` = `course_x_objective`.`course_id`
                                       AND `objective_x_mesh`.`objective_id` = `course_x_objective`.`objective_id`
                                  UNION
                                    SELECT `session_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_x_mesh`
                                     WHERE `session`.`course_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_x_mesh`.`session_id` = `session`.`session_id`
                                  UNION
                                    SELECT `session_learning_material_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_learning_material_x_mesh`,`session_learning_material`
                                     WHERE `session`.`course_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_learning_material`.`session_id` = `session`.`session_id`
                                       AND `session_learning_material_x_mesh`.`session_learning_material_id`
                                                            = `session_learning_material`.`session_learning_material_id`
                                  UNION
                                    SELECT `objective_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_x_objective`, `objective_x_mesh`
                                     WHERE `session`.`course_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_x_objective`.`session_id` = `session`.`session_id`
                                       AND `objective_x_mesh`.`objective_id` = `session_x_objective`.`objective_id`';
                    $courseRow = $this->course->getRowForPrimaryKeyId($poValues[0]);
                    if ($courseRow) {
                        $poDisplayValue = $courseRow->title;
                    }
                    break;
                case self::REPORT_NOUN_SESSION :
                    $queryString = 'SELECT `session_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_x_mesh`
                                     WHERE `session`.`session_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_x_mesh`.`session_id` = `session`.`session_id`
                                  UNION
                                    SELECT `session_learning_material_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_learning_material_x_mesh`,`session_learning_material`
                                     WHERE `session`.`session_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_learning_material`.`session_id` = `session`.`session_id`
                                       AND `session_learning_material_x_mesh`.`session_learning_material_id`
                                                            = `session_learning_material`.`session_learning_material_id`
                                  UNION
                                    SELECT `objective_x_mesh`.`mesh_descriptor_uid`
                                      FROM `session`, `session_x_objective`, `objective_x_mesh`
                                     WHERE `session`.`session_id` = ' . $clean['id'] .'
                                       AND `session`.`deleted` = FALSE
                                       AND `session_x_objective`.`session_id` = `session`.`session_id`
                                       AND `objective_x_mesh`.`objective_id` = `session_x_objective`.`objective_id`';
                    $sessionRow = $this->iliosSession->getRowForPrimaryKeyId($poValues[0]);
                    if ($sessionRow) {
                        $poDisplayValue = $sessionRow->title;
                    }
                    break;
                case self::REPORT_NOUN_LEARNING_MATERIAL :
                    $queryString = 'SELECT `cxm`.`mesh_descriptor_uid`
                                    FROM `course_learning_material` `c`, `course_learning_material_x_mesh` `cxm`
                                   WHERE `c`.`course_learning_material_id` = `cxm`.`course_learning_material_id`
                                     AND `c`.`learning_material_id` = ' . $clean['id'] .'
                                UNION
                                  SELECT `sxm`.`mesh_descriptor_uid`
                                    FROM `session_learning_material` `s`, `session_learning_material_x_mesh` `sxm`
                                   WHERE `s`.`session_learning_material_id` = `sxm`.`session_learning_material_id`
                                     AND `s`.`learning_material_id` = ' . $clean['id'];
                    $learningMaterialRow = $this->learningMaterial->getRowForPrimaryKeyId($poValues[0]);
                    if ($learningMaterialRow) {
                        $poDisplayValue = $learningMaterialRow->title;
                    }
                    break;
                default :
                    $poDisplayValue = "Error: Can not match prepositional object - " . $po . ", contact developer.";
            }

            if (strlen($queryString) > 0) {
                $queryResults = $DB->query($queryString);
            }
        }

        if (! is_null($queryResults)) {
            $idArray = array();

            foreach ($queryResults->result_array() as $row) {
                array_push($idArray, $row['mesh_descriptor_uid']);
            }

            $this->reallyFreeQueryResults($queryResults);

            foreach ($idArray as $id) {
                $meshRow = $this->mesh->getRowForPrimaryKeyId($id);

                array_push($rhett, $meshRow->name);
            }
            usort($rhett, 'strcasecmp');
        }

        return $rhett;
    }

    /**
     * Returns a formatted text string containing program title and program year information for a given program year.
     * @param int $programYearId the program year id.
     * @return string|boolean the formatted program title/year info, or FALSE if no program year could be found
     */
    protected function _getDisplayForProgramYear ($programYearId)
    {
        $programYearRow = $this->programYear->getRowForPrimaryKeyId($programYearId);
        if (! $programYearRow) {
            return false;
        }
        $programRow = $this->program->getRowForPrimaryKeyId($programYearRow->program_id);
        if (! $programRow) {
            return false;
        }
        $lang =  $this->getLangToUse();
        $classOfStr = $this->i18nVendor->getI18NString('general.phrases.class_title_prefix', $lang);
        $classYear = $programRow->duration + $programYearRow->start_year;
        return $programRow->title . ' - ' . $classOfStr . ' ' . $classYear;
    }

    protected function _printCompetencyTree ($competencyTree)
    {
        $retval = array();
        if (!empty($competencyTree)) {
            foreach ($competencyTree as $node) {
                $line = $node['title'];
                if (isset($node['subdomains']) && is_array($node['subdomains'])) {
                    $line .= $this->_printCompetencyTreeNodes( $node['subdomains'] );
                }
                array_push($retval, $line);
            }
        }
        return $retval;
    }

    protected function _printCompetencyTreeNodes ($competencyTreeNodes)
    {
        $retval = "<ul>";

        foreach($competencyTreeNodes as $node) {
            $retval .=  "<li>" . $node['title'];
            if (isset($node['subdomains']) && is_array($node['subdomains'])) {
                $reval .= $this->_printCompetencyTreeNodes( $node['subdomains'] );
            }
            $retval .= "</li>";
        }
        $retval .= "</ul>";
        return $retval;
    }
}
