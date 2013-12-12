<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 *
 * Base user authentication controller.
 * It provides common functionality among classes that deal with
 * authentication
 *
 */
abstract class Base_Authentication_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('User', 'user', TRUE);
    }


}
