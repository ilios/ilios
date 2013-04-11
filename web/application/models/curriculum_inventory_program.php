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
     * Creates a new curriculum inventory program.
     * @param int $programYearId the corresponding Ilios program year id.
     * @param string $reportName the program-report name
     * @param string $reportDescription the program-report description
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return boolean TRUE on success, FALSE on failure
     */
    public function create ($programYearId, $reportName = '', $reportDescription = '', DateTime $startDate = null, DateTime $endDate = null)
    {
        $data = array();
        $data['program_year_id'] = $programYearId;
        $data['report_name'] = $reportName;
        $data['report_description'] = $reportDescription;
        if (isset($startDate)) {
            $data['start_date'] = $startDate->format('Y-m-d');
        }
        if (isset($endDate)) {
            $data['end_date'] = $endDate->format('Y-m-d');
        }
        return $this->db->insert($this->databaseTableName, $data);
    }

    /**
     * Updates a curriculum inventory program.
     * @param int $programYearId the corresponding Ilios program year id.
     * @param string $reportName the program-report name
     * @param string $reportDescription the program-report description
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return boolean TRUE on success, FALSE on failure
     */
    public function update ($programYearId, $reportName = '', $reportDescription = '', DateTime $startDate = null, DateTime $endDate = null) {
        $data = array();
        $data['report_name'] = $reportName;
        $data['report_description'] = $reportDescription;
        if (isset($startDate)) {
            $data['start_date'] = $startDate->format('Y-m-d');
        }
        if (isset($endDate)) {
            $data['end_date'] = $endDate->format('Y-m-d');
        }
        $this->db->where('program_year_id', $programYearId);
        return $this->db->update($data);
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