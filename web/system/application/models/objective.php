<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "objective" tables.
 */
class Objective extends Abstract_Ilios_Model
{

    public function __construct ()
    {
        parent::__construct('objective', array('objective_id'));

        $this->createDBHandle();

        $this->load->model('Competency', 'competency', TRUE);
        $this->load->model('Mesh', 'mesh', TRUE);
    }

    /**
     * Retrieves an objective and associated data (MeSH terms, parent objectives, competency titles) by its given id.
     * @param int $objectiveId the objective id
     * @param boolean $includeCompetencyTitles pass TRUE to include competency titles
     * @return array an associated array representing the objective and related data
     * @todo clean up this hideous mess
     */
    public function getObjective ($objectiveId, $includeCompetencyTitles = false)
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

        if ($includeCompetencyTitles) {
            $rhett['parent_competency_titles'] = array();
        }

        $competencyCache = array();

        if ($crossIdArray != null) {
            foreach ($crossIdArray as $id) {
                if ($includeCompetencyTitles) {
                    $objectiveRow = $this->getRowForPrimaryKeyId($id);
                    if (0 < (int) $objectiveRow->competency_id) {
                        $competencyTitles = array();
                        // associated competency
                        if (! array_key_exists($objectiveRow->competency_id, $competencyCache)) {
                            $competencyRow = $this->competency->getRowForPrimaryKeyId($objectiveRow->competency_id);
                            $competencyCache[$competencyRow->competency_id] = $competencyRow;
                        }
                        $competency = $competencyCache[$objectiveRow->competency_id];
                        $competencyTitles[] = $competency->title;
                        // parent-competency
                        if (0 < (int) $competency->parent_competency_id) {
                          if (! array_key_exists($competency->parent_competency_id, $competencyCache)) {
                              $competencyRow = $this->competency->getRowForPrimaryKeyId($competency->parent_competency_id);
                              $competencyCache[$competencyRow->competency_id] = $competencyRow;
                          }
                          $competency = $competencyCache[$competency->parent_competency_id];
                          $competencyTitles[] = $competency->title;
                        }
                        $rhett['parent_competency_titles'][] = $competencyTitles;
                    } else {
                        $rhett['parent_competency_titles'][] = array();
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
        $DB = $this->dbHandle;

        $newRow = array();
        $newRow['objective_id'] = null;

        $newRow['title'] = $objectiveObject['title'];
        $newRow['competency_id'] = $objectiveObject['competencyId'];

        $DB->insert($this->databaseTableName, $newRow);

        $objectiveId = $DB->insert_id();

        array_push($auditAtoms, $this->auditEvent->wrapAtom($objectiveId, 'objective_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$CREATE_EVENT_TYPE));

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
        $DB = $this->dbHandle;

        $updateRow = array();
        $updateRow['title'] = $objectiveObject['title'];
        $updateRow['competency_id'] = $objectiveObject['competencyId'];

        $DB->where('objective_id', $objectiveObject['dbId']);
        $DB->update($this->databaseTableName, $updateRow);

        array_push($auditAtoms, $this->auditEvent->wrapAtom($objectiveObject['dbId'],
                                                            'objective_id',
                                                            $this->databaseTableName,
                                                            Audit_Event::$UPDATE_EVENT_TYPE));

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

}