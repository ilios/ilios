<?php
/**
 * @package Ilios
 * 
 * Application Status Controller.
 * Its main purpose is to provide information about the general status of the application to
 * health monitoring systems (such as NAGIOS).
 */
class Status extends CI_Controller
{

  /**
   * Performs an application health check.
   * This includes checks
   * - if the database server can be reached
   * - if upload-directories are writeable by the web-server process.
   * Prints out a status report.
   * @return string the status page
   */
    public function index ()
    {
        // no authn!

        // get status details by running various checks
        $statusDetails = array();
        $statusDetails['Can read from database'] = $this->_checkDatabaseConnection();
        $statusDetails['Can write to learning materials directory'] = $this->_checkIfLearningMaterialsDirIsWriteable();
        $statusDetails['Can write to temporary uploads directory'] =  $this->_checkIfTempUploadsDirIsWriteable();

        // get the overall status by logical conjunction if individual statuses
        $overallStatus = array_reduce(array_values($statusDetails), function (&$result, $item) {
            return ($result && $item);
        }, true);

        $data['overall'] = $overallStatus;
        $data['details'] = $statusDetails;

        $this->load->view('status/index', $data);
    }

    /**
     * Checks the connection to the db server by running a query against it.
     * @return boolean TRUE on success, FALSE on failure
     */
    protected function _checkDatabaseConnection ()
    {
        // this query comes back empty-handed on:
        // - failed connection to the db server
        // - failed authentication against db server
        // - missing database
        // - empty database
        // that's "close enough" for now. [ST 2013/03/14]
        $result = $this->db->query('SHOW TABLES');
        return (0 < $result->num_rows);
    }

    /**
     * Checks if the learning materials directory is writeable.
     * @return boolean TRUE on success, FALSE on failure
     */
    protected function _checkIfLearningMaterialsDirIsWriteable ()
    {
        $path = dirname(dirname(__DIR__)) . '/learning_materials';
        return is_really_writable($path);
    }

    /**
     * Checks if the temporary uploads directory is writeable.
     * @return boolean TRUE on success, FALSE on failure
     */
    protected function _checkIfTempUploadsDirIsWriteable ()
    {
        $path =  dirname(dirname(__DIR__)) . '/tmp_uploads';
        return is_really_writable($path);
    }
}
