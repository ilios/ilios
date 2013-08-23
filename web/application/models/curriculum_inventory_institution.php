<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_institution" table.
 */
class Curriculum_Inventory_Institution extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_institution', array('school_id'));
    }
}