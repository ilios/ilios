<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_export" table.
 */
class Curriculum_Inventory_Export extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_export', array('report_id'));
    }

    /**
     * Checks if a given report has already been exported.
     * @param int $reportId The report id.
     * @return boolean TRUE if an export exists, FALSE otherwise.
     */
    public function exists ($reportId)
    {
        $rhett = false;
        $this->db->where('report_id', $reportId);
        $this->db->from($this->databaseTableName);
        if ($this->db->count_all_results()) {
            $rhett = true;
        }
        return $rhett;
    }

    /**
     * Creates an report export record from the given report id, report document and user.
     * @param int $reportId The report id.
     * @param string $document The report document.
     * @param int $userId The id of the user that is creating the export.
     * @return boolean TRUE on successful creation, FALSE on failure.
     */
    public function create ($reportId, $document, $userId)
    {
        $data = array();
        $data['report_id'] = $reportId;
        $data['document'] = $document;
        $data['created_by'] = $userId;
        return $this->db->insert($this->databaseTableName, $data);
    }
}
