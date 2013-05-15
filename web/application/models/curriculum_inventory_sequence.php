<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_sequence" table.
 */
class Curriculum_Inventory_Sequence extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_sequence', array('report_id'));
    }

    /**
     * Creates a new sequence.
     * @param int $reportId
     * @param string $description
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function create ($reportId, $description = '')
    {
        $data = array();
        $data['report_id'] = $reportId;
        $data['description'] = $description;
        return $this->db->insert($this->databaseTableName, $data);
    }

    /**
     * Updates a sequence with the given data.
     * @param int $reportId the report_id id
     * @param string $description the sequence description
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function update ($reportId, $description = '')
    {
        $data = array();
        $data['description'] = $description;
        $this->db->where('report_id', $reportId);
        return $this->db->update($this->databaseTableName, $data);
    }

    /**
     * Deletes the sequence for a given report.
     * @param int $reportId The report id.
     */
    public function delete ($reportId)
    {
        $this->db->delete($this->databaseTableName, array('report_id' => $reportId));
    }
}
