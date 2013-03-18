<?php

/**
 * This abstract class embodies the common functionality featured across all Ilios model objects
 *
 * TODO nead a serialize and unserialized methods for the audit event functionality to work; make
 *          sure we're not re-inventing the wheel
 */
abstract class Abstract_Ilios_Model extends CI_Model
{

    /**
     * Minimum number of characters that a given search term must possess
     * in order for wildcard search to take precedence of exact term matching.
     * @var int
     */
    const WILDCARD_SEARCH_CHARACTER_MIN_LIMIT = 3;

    protected $databaseTableName;
    protected $databaseTablePrimaryKeyArray;

    /**
     * @param string $tableName the name of the table within the database which this model represents
     * @param array $primaryKeyArray an array of 0-N primary keys for the associated table
     */
    public function __construct ($tableName = 'none', $primaryKeyArray = array())
    {
        parent::__construct();
        $this->databaseTableName = $tableName;
        $this->databaseTablePrimaryKeyArray = $primaryKeyArray;
    }

    /**
     * @todo add code docs
     */
    public function getTableName ()
    {
        return $this->databaseTableName;
    }

    /**
     * @todo add code docs
     */
    public function startTransaction ()
    {
        $this->db->trans_start();
    }

    /**
     * @todo add code docs
     */
    public function transactionAtomFailed ()
    {
        return ($this->db->trans_status() === FALSE);
    }

    /**
     * @todo add code docs
     */
    public function commitTransaction ()
    {
        $this->db->trans_commit();
    }

    /**
     * @todo add code docs
     */
    public function rollbackTransaction ()
    {
        $this->db->trans_rollback();
    }

    // many subclasses will want to return entire rows based on title closeness match, so we
    //      provide that functionality here
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
     * @return null if the $id does not have a row, false if the row is not published (its
     *              publish_event_id column value is NULL), or true if it is published
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

    // another common subclass method - this uses the $this->databaseTablePrimaryKeyArray[0]
    //      as the column name. returns null if no row exists for the id
    public function getRowForPrimaryKeyId ($id, $checkForDelete = false, $schoolId = null)
    {
        return $this->getRow($this->databaseTableName, $this->databaseTablePrimaryKeyArray[0], $id, $checkForDelete, $schoolId);
    }

    /**
     * @todo add code docs
     */
    public function getRow ($tableName, $rowName, $id, $checkForDelete = false, $schoolId = null)
    {
        $queryResults = null;

        $this->db->where($rowName, $id);
        if ($checkForDelete) {
            $this->db->where('deleted', 0);
        }

        if ($schoolId != null) {
            $this->db->where('owning_school_id', $schoolId);
        }

        $queryResults = $this->db->get($tableName);

        if (is_null($queryResults) || ($queryResults->num_rows() == 0)) {
            return null;
        }

        return $queryResults->first_row();
    }

    /**
     * @return an array of rows each of which are the row contents from the database table
     */
    public function getTableContentsAsArray ($checkForDelete = false)
    {
        $rhett = array();

        if ($checkForDelete) {
            $this->db->where('deleted', 0);
        }

        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            array_push($rhett, $row);
        }

        return $rhett;
    }

    /**
     * Returns the language key as specified in the application configuration.
     * @see Abstract_Ilios_Controller::getLangToUse()
     * @return string
     */
    protected function getLangToUse ()
    {
        return $this->config->item('ilios_default_lang_locale');
    }

    /**
     * @todo add code docs
     * @param string $table
     * @param string $column
     * @param string $uniquingColumn
     * @param int|string $uniquingId
     * @return array|NULL
     */
    protected function getIdArrayFromCrossTable ($table, $column, $uniquingColumn, $uniquingId) {
        $queryResults = null;
        $rhett = null;

        $this->db->select($column);
        $this->db->where($uniquingColumn, $uniquingId);
        $queryResults = $this->db->get($table);

        if ($queryResults->num_rows() > 0) {
            $rhett = array();

            foreach ($queryResults->result_array() as $row) {
                array_push($rhett, $row[$column]);
            }
        }

        return $rhett;
    }

    /**
     * @return checks to see if the couplet-exists and inserts it if not; returns 1 if it already
     *              existed or the insert was successful, 0 on an error
     */
    protected function insertSingleCrossTablePair ($tableName, $keyA, $valueA, $keyB, $valueB) {
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
     * Saves associations between a given record in table A and given records in table B
     * in a given JOIN table.
     * - new associations will be added
     * - missing associations will be removed
     * @param string $joinTblName name of the JOIN table
     * @param string $joinColName name of the column in the JOIN table that references the given record id in table A
     * @param int $joinId the record id from table A
     * @param string $refColName name of the column in the JOIN table that references given record ids in table B
     * @param array $refData the records in table B
     * @param array|NULL $existingAssocIds record ids of already associated records in table B
     * @param string $idName key attribute name of {$refData} elements
     * @param array $auditAtoms auditing trail
     */
    protected function _saveJoinTableAssociations ($joinTblName, $joinColName, $joinId,
            $refColName, $refData = array(), $existingAssocIds = array(), $idName = 'dbId', array &$auditAtoms = array())
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
     * @param string $joinTblName name of the JOIN table
     * @param string $joinColName name of the column in the JOIN table that references the given record id in table A
     * @param int $joinId the record id from table A
     * @param string $refColName name of the column in the JOIN table that references given record ids in table B
     * @param array $refIds the record ids in table B
     * @param array $auditAtoms auditing trail
     */
    protected function _associateWithJoinTable ($joinTblName, $joinColName,
            $joinId, $refColName, array $refIds = array(), array &$auditAtoms = array())
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
        	$auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($joinId,
        			$joinColName, $joinTblName, Ilios_Model_AuditUtils::CREATE_EVENT_TYPE);
        }
    }

    /**
     * Removes any associations between a given record in table A
     * with given records in table B from a JOIN table.
     * @param string $joinTblName name of the JOIN table
     * @param string $joinColName name of the column in the JOIN table that references the given record id in table A
     * @param int $joinId the record id from table A
     * @param string $refColName name of the column in the JOIN table that references given record ids in table B
     * @param array $refIds the record ids in table B
     * @param array $auditAtoms auditing trail
     */
    protected function _unassociateFromJoinTable ($joinTblName, $joinColName,
            $joinId, $refColName, array $refIds = array(), array &$auditAtoms = array())
    {
        $refIds = array_unique($refIds); // de-dupe
        $refIds = array_filter($refIds); // remove falsy values -  this gets rid of NULLs
        if (count($refIds)) {
        	$this->db->where($joinColName, $joinId);
        	$this->db->where_in($refColName, $refIds);
        	$this->db->delete($joinTblName);
        	$auditAtoms[] = Ilios_Model_AuditUtils::wrapAuditAtom($joinId,
        	        $joinColName, $joinTblName, Ilios_Model_AuditUtils::DELETE_EVENT_TYPE);
        }
    }


    /**
     * Removes any entries between given tables A and B for a given record in A
     * from a given JOIN table.
     * Then (re)associates given records in table B with that record in table A.
     *
     * Motto: "delete all, then re-enter them again."
     *
     * @deprecated
     * Use Abstract_Ilios_Model::_saveJoinTableAssociations() instead.
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
    protected function performCrossTableInserts (array $modelArray, $tableName, $columnName,
    		$uniquingColumn, $uniquingId, $idColumnName = 'dbId') {
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
     * @param array $objectives
     * @param string $crossTableName
     * @param string $crossTableColumn
     * @param mixed $columnValue
     * @param array $auditAtoms
     * @return array
     */
    protected function _saveObjectives (array $objectives, $crossTableName, $crossTableColumn,
        $columnValue, &$auditAtoms)
    {
        $rhett = array();

        // get the ids of currently associated objectives from the JOIN table
        $existingObjectiveIds = $this->getIdArrayFromCrossTable($crossTableName, 'objective_id', $crossTableColumn, $columnValue);

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

    protected function rolloverObjectives ($crossTableName, $crossTableRowName, $crossTableId,
                                           $newCrossTableId, $rolloverIsSameAcademicYear,
                                           $parentMap = null) {
        $objectiveIdPairs = array();

        $shouldCopyParentAttributes = ($rolloverIsSameAcademicYear || ($parentMap != null));

        $this->db->where($crossTableRowName, $crossTableId);
        $queryResults = $this->db->get($crossTableName);
        $objectiveIds = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($objectiveIds, $row['objective_id']);
        }

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

            array_push($objectiveIdPairs, $pair);
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
                $queryResults = $this->db->get('objective_x_objective');

                $updateList = array();
                foreach ($queryResults->result_array() as $row) {
                    foreach ($parentMap as $parentObjectIdPair) {
                        if ($parentObjectIdPair['original'] == $row['parent_objective_id']) {
                            $updateTriplet = array();
                            $updateTriplet['oid'] = $objectiveIdPair['new'];
                            $updateTriplet['original_poid'] = $parentObjectIdPair['original'];
                            $updateTriplet['new_poid'] = $parentObjectIdPair['new'];

                            array_push($updateList, $updateTriplet);
                        }
                    }
                }

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
     * Of use when we get back a std obj from a db query (like via the first_row() method) and we
     *  want to ship an array back to the client. Sucks to have this both in this hierarchy and
     *  the controller hierarchy. Ditch the controller one and change references to this.. TODO
     */
    protected function convertStdObjToArray ($stdObj) {
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
     * In multiple query results involving a stored procedure call, call this between queries.
     */
    protected function reallyFreeQueryResults ($queryResults)
    {
        if (is_object($queryResults->conn_id)) {
            mysqli_next_result($queryResults->conn_id);
        }

        $queryResults->free_result();
    }
}
