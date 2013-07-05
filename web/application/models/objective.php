<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) to the "objective" tables.
 */
class Objective extends Ilios_Base_Model
{

    public function __construct ()
    {
        parent::__construct('objective', array('objective_id'));

        $this->load->model('Competency', 'competency', TRUE);
        $this->load->model('Mesh', 'mesh', TRUE);
    }

    /**
     * Retrieves an objective and associated data (MeSH terms, parent objectives, competency titles) by its given id.
     * @param int $objectiveId the objective id
     * @param boolean $includeCompetencies pass TRUE to include parent competency information
     * @return array an associated array representing the objective and related data
     * @todo clean up this hideous mess
     */
    public function getObjective ($objectiveId, $includeCompetencies = false)
    {
        $rhett = $this->convertStdObjToArray($this->getRowForPrimaryKeyId($objectiveId));

        $rhett['mesh_terms'] = array();
        $crossIdArray = $this->getIdArrayFromCrossTable('objective_x_mesh',
            'mesh_descriptor_uid', 'objective_id', $objectiveId);
        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                array_push($rhett['mesh_terms'], $this->mesh->getMeSHObjectForDescriptor($id));
            }
        }

        $rhett['parent_objectives'] = array();
        $crossIdArray = $this->getIdArrayFromCrossTable('objective_x_objective',
            'parent_objective_id', 'objective_id', $objectiveId);

        if ($includeCompetencies) {
            $rhett['parent_competencies'] = array();
        }

        $competencyCache = array();

        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                if ($includeCompetencies) {
                    $objectiveRow = $this->getRowForPrimaryKeyId($id);
                    if (0 < (int) $objectiveRow->competency_id) {
                        $competencies = array();
                        // associated competency
                        if (! array_key_exists($objectiveRow->competency_id, $competencyCache)) {
                            $competencyRow = $this->competency->getRowForPrimaryKeyId($objectiveRow->competency_id);
                            $competencyCache[$competencyRow->competency_id] = $competencyRow;
                        }
                        $competency = $competencyCache[$objectiveRow->competency_id];
                        $competencies[] = $competency;
                        // parent-competency
                        if (0 < (int) $competency->parent_competency_id) {
                          if (! array_key_exists($competency->parent_competency_id, $competencyCache)) {
                              $competencyRow = $this->competency->getRowForPrimaryKeyId($competency->parent_competency_id);
                              $competencyCache[$competencyRow->competency_id] = $competencyRow;
                          }
                          $competency = $competencyCache[$competency->parent_competency_id];
                          $competencies[] = $competency;
                        }
                        $rhett['parent_competencies'][] = $competencies;
                    } else {
                        $rhett['parent_competencies'][] = array();
                    }
                }
                $rhett['parent_objectives'][] = $id;
            }
        }

        return $rhett;
    }

    /*
     * Transactions are assumed to be handled outside this block
     *
     * TODO error detection
     */
    public function addNewObjective ($objectiveObject, &$auditAtoms)
    {
        $newRow = array();
        $newRow['objective_id'] = null;
        $newRow['title'] = $objectiveObject['title'];
        $newRow['competency_id'] = $objectiveObject['competencyId'] ? $objectiveObject['competencyId'] : null;

        $this->db->insert($this->databaseTableName, $newRow);

        $objectiveId = $this->db->insert_id();

        array_push($auditAtoms, $this->auditEvent->wrapAtom($objectiveId, 'objective_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::CREATE_EVENT_TYPE));

        if ($objectiveId != -1) {
            $mockObjectArray = array();
            foreach ($objectiveObject['parentObjectives'] as $key => $val) {
                    $parentObjective = array();
                    $parentObjective['dbId'] = $val;
                    array_push($mockObjectArray, $parentObjective);
            }

            $this->performCrossTableInserts($objectiveObject['meshTerms'], 'objective_x_mesh',
                                            'mesh_descriptor_uid', 'objective_id', $objectiveId);
            $this->performCrossTableInserts($mockObjectArray, 'objective_x_objective',
                                            'parent_objective_id', 'objective_id', $objectiveId);
        }

        return $objectiveId;
    }

    /*
     * Transactions are assumed to be handled outside this block
     *
     * TODO error detection
     */
    public function updateObjective ($objectiveObject,  &$auditAtoms)
    {
        $updateRow = array();
        $updateRow['title'] = $objectiveObject['title'];
        $updateRow['competency_id'] = $objectiveObject['competencyId'] ? $objectiveObject['competencyId'] : null;

        $this->db->where('objective_id', $objectiveObject['dbId']);
        $this->db->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($objectiveObject['dbId'],
                                                            'objective_id',
                                                            $this->databaseTableName,
                                                            Ilios_Model_AuditUtils::UPDATE_EVENT_TYPE));

        $this->performCrossTableInserts($objectiveObject['meshTerms'], 'objective_x_mesh',
                                        'mesh_descriptor_uid', 'objective_id',
                                        $objectiveObject['dbId']);

        $mockObjectArray = array();
        foreach ($objectiveObject['parentObjectives'] as $key => $val) {
            $parentObjective = array();
            $parentObjective['dbId'] = $val;
            array_push($mockObjectArray, $parentObjective);
        }
        $this->performCrossTableInserts($mockObjectArray, 'objective_x_objective',
                                        'parent_objective_id', 'objective_id',
                                        $objectiveObject['dbId']);
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
    public function saveObjectives (array $objectives, $crossTableName, $crossTableColumn, $columnValue, &$auditAtoms)
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

        foreach ($objectives as $val) {
            $dbId = $val['dbId'];

            if ($dbId == -1) {
                $dbId = $this->addNewObjective($val, $auditAtoms);
            } else {
                $this->updateObjective($val, $auditAtoms);
            }

            $newId = array();
            $newId['dbId'] = $dbId;
            $newId['md5'] = $val['cachedMD5'];

            $rhett[] = $newId;
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
    public function rolloverObjectives ($crossTableName, $crossTableRowName, $crossTableId, $newCrossTableId,
                                        $rolloverIsSameAcademicYear, $parentMap = null)
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
            $objectiveRow = $this->getRowForPrimaryKeyId($objectiveId);

            $newRow = array();
            $newRow['objective_id'] = null;

            $newRow['title'] = $objectiveRow->title;
            $newRow['competency_id'] = $rolloverIsSameAcademicYear ? $objectiveRow->competency_id
                : null;

            $this->db->insert($this->getTableName(), $newRow);
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
}
