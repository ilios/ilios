<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package Ilios
 *
 * Base Data Access Object (DAO).
 *
 * Provided functionality for handling transactions and common CRUD-operations used across the entire persistence layer
 * in Ilios.
 * All table-specific DAOs in Ilios must inherit from this class.
 */
abstract class Ilios_Base_Model extends CI_Model
{
    /**
     * Minimum number of characters that a given search term must possess in order for wildcard search to take
     * precedence of exact term matching.
     *
     * @var int
     */
    const WILDCARD_SEARCH_CHARACTER_MIN_LIMIT = 3;

    /**
     * The name of the table within the database which this model represents.
     *
     * @var string
     */
    protected $databaseTableName;
    /**
     * The column names comprising the targeted table's primary key.
     *
     * @var array
     */
    protected $databaseTablePrimaryKeyArray;

    /**
     * Constructor.
     *
     * @param string $tableName The name of the table within the database which this model represents.
     * @param array $primaryKeyArray The column names comprising the targeted table's primary key.
     */
    public function __construct ($tableName = 'none', $primaryKeyArray = array())
    {
        parent::__construct();
        $this->databaseTableName = $tableName;
        $this->databaseTablePrimaryKeyArray = $primaryKeyArray;
    }

    /**
     * Retrieves the name of the database table that this model represents.
     * @return string The table name.
     */
    public function getTableName ()
    {
        return $this->databaseTableName;
    }

    /**
     * Starts a database transaction.
     *
     * @see CI_DB_driver::trans_start()
     * @link http://ellislab.com/codeigniter/user-guide/database/transactions.html
     */
    public function startTransaction ()
    {
        $this->db->trans_start();
    }

    /**
     * Checks the status of a database transaction.
     *
     * @see CI_DB_driver::trans_status()
     * @link http://ellislab.com/codeigniter/user-guide/database/transactions.html
     */
    public function transactionAtomFailed ()
    {
        return ($this->db->trans_status() === FALSE);
    }

    /**
     * Commits a database transaction.
     *
     * @see CI_DB_mysqli_driver::trans_commit()
     * @link http://ellislab.com/codeigniter/user-guide/database/transactions.html
     */
    public function commitTransaction ()
    {
        $this->db->trans_commit();
    }

    /**
     * Rolls back a database transaction.
     *
     * @see CI_DB_mysqli_driver::trans_rollback()
     * @link http://ellislab.com/codeigniter/user-guide/database/transactions.html
     */
    public function rollbackTransaction ()
    {
        $this->db->trans_rollback();
    }

    /**
     * Retrieves a query result set containing all matching records from the model's table by performing
     * a double-sided wildcard search on the table's
     * "title" column.
     * matching records.
     * @param string $match The search term.
     * @param boolean $checkForDelete If TRUE then records marked as 'deleted' will be filtered out.
     * @return CI_DB_result The query result object.
     */
    public function returnRowsFilteredOnTitleMatch ($match, $checkForDelete = false)
    {
        $this->db->like('title', $match, 'both');
        if ($checkForDelete) {
            $this->db->where('deleted', 0);
        }
        $this->db->order_by('title');

        return $this->db->get($this->databaseTableName);
    }

    /**
     * Checks whether a given record is flagged as 'published' in the model's table.
     *
     * @param mixed $id The record's primary key value.
     * @return boolean|null NULL if the $id does not have a row, FALSE if the row is not published (its
     *     publish_event_id column value is NULL), or TRUE if it is published.
     */
    public function isPublished ($id)
    {
        $rhett = false;

        $row = $this->getRowForPrimaryKeyId($id);

        if (is_null($row)) {
            return null;
        }

        return (! is_null($row->publish_event_id));
    }

    /**
     * Retrieves a record from the model's table by its primary key.
     *
     * @param $id mixed The record's primary key.
     * @param boolean $checkForDelete If TRUE then records marked as 'deleted' will be filtered out.
     * @param int $schoolId If provided then records are further filtered checking the given value against the value in the
     *     'owning_school_id' column.
     * @return null|stdClass The record in the table, or NULL if none was found.
     * @see Ilios_Base_Model::getRow()
     */
    public function getRowForPrimaryKeyId ($id, $checkForDelete = false, $schoolId = null)
    {
        return $this->getRow($this->databaseTableName, $this->databaseTablePrimaryKeyArray[0], $id, $checkForDelete, $schoolId);
    }

    /**
     * Retrieves the first row from a given table matching a given value in a given column.
     *
     * @param string $tableName The table name.
     * @param string $rowName The column name.
     * @param string $id The column value.
     * @param boolean $checkForDelete If TRUE then records marked as 'deleted' will be filtered out.
     * @param int $schoolId If provided then records are further filtered checking the given value against the value in the
     *     'owning_school_id' column.
     * @return null|stdClass The first matching record in the table, or an NULL if none was found.
     */
    public function getRow ($tableName, $rowName, $id, $checkForDelete = false, $schoolId = null)
    {
        $this->db->where($rowName, $id);
        if ($checkForDelete) {
            $this->db->where('deleted', 0);
        }

        if ($schoolId != null) {
            $this->db->where('owning_school_id', $schoolId);
        }

        $query = $this->db->get($tableName);

        if (is_null($query) || ($query->num_rows() == 0)) {
            return null;
        }

        $rhett =  $query->first_row();

        $query->free_result();

        return $rhett;
    }

    /**
     * Returns the language key as specified in the application configuration.
     *
     * @see Abstract_Ilios_Controller::getLangToUse()
     * @return string The language key.
     */
    protected function getLangToUse ()
    {
        return $this->config->item('ilios_default_lang_locale');
    }

    /**
     * Retrieves a list of ids from a target column in a JOIN table that are matching a given constraining value.
     * @param string $table The table name.
     * @param string $column The name of the target column.
     * @param string $uniquingColumn The name of the constraining column.
     * @param mixed $uniquingId The value in the constraining column.
     * @return array|null an array of target ids, or NULL if none were found.
     */
    protected function getIdArrayFromCrossTable ($table, $column, $uniquingColumn, $uniquingId)
    {
        $rhett = null;

        $this->db->select($column);
        $this->db->where($uniquingColumn, $uniquingId);
        $query = $this->db->get($table);

        if ($query->num_rows()) {
            $rhett = array();

            foreach ($query->result_array() as $row) {
                $rhett[] = $row[$column];
            }
        }

        $query->free_result();

        return $rhett;
    }

    /**
     * Checks if a given couplet exists in the given JOIN table and inserts it if does not.
     *
     * @param string $tableName The name of the JOIN table.
     * @param string $keyA The left- hand column name.
     * @param mixed $valueA The left-hand column value.
     * @param string $keyB The right-hand column name.
     * @param mixed $valueB The right-hand column value.
     * @return int 1 if the couplet already existed or the insert was successful, 0 on an error.
     */
    protected function insertSingleCrossTablePair ($tableName, $keyA, $valueA, $keyB, $valueB)
    {
        $rhett = 0;

        $this->db->where($keyA, $valueA);
        $this->db->where($keyB, $valueB);
        $queryResults = $this->db->get($tableName);
        if ($queryResults->num_rows() == 0) {
            $newRow = array();
            $newRow[$keyA] = $valueA;
            $newRow[$keyB] = $valueB;

            $this->db->insert($tableName, $newRow);
            if ($this->db->affected_rows() == 1) {
                $rhett = 1;
            }
        }
        else {
            $rhett = 1;
        }

        return $rhett;
    }

    /**
     * Saves associations between a given record in table A and given records in table B in a given JOIN table.
     *
     * Given associations that are non-existent in the database will be added. Pre-existing associations that are not
     * in the given input will be deleted from the database.
     *
     * @param string $joinTblName The name of the JOIN table.
     * @param string $joinColName The name of the column in the JOIN table that references the given record id in table A.
     * @param int $joinId The record id from table A.
     * @param string $refColName The name of the column in the JOIN table that references given record ids in table B.
     * @param array $refData The records in table B.
     * @param array|null $existingAssocIds The record ids of already associated records in table B.
     * @param string $idName The key attribute name of {$refData} elements.
     * @param array $auditAtoms The auditing trail.
     */
    protected function _saveJoinTableAssociations ($joinTblName, $joinColName, $joinId, $refColName, $refData = array(),
                                                   $existingAssocIds = array(), $idName = 'dbId',
                                                   array &$auditAtoms = array())
    {
        // figure out which associations were added, removed and kept.
        $keepAssocIds = array();
        $addAssocIds = array();
        $removeAssocIds = array();
        if (! empty($existingAssocIds)) {
            foreach ($refData as $item) {
                if (in_array($item[$idName], $existingAssocIds)) { // exists?
                    $keepAssocIds[] = $item[$idName]; // flag as "to keep"
                } else {
                    $addAssocIds[] = $item[$idName]; // flag as "to add"
                }
            }
            $removeAssocIds = array_diff($existingAssocIds, $keepAssocIds); // find the assoc. to remove
        } else {
            foreach ($refData as $item) { // add all
                $addAssocIds[] = $item[$idName];
            }
        }
        if (count($addAssocIds)) { // add new associations
            $this->_associateWithJoinTable($joinTblName, $joinColName, $joinId, $refColName, $addAssocIds, $auditAtoms);
        }
        if (count($removeAssocIds)) { // remove deleted associations
            $this->_unassociateFromJoinTable($joinTblName, $joinColName, $joinId, $refColName, $removeAssocIds, $auditAtoms);
        }
    }

    /**
     * Associates a given record in table A with given records in table B via a JOIN table.
     *
     * @param string $joinTblName The name of the JOIN table.
     * @param string $joinColName The name of the column in the JOIN table that references the given record id in table A.
     * @param int $joinId The record id from table A.
     * @param string $refColName The name of the column in the JOIN table that references given record ids in table B.
     * @param array $refIds The record ids in table B.
     * @param array $auditAtoms The auditing trail.
     */
    protected function _associateWithJoinTable ($joinTblName, $joinColName, $joinId, $refColName,
                                                array $refIds = array(), array &$auditAtoms = array())
    {
        $refIds = array_unique($refIds); // de-dupe
        $refIds = array_filter($refIds); // remove falsy values -  this gets rid of NULLs
        if (count($refIds)) {
            foreach ($refIds as $id) {
                $row = array();
                $row[$joinColName] = $joinId;
                $row[$refColName] = $id;
                $this->db->insert($joinTblName, $row);
            }
            $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($joinId, $joinColName, $joinTblName,
                Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
        }
    }

    /**
     * Removes any associations between a given record in table A with given records in table B from a JOIN table.
     *
     * @param string $joinTblName The name of the JOIN table.
     * @param string $joinColName The name of the column in the JOIN table that references the given record id in table A.
     * @param int $joinId The record id from table A.
     * @param string $refColName The name of the column in the JOIN table that references given record ids in table B.
     * @param array $refIds The record ids in table B.
     * @param array $auditAtoms The auditing trail.
     */
    protected function _unassociateFromJoinTable ($joinTblName, $joinColName, $joinId, $refColName,
                                                  array $refIds = array(), array &$auditAtoms = array())
    {
        $refIds = array_unique($refIds); // de-dupe
        $refIds = array_filter($refIds); // remove falsy values -  this gets rid of NULLs
        if (count($refIds)) {
            $this->db->where($joinColName, $joinId);
            $this->db->where_in($refColName, $refIds);
            $this->db->delete($joinTblName);
            $auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($joinId, $joinColName, $joinTblName,
                Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
        }
    }


    /**
     * Removes any entries between given tables A and B for a given record in A from a given JOIN table.
     *
     * Then (re)associates given records in table B with that record in table A.
     * Motto: "delete all, then re-enter them again."
     *
     * @deprecated
     * Use Ilios_Base_Model::_saveJoinTableAssociations() instead.
     *
     *
     * @param array $modelArray
     * @param string $tableName
     * @param string $columnName
     * @param string $uniquingColumn
     * @param int $uniquingId
     * @param string $idColumnName
     * @see Abstract_Ilios_Model::_saveJoinTableAssociations()
     */
    protected function performCrossTableInserts (array $modelArray, $tableName, $columnName, $uniquingColumn,
                                                 $uniquingId, $idColumnName = 'dbId')
    {
        $this->db->where($uniquingColumn, $uniquingId);
        $this->db->delete($tableName);

        foreach ($modelArray as $key => $val) {
            $newRow = array();
            $newRow[$uniquingColumn] = $uniquingId;
            $newRow[$columnName] = $val[$idColumnName];
            $this->db->insert($tableName, $newRow);
        }
    }

    /**
     * Adds or updates given objectives in the database.
     *
     * @param array $objectives A list of objectives.
     * @param string $crossTableName The name of an objectives JOIN table.
     * @param string $crossTableColumn The column name in the objectives JOIN table that references the entity table
     *     that the objectives are associated with.
     * @param mixed $columnValue The id of the entity that the objectives are associated with.
     * @param array $auditAtoms The auditing trail.
     * @return array A nested array of associative arrays, containing information about the saved objectives.
     *     Each array element contains a MD5 hash of the objective's content (key: 'md5') and the objective's
     *     db record id (key: 'dbId').
     */
    protected function _saveObjectives (array $objectives, $crossTableName, $crossTableColumn, $columnValue,
                                        &$auditAtoms)
    {
        $rhett = array();

        // get the ids of currently associated objectives from the JOIN table
        $existingObjectiveIds = $this->getIdArrayFromCrossTable($crossTableName, 'objective_id', $crossTableColumn,
            $columnValue);

        /*
         * Objectives:
         *
         * does the objective exist already (dbId != -1), update that objective
         * else, make a new objective.
         *
         * make a new array with key 'dbId' featuring that dbId, add it to $objectiveIdArray
         * give $objectiveIdArray to the cross table insert method
         */

        foreach ($objectives as $key => $val) {
            $dbId = $val['dbId'];

            if ($dbId == -1) {
                $dbId = $this->objective->addNewObjective($val, $auditAtoms);
            }
            else {
                $this->objective->updateObjective($val, $auditAtoms);
            }

            $newId = array();
            $newId['dbId'] = $dbId;
            $newId['md5'] = $val['cachedMD5'];

            array_push($rhett, $newId);
        }

        // update object associations
        $this->_saveJoinTableAssociations($crossTableName, $crossTableColumn, $columnValue, 'objective_id', $rhett, $existingObjectiveIds);

        return $rhett;
    }

    /**
     * This method copies ("rolls over") objectives for a given associated entity from one academic year into another.
     *
     * @param string $crossTableName The name of the objectives JOIN table.
     * @param string $crossTableRowName The column name in the objectives JOIN table that references the entity table
     *     that the objectives are associated with.
     * @param string $crossTableId The id of the source entity that the objectives are associated with.
     * @param int $newCrossTableId The id of the target entity that the copied objectives should be associated with.
     * @param boolean $rolloverIsSameAcademicYear Flag indicating whether objective are being copied within the same
     *     academic year or not. If set to TRUE then objective/competency associations are copied over, otherwise not.
     * @param array|null $parentMap An array of nested arrays, representing already rolled-over parent objectives
     *     to the objectives being rolled over. Each item is an associative array containing the rolled-over parent
     *     objective's original and new record id, keyed off by "original" and "new".
     * @return array An array of nested arrays, representing the rolled over objectives. Each item is an associative
     *     array containing the rolled-over objective's original and new record id, keyed off by "original" and "new".
     */
    protected function rolloverObjectives ($crossTableName, $crossTableRowName, $crossTableId,
                                           $newCrossTableId, $rolloverIsSameAcademicYear,
                                           $parentMap = null)
    {
        $objectiveIdPairs = array();

        $shouldCopyParentAttributes = ($rolloverIsSameAcademicYear || ($parentMap != null));

        $this->db->where($crossTableRowName, $crossTableId);
        $query = $this->db->get($crossTableName);
        $objectiveIds = array();
        foreach ($query->result_array() as $row) {
            $objectiveIds[] = $row['objective_id'];
        }

        $query->free_result();

        foreach ($objectiveIds as $objectiveId) {
            $objectiveRow = $this->objective->getRowForPrimaryKeyId($objectiveId);

            $newRow = array();
            $newRow['objective_id'] = null;

            $newRow['title'] = $objectiveRow->title;
            $newRow['competency_id'] = $rolloverIsSameAcademicYear ? $objectiveRow->competency_id
                                                                   : null;

            $this->db->insert($this->objective->getTableName(), $newRow);
            $pair = array();
            $pair['new'] = $this->db->insert_id();
            $pair['original'] = $objectiveId;

            $objectiveIdPairs[] = $pair;
        }
        foreach ($objectiveIdPairs as $objectiveIdPair) {
            $newRow = array();
            $newRow[$crossTableRowName] = $newCrossTableId;
            $newRow['objective_id'] = $objectiveIdPair['new'];
            $this->db->insert($crossTableName, $newRow);

            $queryString = 'SELECT copy_objective_attributes_to_objective('
                            . $objectiveIdPair['original'] . ', ' . $objectiveIdPair['new']
                            . ($shouldCopyParentAttributes ? ', 1' : ', 0') . ')';
            $this->db->query($queryString);

            if ($parentMap != null) {
                $this->db->where('objective_id', $objectiveIdPair['new']);
                $query = $this->db->get('objective_x_objective');

                $updateList = array();
                foreach ($query->result_array() as $row) {
                    foreach ($parentMap as $parentObjectIdPair) {
                        if ($parentObjectIdPair['original'] == $row['parent_objective_id']) {
                            $updateTriplet = array();
                            $updateTriplet['oid'] = $objectiveIdPair['new'];
                            $updateTriplet['original_poid'] = $parentObjectIdPair['original'];
                            $updateTriplet['new_poid'] = $parentObjectIdPair['new'];

                            $updateList[] = $updateTriplet;
                        }
                    }
                }

                $query->free_result();

                foreach ($updateList as $updateTriplet) {
                    $this->db->where('objective_id', $updateTriplet['oid']);
                    $this->db->where('parent_objective_id', $updateTriplet['original_poid']);

                    $updateRow = array();
                    $updateRow['parent_objective_id'] = $updateTriplet['new_poid'];

                    $this->db->update('objective_x_objective', $updateRow);
                }
            }
        }

        return $objectiveIdPairs;
    }

    /**
     * Utility method for converting a given stdClass object an associative array representation.
     *
     * Of use when we get back a std obj from a db query (like via the first_row() method) and we want to ship an array
     * back to the client.
     *
     * @param stdClass $stdObj An object.
     * @return array An associative array.
     */
    protected function convertStdObjToArray ($stdObj)
    {
        $rhett = $stdObj;

        if (is_array($stdObj) || is_object($stdObj)) {
            $rhett = array();

            foreach ($stdObj as $key => $val) {
                $rhett[$key] = $this->convertStdObjToArray($val);
            }
        }

        return $rhett;
    }

    /**
     * Workaround to free query results from stored procedure calls.
     *
     * In multiple query results involving a stored procedure call, call this between queries to free query results.
     * Taken from the CI forums, see:
     * @link http://ellislab.com/forums/viewthread/71141/P15/#790715
     *
     * This should be fixed in CI 3.x, see:
     * @link https://github.com/EllisLab/CodeIgniter/pull/436
     *
     * @param CI_DB_result $queryResults The query result object.
     */
    protected function reallyFreeQueryResults ($queryResults)
    {
        if (is_object($queryResults->conn_id)) {
            mysqli_next_result($queryResults->conn_id);
        }

        $queryResults->free_result();
    }
}
