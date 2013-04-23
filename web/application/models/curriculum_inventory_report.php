<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_report" table.
 */
class Curriculum_Inventory_Report extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_report', array('report_id'));
    }

    /**
     * Creates a new curriculum inventory report for a given academic year and program.
     * @param int $year the academic year
     * @param string $programId the program identifier
     * @param string $name the report name
     * @param string $description the report description
     * @param DateTime $startDate the report start date
     * @param DateTime $endDate the report end date
     * @return int|boolean the new report id on success, or FALSE on failure
     */
    public function create ($year, $programId, $name = '', $description = '',
                            DateTime $startDate = null, DateTime $endDate = null)
    {
        $data = array();
        $data['year'] = $year;
        $data['program_id'] = $programId;
        $data['name'] = $name;
        $data['description'] = $description;
        if (isset($startDate)) {
            $data['start_date'] = $startDate->format('Y-m-d');
        }
        if (isset($endDate)) {
            $data['end_date'] = $endDate->format('Y-m-d');
        }

        if ($this->db->insert($this->databaseTableName, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Updates a given curriculum inventory report.
     * @param int $reportId the report id
     * @param string $name the report name
     * @param string $description the report description
     * @param DateTime $startDate the report start date
     * @param DateTime $endDate the report end date
     * @return boolean TRUE on success, FALSE on failure
     */
    public function update ($reportId, $name = '', $description = '',
                            DateTime $startDate = null, DateTime $endDate = null) {
        $data = array();
        $data['name'] = $name;
        $data['description'] = $description;
        if (isset($startDate)) {
            $data['start_date'] = $startDate->format('Y-m-d');
        } else {
            $data['start_date'] = null;
        }
        if (isset($endDate)) {
            $data['end_date'] = $endDate->format('Y-m-d');
        } else {
            $data['end_date'] = null;
        }
        $this->db->where('report_id', $reportId);
        return $this->db->update($data);
    }

    /**
     * Retrieves a curriculum inventory report for a given academic year and owning program.
     * @param int $year the academic year
     * @param int $programId the owning program id
     * @return null|stdClass the report object, or NULL if not found.
     */
    public function getByAcademicYearAndProgram ($year, $programId)
    {
        $rhett = null;
        $query = $this->db->get_where($this->databaseTableName, array('year' => $year, 'program_id' => $programId));
        if (0 < $query->num_rows()) {
            $rhett = $query->result();
        }
        $query->free_result();
        return $rhett;
    }
}