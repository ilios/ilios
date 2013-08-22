<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Add the new academic year table and backfill it with the default values.
 * NOTE: you will probably want to change the values of the start and end date
 * values when applicable
 */
class Migration_Add_and_populate_academic_year_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

	//Drop the table (just in case it's there)
        $sql = "DROP TABLE IF EXISTS `academic_year`";
        $this->db->query($sql);
	//Create the table
        $sql =<<<EOL
CREATE TABLE `academic_year` (
  `academic_year_id` SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE KEY,
  `school_id` INT(10) NOT NULL,
  `academic_year_start_date` DATETIME NOT NULL,
  `academic_year_end_date` DATETIME NOT NULL,
  `start_year` SMALLINT(4) NOT NULL,
  PRIMARY KEY `school_start_year` (`school_id`,`start_year`),
  INDEX `school_year_start_date_end_date` (`school_id`,`start_year`,`academic_year_start_date`,`academic_year_end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOL;
        $this->db->query($sql);
	
	//create a temporary table to get all the years used by each school
	$sql = "DROP TABLE IF EXISTS migration_academic_years_by_school";
        $this->db->query($sql);
	$sql = "CREATE TABLE migration_academic_years_by_school AS SELECT `year`, `owning_school_id` FROM `course` GROUP BY `owning_school_id`, `year`";
        $this->db->query($sql);
	
	// using the new table, populate the table with pre-determined datetimes (YYYY-07-01 00:00:00 - [YYYY + 1]-06-30 23:59:59)
$sql =<<<EOL
INSERT INTO academic_year (school_id, academic_year_start_date, academic_year_end_date, start_year) SELECT owning_school_id, CONCAT(`year`, '-07-01 00:00:00'), CONCAT((`year`+1), '-06-30 23:59:59'), `year` FROM migration_academic_years_by_school ORDER BY `year`, owning_school_id
EOL;
        $this->db->query($sql);

	//drop the temporary migration table
	$sql = "DROP TABLE migration_academic_years_by_school";
        $this->db->query($sql);
        
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        // remove the foreign key constraints
	$sql = "DROP TABLE IF EXISTS academic_year";
        $this->db->query($sql);
        
	$this->db->trans_complete();
    }
}
