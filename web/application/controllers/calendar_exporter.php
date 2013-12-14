<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'lios_web_controller.php';

/**
 * @package Ilios
 * Calendar exporter controller.
 */
class Calendar_Exporter extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();

    }

    /**
     * Index action.
     */
    public function index ()
    {
        // Default to export in ICalendar format
        $this->exportICalendar();
    }


}
