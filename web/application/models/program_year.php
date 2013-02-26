<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "program_year" table.
 * @todo nothing is really ACID here.
 */
class Program_Year extends Abstract_Ilios_Model
{

    /**
     * @var int
     */
    protected $startYear;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('program_year', array('program_year_id'));

        $this->createDBHandle();

        $this->load->model('Cohort', 'cohort', TRUE);
        $this->load->model('Competency', 'competency', TRUE);
        $this->load->model('Department', 'department', TRUE);
        $this->load->model('Discipline', 'discipline', TRUE);
        $this->load->model('Objective', 'objective', TRUE);
        $this->load->model('Program', 'program', TRUE);
        $this->load->model('Publish_Event', 'publishEvent', TRUE);
        $this->load->model('School', 'school', TRUE);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Returns a list of program years in this format:
     * Program title, Class of XXXX
     * The list is sorted by program title, then start year
     * The list does not return deleted program year, deleted program
     */
    public function getProgramYears() {
        $DB = $this->dbHandle;
        $lang =  $this->getLangToUse();
        $classOfStr = $this->i18nVendor->getI18NString('general.phrases.class_title_prefix', $lang);

        $queryString = 'SELECT `program`.`title`, `program_year`.`start_year` + `program`.`duration` as classOfValue,
                               `program_year`.`program_year_id`
                          FROM `program_year`, `program`
                         WHERE `program_year`.`program_id` = `program`.`program_id`
                           AND `program_year`.`deleted` = 0
                           AND `program`.`deleted` = 0
                           AND `program`.`owning_school_id` = ' . $this->session->userdata('school_id') . '
                      ORDER BY `program`.`title`, `program_year`.`start_year`';

        $queryResults = $DB->query($queryString);
        $items = array();
        foreach ($queryResults->result_array() as $row) {
            $item = array();
            $item['value'] = $row['program_year_id'];
            $item['display_title'] = $row['title'] . ' - '. $classOfStr . ' ' . $row['classOfValue'];

            array_push($items, $item);
        }

        return $items;
    }

    /**
     * @todo add code docs
     * @param int $programId
     * @return array
     */
    public function getProgramYearsForProgram ($programId)
    {
        $rhett = array();
        $crossIdArray = null;
        $queryResults = null;
        $DB = $this->dbHandle;

        $DB->where('program_id', $programId);
        $DB->where('deleted', 0);
        $DB->where('archived', 0);
        $DB->order_by('start_year');
        $queryResults = $DB->get($this->databaseTableName);

        foreach ($queryResults->result_array() as $row) {
            $pyid = $row['program_year_id'];
            $subQueryResults = null;
            $programYear = null;

            $programYear = array();

            $programYear['program_year_id'] = $row['program_year_id'];
            $programYear['start_year'] = $row['start_year'];
            $programYear['locked'] = $row['locked'];
            $programYear['publish_event_id'] = (($row['publish_event_id'] != null)
                                                 && ($row['publish_event_id'] != ''))
                                                        ? $row['publish_event_id'] : -1;


            $stewardArray = $this->getStewardsForProgramYear($pyid);
            if ($stewardArray != null) {
                $programYear['steward'] = $stewardArray;
            }

            $crossIdArray = $this->getIdArrayFromCrossTable('program_year_x_competency',
                                                            'competency_id', 'program_year_id',
                                                            $pyid);
            if ($crossIdArray != null) {
                $compentencyArray = array();

                foreach ($crossIdArray as $id) {
                    $competency = $this->competency->getRowForPrimaryKeyId($id);

                    if ($competency != null) {
                        array_push($compentencyArray, $competency);
                    }
                    else {
                        // todo
                    }
                }

                $programYear['competency'] = $compentencyArray;
            }


            $programYear['objectives'] = $this->getObjectivesForProgramYear($pyid);


            $crossIdArray = $this->getIdArrayFromCrossTable('program_year_x_discipline',
                                                            'discipline_id', 'program_year_id',
                                                            $pyid);
            if ($crossIdArray != null) {
                $disciplineArray = array();

                foreach ($crossIdArray as $id) {
                    $discipline = $this->discipline->getRowForPrimaryKeyId($id);

                    if ($discipline != null) {
                        array_push($disciplineArray, $discipline);
                    }
                    else {
                        // todo
                    }
                }

                $programYear['discipline'] = $disciplineArray;
            }

            $crossIdArray = $this->getIdArrayFromCrossTable('program_year_director', 'user_id',
                                                            'program_year_id', $pyid);
            if ($crossIdArray != null) {
                $directorArray = array();

                foreach ($crossIdArray as $id) {
                    $director = $this->user->getRowForPrimaryKeyId($id);

                    if ($director != null) {
                        array_push($directorArray, $director);
                    }
                    else {
                        // todo
                    }
                }

                $programYear['director'] = $directorArray;
            }


            array_push($rhett, $programYear);
        }

        // todo return rhettArray, have controller json it up
        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $programYearId
     * @return array
     */
    public function getObjectivesAndProgramTitle ($programYearId)
    {
        $rhett = array();

        $rhett['objectives'] = $this->getObjectivesForProgramYear($programYearId);

        $programYear = $this->getRowForPrimaryKeyId($programYearId);
        $program = $this->program->getRowForPrimaryKeyId($programYear->program_id);
        $rhett['program_title'] = $program->title;
        $rhett['school_id'] = $program->owning_school_id;
        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $userId
     * @return array
     */
    public function getProgramYearsForDirector ($userId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('program_year_director', 'program_year_id',
                                                        'user_id', $userId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $programYear = $this->getRowForPrimaryKeyId($id);

                if (($programYear != null)
                        && ($programYear->deleted == 0)
                        && ($programYear->archived == 0)) {
                    array_push($rhett, $programYear);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $progamYearId
     * @return array|NULL
     */
    public function getStewardsForProgramYear ($progamYearId)
    {
        $DB = $this->dbHandle;
        $queryResults = null;
        $rhett = null;

        $DB->where('program_year_id', $progamYearId);
        $queryResults = $DB->get('program_year_steward');

        if ($queryResults->num_rows() > 0) {
            $rhett = array();

            foreach ($queryResults->result_array() as $row) {
                $model = array();

                if (is_null($row['department_id'])) {
                    $schoolRow = $this->school->getRowForPrimaryKeyId($row['school_id']);

                    $model['title'] = $schoolRow->title;
                    $model['parent_school_id'] = -1;
                    $model['row_id'] = $schoolRow->school_id;
                    $model['steward_is_school'] = 1;
                }
                else {
                    $deparmentRow = $this->department->getRowForPrimaryKeyId($row['department_id']);

                    $model['title'] = $deparmentRow->title;
                    $model['parent_school_id'] = $deparmentRow->school_id;
                    $model['row_id'] = $deparmentRow->department_id;
                    $model['steward_is_school'] = 0;
                }

                array_push($rhett, $model);
            }
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $programYearId
     * @param boolean $shouldLock
     * @param boolean $shouldArchive if true, shouldLock is ignored and assumed to be true
     * @param array $auditAtoms
     */
    public function lockOrArchiveProgramYear ($programYearId, $shouldLock, $shouldArchive, &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $lockValue = ($shouldLock ? 1 : 0);
        if ($shouldArchive) {
            $lockValue = 1;
            $archiveValue = 1;
        }
        else {
            $archiveValue = 0;
        }

        $updateRow = array();
        $updateRow['locked'] = $lockValue;
        $updateRow['archived'] = $archiveValue;

        $DB->where('program_year_id', $programYearId);
        $DB->update($this->databaseTableName, $updateRow);


        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE, 1));
    }

    /**
     * @todo add code docs
     * @param int $programYearId
     * @param array $auditAtoms
     * @return boolean TRUE if there was an apparent deletion (the check is not robust (which would be
     *              requerying the db to make sure that no row exists for the deleted id (TODO))
     */
    public function deleteProgramYear ($programYearId, &$auditAtoms)
    {
        $groupIds = $this->cohort->getGroupIdsForCohortWithProgramYear($programYearId);

        $DB = $this->dbHandle;

        $tables = array('program_year_director', 'program_year_x_competency',
                        'program_year_x_objective', 'program_year_x_discipline',
                        'program_year_steward');

        $DB->where('program_year_id', $programYearId);
        $DB->delete($tables);

        if ($this->transactionAtomFailed()) {
            return false;
        }

        $updateRow = array();
        $updateRow['deleted'] = 1;

        $DB->where('program_year_id', $programYearId);
        $DB->update($this->databaseTableName, $updateRow);

        if ($this->transactionAtomFailed()) {
            return false;
        }

        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            'program_year_director',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            'program_year_x_competency',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            'program_year_x_discipline',
                                                            Audit_Event::$DELETE_EVENT_TYPE));
        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$DELETE_EVENT_TYPE, 1));

        if (! $this->cohort->deleteCohortAndAssociationsForProgramYear($programYearId,
                                                                       $auditAtoms)) {
            return false;
        }

        foreach ($groupIds as $groupId) {
            $DB->where('group_id', $groupId);
            $DB->or_where('parent_group_id', $groupId);
            $DB->delete('group');

            if ($this->transactionAtomFailed()) {
                return false;
            }

            array_push($auditAtoms, $this->auditEvent->wrapAtom($groupId, 'group_id', 'group',
                                                                Audit_Event::$DELETE_EVENT_TYPE));
            array_push($auditAtoms, $this->auditEvent->wrapAtom($groupId, 'parent_group_id',
                                                                'group',
                                                                Audit_Event::$DELETE_EVENT_TYPE));
        }

        return true;
    }

    /**
     * Adds a new year to a given program.
     *
     * @param int $startYear the start year
     * @param array $compentenciesArray a list of program competencies
     * @param array $objectivesArray a list of program objectives
     * @param array $disciplinesArray a list of program disciplines
     * @param array $directorsArray a list of program directors
     * @param array $stewardsArray a list of program stewards
     * @param int $programId the program id
     * @param int $publishId
     * @param array $auditAtoms
     * @param array $returningObjectives
     *
     * @return int the id of the newly created program year
     *
     * @todo The code calling this method should be responsible for transactionality; to that extent, this method should some sort of success / failure indication.
     */
    public function addProgramYear ($startYear, $compentenciesArray, array $objectivesArray, array $disciplinesArray,
        array $directorsArray, array $stewardsArray, $programId, $publishId, array &$auditAtoms, array &$returningObjectives)
    {
        $DB = $this->dbHandle;

        // First do the program_year table insert (then the cross tables)
        $newRow = array();
        $newRow['program_year_id'] = null;

        $newRow['start_year'] = $startYear;
        $newRow['program_id'] = $programId;
        $newRow['deleted'] = 0;
        $newRow['locked'] = 0;
        $newRow['archived'] = 0;
        $newRow['publish_event_id'] = ((($publishId != null) && ($publishId > 0)) ? $publishId
                                                                                  : null);

        $DB->insert($this->databaseTableName, $newRow);

        $newId = $DB->insert_id();

        array_push($auditAtoms, $this->auditEvent->wrapAtom($newId, 'program_year_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$CREATE_EVENT_TYPE, 1));


        $duration = $this->program->getDurationForProgramWithId($programId);
        $titleString = $this->classTitleForStartYearAndDuration($startYear, $duration);

        $newRow = array();
        $newRow['cohort_id'] = null;
        $newRow['title'] = $titleString;
        $newRow['program_year_id'] = $newId;
        $DB->insert('cohort', $newRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($DB->insert_id(), 'cohort_id', 'cohort',
                                                            Audit_Event::$CREATE_EVENT_TYPE));

        // TODO audit events for the cross table transactions?
        $this->processCrossTableTransactions('program_year_x_competency', 'competency_id', $newId,
                                             $compentenciesArray, false);
        $this->processCrossTableTransactions('program_year_x_discipline', 'discipline_id', $newId,
                                             $disciplinesArray, false);
        $this->processCrossTableTransactions('program_year_director', 'user_id', $newId,
                                             $directorsArray, false);
        $this->processStewardTableTransactions($newId, $stewardsArray, false);

        // Explicitly set the "dbId" property to -1
        // for each given objective
        // in order to force new objective creation.
        // This will cause existing objectives to be cloned rather than shared across
        // multiple program years.
        for ($i = 0, $n = count($objectivesArray); $i < $n; $i++) {
            $objectivesArray[$i]['dbId'] = -1;
        }

        $returningObjectives = $this->_saveObjectives($objectivesArray, 'program_year_x_objective',
                                                     'program_year_id', $newId, $auditAtoms);
        return $newId;
    }

    /**
     * The code calling this method should be responsible for transactionality; to that extent, this
     * method should some sort of success / failure indication. (TODO)
     * @todo improve code docs
     * @param int $programYearId
     * @param int $startYear
     * @param array $compentenciesArray
     * @param array $objectivesArray
     * @param array $disciplinesArray
     * @param array $directorsArray
     * @param array $stewardsArray
     * @param int $publishId
     * @param int $programId
     * @param array $auditAtoms
     * @return array
     */
    public function updateProgramYearWithId ($programYearId, $startYear, array $compentenciesArray,
        array $objectivesArray, array $disciplinesArray, array $directorsArray, array $stewardsArray,
        $publishId, $programId, array &$auditAtoms)
    {
        $DB = $this->dbHandle;

        $updateValues = array();
        $updateValues['start_year'] = $startYear;
        $updateValues['program_id'] = $programId;
        $updateValues['publish_event_id'] = (($publishId > 0) ? $publishId : null);

        $DB->where('program_year_id', $programYearId);
        $DB->update($this->databaseTableName, $updateValues);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($programYearId, 'program_year_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE, 1));


        $duration = $this->program->getDurationForProgramWithId($programId);
        $titleString = $this->classTitleForStartYearAndDuration($startYear, $duration);

        $row = $this->cohort->getCohortWithProgramYearId($programYearId);
        $cohortId = $row->cohort_id;

        $updateValues = array();
        $updateValues['title'] = $titleString;

        $DB->where('cohort_id', $cohortId);
        $DB->update('cohort', $updateValues);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($cohortId, 'cohort_id', 'cohort',
                                                            Audit_Event::$UPDATE_EVENT_TYPE));

        $rhett = $this->_saveObjectives($objectivesArray, 'program_year_x_objective',
                                       'program_year_id', $programYearId, $auditAtoms);

        // TODO audit events for the cross table transactions?
        $this->processCrossTableTransactions('program_year_x_competency', 'competency_id',
                                             $programYearId, $compentenciesArray, true);
        $this->processCrossTableTransactions('program_year_x_discipline', 'discipline_id',
                                             $programYearId, $disciplinesArray, true);
        $this->processCrossTableTransactions('program_year_director', 'user_id', $programYearId,
                                             $directorsArray, true);
        $this->processStewardTableTransactions($programYearId, $stewardsArray, true);

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $schoolId
     * @return array
     */
    public function getAllProgramCohortsWithSchoolId ($schoolId)
    {
        $programs = $this->program->getAllPublishedProgramsWithSchoolId($schoolId);

        $rhett = array();

        if (!empty($programs)) {
            $DB = $this->dbHandle;

            $DB->where('deleted', 0);
            $DB->where('publish_event_id != ', 'NULL');
            $DB->where('archived', 0);
            $DB->where_in('program_id', array_keys($programs));
            $DB->join('cohort', 'cohort.program_year_id = ' . $this->databaseTableName . '.program_year_id');

            $results = $DB->get($this->databaseTableName);

            foreach ($results->result_array() as $row) {
                $id = $row['program_year_id'];
                $program_id = $row['program_id'];

                $value = array();
                $value['cohort_id'] = $row['cohort_id'];
                $value['cohort_title'] = $row['title'];
                $value['program_id'] = $program_id;
                $value['program_title'] = $programs[$program_id]['title'];
                $value['program_short_title'] = $programs[$program_id]['short_title'];
                $value['program_cohort_title'] = $value['program_title'] . ' ' . $value['cohort_title'];

                $rhett[$id] = $value;
            }
        }
        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $programYearId
     * @param array $stewardsArray
     * @param boolean $partOfUpdate
     */
    protected function processStewardTableTransactions ($programYearId, $stewardsArray,
        $partOfUpdate)
    {
        $DB = $this->dbHandle;

        if ($partOfUpdate) {
            $DB->where('program_year_id', $programYearId);
            $DB->delete('program_year_steward');
        }

        foreach ($stewardsArray as $key => $val) {
            $newRow = array();

            $newRow['program_year_id'] = $programYearId;
            if ($val['parentId'] == -1) {
                $newRow['school_id'] = $val['dbId'];
            }
            else {
                $newRow['school_id'] = $val['parentId'];
                $newRow['department_id'] = $val['dbId'];
            }

            $DB->insert('program_year_steward', $newRow);
        }
    }

    /**
     * @todo add code docs
     * @param string $table
     * @param string $column
     * @param int $programYearId
     * @param array $modelArray
     * @param boolean $partOfUpdate
     */
    protected function processCrossTableTransactions ($table, $column, $programYearId, $modelArray,
        $partOfUpdate)
    {
        $DB = $this->dbHandle;

        $models = array();

        if ($partOfUpdate) {
            $models = $this->deleteIntersection($table, $programYearId, $column, $modelArray);
        }
        else {
            $models = $modelArray;
        }

        foreach ($models as $key => $val) {
            $newRow = array();

            $newRow['program_year_id'] = $programYearId;
            $newRow[$column] = $val['dbId'];

            $DB->insert($table, $newRow);
        }
    }

    /**
     * Given a model array (array), a table name, a program year id, and a column name within that
     * table, delete all rows in that table where the program year id is as specified and a
     * pre-existing value in that column name does not exists in the model array. Return an
     * array of models from the model array which are not already in the table.
     *
     * @param string $tableName
     * @param string $programYearId
     * @param string $uniqueColumnName
     * @param array $modelArrayArray assumed to have values which are arrays with a key named 'dbId'
     * @return an array of ids which should be inserted
     */
    protected function deleteIntersection ($tableName, $programYearId, $uniqueColumnName,
        $modelArrayArray)
    {
        $idArray = array();
        $exists = array();
        $nonAssociativeArray = array();     // for diffing
        $diffedArray = null;
        $toDelete = array();
        $queryResults = null;
        $DB = $this->dbHandle;

        foreach ($modelArrayArray as $index => $modelArray) {
            $idArray[$modelArray['dbId']] = $modelArray['dbId'];

            array_push($nonAssociativeArray, $modelArray['dbId']);
        }

        $DB->select($uniqueColumnName);
        $DB->where('program_year_id', $programYearId);

        $queryResults = $DB->get($tableName);

        foreach ($queryResults->result_array() as $row) {
            $id = $row[$uniqueColumnName];

            if (isset($idArray[$id])){
                array_push($exists, $id);
            }
            else {
                array_push($toDelete, $id);
            }
        }

        foreach ($toDelete as $id) {
            $DB->where('program_year_id', $programYearId);
            $DB->where($uniqueColumnName, $id);

            $DB->delete($tableName);
        }

        $diffedArray = array_diff($nonAssociativeArray, $exists);
        $exists = array();
        foreach ($diffedArray as $key => $val) {
            foreach ($modelArrayArray as $index => $modelArray) {
                if ($modelArray['dbId'] == $val) {
                    array_push($exists, $modelArray);

                    break;
                }
            }
        }

        return $exists;
    }

    /**
     * Generates a CSS class-name for a given start-year and duration of a program year.
     * @param int $startYear
     * @param int $duration
     * @return string the CSS class name
     */
    protected function classTitleForStartYearAndDuration ($startYear, $duration)
    {
        $lang = $this->getLangToUse();
        $msg = $this->i18nVendor->getI18NString('general.phrases.class_title_prefix', $lang);
        return $msg . " " . ($duration + $startYear);
    }

    /**
     * @todo add code docs
     * @param int $programYearId
     * @return array
     */
    protected function getObjectivesForProgramYear ($programYearId)
    {
        $rhett = array();

        $crossIdArray = $this->getIdArrayFromCrossTable('program_year_x_objective',
                'objective_id', 'program_year_id',
                $programYearId);

        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                $objective = $this->objective->getObjective($id);

                if ($objective != null) {
                    array_push($rhett, $objective);
                }
                else {
                    // todo
                }
            }
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     * @param int $pyid
     * @param int $schoolId
     * @return boolean
     */
    protected function programYearIsStewardedBySchool ($pyid, $schoolId)
    {
        $clean = array();
        $clean['py_id'] = (int) $pyid;
        $clean['school_id'] = (int) $schoolId;
        $sql = "SELECT program_has_year_stewarded_by_school({$clean['py_id']}, {$clean['school_id']}) AS flag";

        $DB = $this->dbHandle;
        $queryResults = $DB->query($sql);

        if ($queryResults->num_rows() > 0) {
            $firstRow = $queryResults->first_row();
            return ($firstRow->flag == '1');
        }
        return false;
    }
}
