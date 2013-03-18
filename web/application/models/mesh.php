<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) to the "mesh" tables.
 */
class Mesh extends Abstract_Ilios_Model
{

    public function __construct ()
    {
        parent::__construct('mesh_descriptor', array('mesh_descriptor_uid'));
    }

    /**
     * @todo add code docs
     */
    public function saveMeSHSearchSelection ($pairArray)
    {
        $count = 0;
        $affectedCount = 0;
        foreach ($pairArray as $pair) {
            if (! is_null($pair['uid'])) {
                $newRow = array();
                $newRow['mesh_user_selection_id'] = null;

                $newRow['mesh_descriptor_uid'] = $pair['uid'];
                $newRow['search_phrase'] = $pair['searchTerm'];

                $this->db->insert('mesh_user_selection', $newRow);

                $affectedCount += $this->db->affected_rows();
                $count++;
            }
        }

        return ($count == $affectedCount);
    }

    /*
     * @return a JSON'd array of the following:
     *
     * server response to mesh query:
     *      'previous_searches' matches to previous user searches
     *                              previous user search term
     *                              mesh object
     *      'search_results'    matches in the mesh universe
     *                              mesh object
     *
     * mesh object:
     *      'name'          descriptor name
     *      'uid            descriptor uid
     *      'tree_path'     mesh tree
     *      'scope_notes'   array of descriptor's concepts' scope-notes
     *
     * mesh tree:
     *      list of {tree number, name}, {tree number, name}, ... going from root to specific object
     */
    public function searchMeSHUniverseForIlios ($searchString)
    {
        $rhett = array();

        if (! strpos($searchString, " ")) {
            $meshSearchProcedureInvocation = 'CALL mesh_search(\'+' . addslashes($searchString)
                                                . '*\')';
        }
        else {
            $meshSearchProcedureInvocation = 'CALL mesh_search(\'+"' . addslashes($searchString)
                                                . '"\')';
        }

        $previousSearches = array();
        $this->db->like('search_phrase', $searchString);
        $queryResults = $this->db->get('mesh_user_selection');

        foreach ($queryResults->result_array() as $row) {
            $previousSearch = array();

            $previousSearch['searched_term'] = $row['search_phrase'];
            $previousSearch['mesh_object']
                                   = $this->getMeSHObjectForDescriptor($row['mesh_descriptor_uid']);

            array_push($previousSearches, $previousSearch);
        }

        $queryResults = $this->db->query($meshSearchProcedureInvocation);
        // if we don't take all these from memory and attempt to use active record midst retrieving
        //      these stored proc results, there will be sql unhappiness.. this is not great for
        //      though memory
        $rowResults = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($rowResults, $row);
        }
        $this->reallyFreeQueryResults($queryResults);

        $searchResults = array();
        foreach ($rowResults as $row) {
            if (! isset($searchResults[$row['uid']])) {
                $searchResults[$row['uid']] = $this->getMeSHObjectForDescriptor($row['uid'],
                                                                                $row['tree_number']);
            }
        }

        usort($previousSearches, array($this, "previousSearchComparator"));
        usort($searchResults, array($this, "universeSearchComparator"));

        $rhett['previous_searches'] = $previousSearches;
        $rhett['search_results'] = $searchResults;

        $realRhett = array();
        $realRhett['results'] = $rhett;

        return $realRhett;
    }

    /*
     *      'name'          descriptor name
     *      'uid            descriptor uid
     *      'tree_path'     mesh tree
     *      'scope_notes'   array of descriptor's concepts' scope-notes
     */
    public function getMeSHObjectForDescriptor ($descriptorUniqueId, $treeNumber = null)
    {
        $rhett = array();

        $this->db->where('mesh_descriptor_uid', $descriptorUniqueId);
        $queryResults = $this->db->get('mesh_descriptor');
        $row = $queryResults->first_row();

        $rhett['uid'] = $descriptorUniqueId;
        $rhett['name'] = $row->name;

        $conceptIds = array();
        $this->db->where('mesh_descriptor_uid', $descriptorUniqueId);
        $queryResults = $this->db->get('mesh_descriptor_x_concept');
        foreach ($queryResults->result_array() as $row) {
            array_push($conceptIds, $row['mesh_concept_uid']);
        }
        $scopeNotes = array();
        foreach ($conceptIds as $conceptId) {
            $this->db->where('mesh_concept_uid', $conceptId);
            $queryResults = $this->db->get('mesh_concept');
            foreach ($queryResults->result_array() as $row) {
                if (! is_null($row['scope_note'])) {
                    array_push($scopeNotes, $row['scope_note']);
                }
            }
        }
        $rhett['scope_notes'] = $scopeNotes;

        $treePath = array();
        if (! is_null($treeNumber)) {
            $meshTreeDecomposeInvocation = 'CALL decompose_mesh_tree("' . $treeNumber . '")';
            $queryResults = $this->db->query($meshTreeDecomposeInvocation);
            foreach ($queryResults->result_array() as $row) {
                array_push($treePath, $row);
            }
            $this->reallyFreeQueryResults($queryResults);
        }
        $rhett['tree_path'] = $treePath;

        return $rhett;
    }

    /*
     *      'tree_number'   tree number
     *      'name'          descriptor name
     *      'uid            descriptor uid
     *      'tree_path'     mesh tree
     */
    protected function getMeSHObjectForTreeNumber ($treeNumber, $descriptorUniqueId = null,
                                                   $descriptorName = null, $matchingTable = null,
                                                   $matchingUID = null) {
        $rhett = array();

        if (is_null($descriptorUniqueId)) {
            $this->db->where('tree_number', $treeNumber);
            $queryResults = $this->db->get('mesh_tree_x_descriptor');
            foreach ($queryResults->result_array() as $row) {
                $descriptorUniqueId = $row['mesh_descriptor_uid'];
            }

            $this->db->where('mesh_descriptor_uid', $descriptorUniqueId);
            $queryResults = $this->db->get('mesh_descriptor');
            foreach ($queryResults->result_array() as $row) {
                $descriptorName = $row['name'];
            }
        }
/*
    mesh object:
        . tree number
        . descriptor name
        . descriptor uid
        . mesh tree
 */

        $rhett['tree_number'] = $treeNumber;
        $rhett['uid'] = $descriptorUniqueId;
        $rhett['name'] = $descriptorName;

        if (! is_null($matchingTable)) {
            $rhett['match_table'] = $matchingTable;
            $rhett['match_uid'] = $matchingUID;
        }

        $meshTreeDecomposeInvocation = 'CALL decompose_mesh_tree("' . $treeNumber . '")';
        $queryResults = $this->db->query($meshTreeDecomposeInvocation);
        $treePath = array();
        foreach ($queryResults->result_array() as $row) {
            array_push($treePath, $row);
        }
        $this->reallyFreeQueryResults($queryResults);

        $rhett['tree_path'] = $treePath;


        return $rhett;
    }

    /**
     * @todo add code docs
     */
    protected function previousSearchComparator ($a, $b)
    {
        $meshObjA = $a['mesh_object'];
        $meshObjB = $b['mesh_object'];

        return strcmp($meshObjA['name'], $meshObjB['name']);
    }

    /**
     * @todo add code docs
     */
    protected function universeSearchComparator ($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    }
}
