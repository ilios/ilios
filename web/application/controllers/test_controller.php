<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * "Test" Controller.
 * While this controller does not provide any functionality itself, it is used to instantiate
 * a full CI environment for unit testing purposes.
 * @see tests/ci_bootstrap.php
 */
class Test_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Default action.
     */
    public function index ()
    {
        // do nothing
    }
}
