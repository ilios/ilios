<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_sequence_block_session" table.
 */
class Curriculum_Inventory_Sequence_Block_Session extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_sequence_block_session', array('sequence_block_session_id'));
    }


    /**
     * Retrieves a sequence block session by its given id.
     * @param int $sequenceBlockSessionId The sequence block session id.
     * @return array|boolean An associative array representing the sequence block record, or FALSE if none was found.
     */
    public function get ($sequenceBlockSessionId)
    {
        $rhett = false;
        $query = $this->db->get_where($this->databaseTableName, array('sequence_block_session_id' => $sequenceBlockSessionId));
        if (0 < $query->num_rows()) {
            $rhett = $query->row_array();
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the sequence block sessions associated with a given block.
     * @param int $blockId the block id
     * @return array of sequence block sessions indexed by sequence_block_session_id
     */
    public function getSessions ($blockId)
    {
        $rhett = array();
        $this->db->select('*');
        $this->db->from($this->getTableName());
        $this->db->where('sequence_block_id =', $blockId);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $rhett[$row['sequence_block_session_id']] = $row;
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Clears all the sessions for a sequence block
     * @param int $blockId the block id
     * @return int number of affected rows
     */
    public function clearSessionsForBlock ($blockId)
    {
        $this->db->where('sequence_block_id =', $blockId);
        $this->db->delete($this->getTableName());

        return $this->db->affected_rows();
    }

    /**
     * Creates a new sequence block session.
     * Note: Input validation according to business rules, like type- and range-checking, is assumed to happen further
     * upstream. In other words, this function expects validated input.
     *
     * @param int $sequenceBlockId
     * @param int $sessionId
     * @param boolean $countOfferingsOnce
     * @return int|boolean the new sequence block session id on success, or FALSE on failure
     */
    public function create ($sequenceBlockId, $sessionId, $countOfferingsOnce)
    {
        $data = array();
        $data['sequence_block_id'] = $sequenceBlockId;
        $data['session_id'] = $sessionId;
        $data['count_offerings_once'] = $countOfferingsOnce;

        if ($this->db->insert($this->databaseTableName, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Updates a sequence block session with given data.
     * Note: Input validation according to business rules, like type- and range-checking, is assumed to happen further
     * upstream. In other words, this function expects validated input.
     *
     * @param int $sequenceBlockId
     * @param array $data An associative array containing the updated values keyed off by column name.
     *    May contain values entries for the following keys:
     *   "sequence_block_id", "session_id", "count_offerings_once"
     */
    public function update ($sequenceBlockSessionId, array $data)
    {
        $this->db->where('sequence_block_session_id', $sequenceBlockSessionId);
        $this->db->update($this->databaseTableName, $data);

    }


    /**
     * Deletes a given sequence block session
     * @param int $sequenceBlockId The sequence block id.
     */
    public function delete ($sequenceBlockSessionId)
    {
        $this->db->delete($this->databaseTableName, array('sequence_block_session_id' => $sequenceBlockSessionId));
    }
}
