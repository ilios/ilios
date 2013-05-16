<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_academic_level" table.
 */
class Curriculum_Inventory_Academic_Level extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_academic_level', array('curriculum_inventory_academic_level_id'));
    }

    /**
     * Creates levels 1 - 10 for the given report.
     * @param int $reportId report id
     * @param string $namePrefix the level name prefix, forms the level name by prepending the level number
     */
    public function createDefaultLevels($reportId, $namePrefix = 'Year')
    {
        for ($i = 1; $i <= 10; $i++) {
            $name = $namePrefix . ' ' . $i;
            $this->create($reportId, $i, $name);
        }
    }

    /**
     * Creates an academic level record.
     * @param int $reportId the id of the associated report
     * @param int $level
     * @param string $name
     * @param string $description
     * @return int|boolean the record's new primary key value, or FALSE on failure
     */
    public function create ($reportId, $level, $name, $description = '')
    {
        $data = array();
        $data['report_id'] = $reportId;
        $data['level'] = $level;
        $data['name'] = $name;
        $data['description'] = $description;

        if ($this->db->insert($this->databaseTableName, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Retrieves the academic levels for a given report.
     * @param int $reportId the report id
     * @return array an associative array academic levels, keyed off by level id
     */
    public function getLevels ($reportId)
    {
        $rhett = array();
        $this->db->order_by('level', 'asc');
        $query = $this->db->get_where($this->databaseTableName, array('report_id' => $reportId));
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row)
            $rhett[$row['academic_level_id']] = $row;
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves the academic levels that were applied to the inventory for a given report.
     * @param int $reportId the report id
     * @return array an associative array academic levels, keyed off by level id
     */
    public function getAppliedLevels ($reportId)
    {
        $rhett = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<< EOL
SELECT DISTINCT al.*
FROM curriculum_inventory_academic_level al
JOIN curriculum_inventory_sequence_block sb ON sb.academic_level_id = al.academic_level_id
WHERE sb.report_id = {$clean['report_id']}
ORDER BY al.level ASC
EOL;
        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row)
                $rhett[$row['academic_level_id']] = $row;
        }
        $query->free_result();
        return $rhett;
    }
}
