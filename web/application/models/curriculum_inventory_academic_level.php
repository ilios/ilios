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
     * Creates levels 1 - 10 for the given program year.
     * @param int $programYearId program year id
     * @param string $namePrefix the level name prefix, forms the level name by prepending the level number
     */
    public function createDefaultLevels($programYearId, $namePrefix = 'Year')
    {
        for ($i = 1; $i <= 10; $i++) {
            $name = $namePrefix . ' ' . $i;
            $this->create($programYearId, $i, $name);
        }
    }

    /**
     * Creates an academic level record.
     * @param int $programYearId the id of the associated program year id.
     * @param int $level
     * @param string $name
     * @param string $description
     * @return int|boolean the record's new primary key value, or FALSE on failure
     */
    public function create ($programYearId, $level, $name, $description = '')
    {
        $data = array();
        $data['program_year_id'] = $programYearId;
        $data['level'] = $level;
        $data['name'] = $name;
        $data['description'] = $description;

        if ($this->db->insert($this->databaseTableName, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Retrieves the academic levels for a given program year.
     * @param int $programYearId the program year id
     * @return array a nested array of arrays, each sub-array representing an academic level record
     */
    public function getList ($programYearId)
    {
        $rhett = array();
        $this->db->order_by('level', 'asc');
        $query = $this->db->get_where($this->databaseTableName, array('program_year_id' => $programYearId));
        if (0 < $query->num_rows()) {
            foreach ($query->result_array as $row)
            $rhett[] = $row;
        }
        $query->free_result();
        return $rhett;
    }
}