<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_program" table.
 */
class Curriculum_Inventory_Program extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_program', array('program_year_id'));
    }

    /**
     * Creates a new curriculum inventory program record for a given program year id.
     * @param int $programYearId the ilios program year id.
     * @return boolean TRUE on success, FALSE on failure
     */
    public function create ($programYearId)
    {
        $data = array();
        $data['program_year_id'] = $programYearId;
        return $this->db->insert($this->databaseTableName, $data);
    }

    /**
     * Lists all curriculum inventory programs associated with a given Ilios program.
     * Note "deleted" programs and program-years are omitted.
     * @param in $programId the program id
     * @return array
     */
    public function listByProgram ($programId)
    {
        $rhett = array();
        $this->db->select("{$this->databaseTableName}.*");
        $this->db->from($this->databaseTableName);
        $this->db->join("program_year", "program_year.program_year_id = {$this->databaseTableName}.program_year_id");
        $this->db->join("program", "program.program_id = program_year.program_id");
        $this->db->where("program.program_id", $programId);
        $this->db->where("program.deleted = 0");
        $this->db->where("program_year.deleted = 0");
        $query = $this->db->get();
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }
}