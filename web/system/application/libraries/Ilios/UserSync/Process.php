<?php

/**
 * Base class for user synchronization processes.
 * Provides some utility functions.
 * Subclasses must implement _run() with their actual synchronization algorithm.
 * The sync process is kicked off my calling the run() method.
 *
 * @abstract
 * @todo evaluate whether this can be even further generalized into a base class that goes beyond the scope of user sync.
 * @todo flesh out code docs
 */
abstract class Ilios2_UserSync_Process
{
    /**
     * @var User_Sync_Exception
     */
    protected $_syncExceptionDao;

    /**
     * Name of the user sync process
     * @var string
     */
    protected $_processName;

    /**
     * Constructor.
     * @param User_Sync_Exception $syncExceptionDao
     * @param String $processName the name of the user sync process
     */
    public function __construct (User_Sync_Exception $syncExceptionDao, $processName)
    {
        $this->_syncExceptionDao = $syncExceptionDao;
        $this->_processName = $processName;
    }


    /**
     * Runs the user synchronization processes.
     * @param int $timestamp Unix timestamp of when the process gets kicked off
     * @param Ilios2_Logger $logger
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function run ($timestamp, Ilios2_Logger $logger)
    {
        // run the actual process
        $rhett = $this->_run($timestamp, $logger);
        return $rhett; // return the success/failure indicator

    }

    /**
     * Saves a given user sync exception for a given user to the database.
     * @param int $processId
     * @param int $userId user
     * @param Ilios2_UserSync_Process_UserException $e
     * @see User_Sync_Exception::addException()
     */
    protected function _saveUserSyncException($processId, $userId, Ilios2_UserSync_Process_UserException $e)
    {
        $this->_syncExceptionDao->addException($processId, $this->_processName, $userId, $e->getCode(),
            $e->getMismatchedAttributeName(), $e->getMismatchedAttributeValue());
    }


    /**
     * Removes any recorded user sync exceptions associated with this process.
     */
    protected function _deleteUserSyncExceptionsForProcess ()
    {
        $this->_syncExceptionDao->deleteExceptions($this->_processName);
    }

    /**
     * Implements the actual sync process in sub-classes.
     * @abstract
     * @param int $processId
     * @param Ilios2_Logger $logger
     * @return boolean TRUE on success, FALSE on failure
     */
    abstract protected function _run ($processId, Ilios2_Logger $logger);
}
