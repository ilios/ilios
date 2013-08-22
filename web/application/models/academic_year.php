<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object to the academic year table.
 */
class Academic_year extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('academic_year');
    }

    /**
     * Retrieves  the all academic years and their respective  start and end dates 
     * from the given schoolId
     *
     * @param int $school_id
     * @return array $rhett - school_id, start_year, academic_start_date, and academic_end_date
     * for this school.
     */
    public function getAllAcademicYearsFromSchoolId ($school_id)
    {
        $rhett = array(); 

        $this->db->select("start_year, academic_year_start_date, academic_year_end_date, school_id");
        $this->db->where('school_id', $school_id);
        
        $query = $this->db->get('academic_year');
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $rhett[$row->start_year]['school_id'] = $row->school_id;
                $rhett[$row->start_year]['start_year'] = $row->start_year;
                $rhett[$row->start_year]['academic_year_start_date'] = $row->academic_year_start_date;
                $rhett[$row->start_year]['academic_year_end_date'] = $row->academic_year_end_date;
            }
        }
        $query->free_result();
        
        return $rhett;
    }
    
	/**
	* Return the start and end dates for an academic year from startYear and school_id 
	*
	* @param int startYear as YYYY
	* @param int school_id
  	* @return Object|null $row - an object of academic year start and end dates, or null if not found
    */
    public function getAcademicYearStartAndEndDate($start_year, $school_id)
    {
        $row = null;

        $this->db->select("academic_year_start_date, academic_year_end_date");
        $this->db->where('start_year', $start_year);
        $this->db->where('school_id', $school_id);
        
        $query = $this->db->get('academic_year');
        
        //as the startyear and school_id are keyed together as UNIQUE, this should only ever return one row...
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
        }
        $query->free_result();
        
        return $row;
    }
}
