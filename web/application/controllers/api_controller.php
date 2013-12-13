<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * API key controller
 */
class API_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Index action.
     */
    public function index ()
    {
        $this->get_api_key();
    }


}
