<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'ilios_cli_controller.php';

/**
 * @package Ilios
 *
 * Migrations controller.
 *
 * @link http://ellislab.com/codeigniter/user-guide/libraries/migration.html
 * @link http://ellislab.com/codeigniter/user-guide/general/cli.html
 */
class Migrate extends Ilios_Cli_Controller
{

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->library('migration');
    }

    /**
     * @see Migrate::current().
     */
    public function index()
    {
        $this->current();
    }

    /**
     * Runs the current migration as configured in <code>application/config/migration.php</code>.
     * Prints an error message on failure.
     * @see CI_Migration::current()
     */
    public function current ()
    {
        if (false === $this->migration->current()) {
            show_error($this->migration->error_string());
        }
    }

    /**
     * Runs the latest migration found in the filesystem.
     * Prints an error message on failure.
     * @see CI_Migration::latest()
     */
    public function latest ()
    {
        if (false === $this->migration->latest()) {
            show_error($this->migration->error_string());
        }
    }

    /**
     * Migrates up/down to a given version.
     * Prints an error message on failure.
     * @param int $version the version number
     * @see CI_Migration::version()
     */
    public function version ($version)
    {
        if (false === $this->migration->version($version)) {
            show_error($this->migration->error_string());
        }
    } 
}
