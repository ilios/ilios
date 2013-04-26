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

    /**
     * Searches reports by name for a given search term, and returns a list of matching reports.
     * @param int $schoolId The record ID of the school that owns the report's program.
     * @param string $term The search term.
     * @return array An array of search results. Each result item is an associative array representing a report.
     */
    public function search ($schoolId, $term = '')
    {
        $rhett = array();
        $len = strlen($term);
        $clean['school_id'] = (int) $schoolId;
        $sql =<<< EOL
SELECT
cir.*
FROM curriculum_inventory_report cir
JOIN program p ON p.program_id = cir.program_id
WHERE
p.owning_school_id = {$clean['school_id']}
EOL;
        if ($len) {
            $clean['search_term'] = $this->db->escape_like_str($term);
            if (Ilios_Base_Model::WILDCARD_SEARCH_CHARACTER_MIN_LIMIT > $len) {
                $sql .= " AND cir.name LIKE '{$clean['search_term']}%'";
            } else {
                $sql .= " AND cir.name LIKE '%{$clean['search_term']}%'";
            }
        }

        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }
}
