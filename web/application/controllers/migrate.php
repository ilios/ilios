<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Ilios
 *
 * Migration controller.
 * Works in CLI-mode only.
 * 
 * @link http://ellislab.com/codeigniter/user-guide/libraries/migration.html
 * @link http://ellislab.com/codeigniter/user-guide/general/cli.html
 */
class Migrate extends CI_Controller
{

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        // deny access if the controller was not called from the command line.
        if ($this->input->is_cli_request()) {
            exit('Access Denied.');
        }
    }

    /**
     * @see Migrate::current().
     */
    public function index()
    {
        $this->current();
    }

    /**
     * Runs the latest migration found in the filesystem.
     * Prints an error message on failure.
     * @see CI_Migration::latest()
     */
    public function latest ()
    {
        if (! $this->migration->latest()) {
            show_error($this->migration->error_string());
        }
    }

    /**
     * Runs the current migration as configured in <code>application/config/migration.php</code>.
     * Prints an error message on failure.
     * @see CI_Migration::current()
     */
    public function current ()
    {
        if (! $this->migration->current()) {
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
        if (! $this->migration->version($version)) {
            show_error($this->migration->error_string());
        }
    } 
}