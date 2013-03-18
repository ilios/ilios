<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'ilios_base_controller.php';

/**
 * @package Ilios
 *
 * Base "CLI" Controller.
 * Extend from here for application controllers that handle request issued from the command line.
 * CLI-mode is required and enforced.
 */
abstract class Ilios_Cli_Controller extends Ilios_Base_Controller
{
    public function __construct ()
    {
        parent::__construct();
        // deny access if the controller was not called from the command line.
        if (! $this->input->is_cli_request()) {
            exit('Access Denied.');
        }
    }
}