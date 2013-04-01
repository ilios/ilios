<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * Curriculum Inventory management controller.
 */
class Curriculum_Inventory_Manager extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Default action, alias for "view".
     * @see Curriculum_Inventory_Manager::view()
     */
    public function index ()
    {
        $this->view();
    }

    public function view ()
    {
        // @todo implement
    }

    public function add ()
    {
        // @todo implement
    }

    public function preview ()
    {
        // @todo implement
    }

    public function save ()
    {
        // @todo implement
    }

    public function export ()
    {
        // @todo implement
    }
}