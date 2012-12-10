<?php

/**
 * The main purpose if this class is to prevent database deadlock.
 * This is attempted by halting script execution during instantiation of a class object
 * and each call to the object's sleep() function.
 *
 * Use this class after database operations that may take a long time to execute,
 * e.g. after executing or rolling back a complex transaction.
 *
 * @todo add more code docs/clarify purpose of this class
 */
class Ilios_Database_DeadlockSleeper
{
    /**
     * @var int
     * @static
     */
    static protected $_instancesCounter = 1;

    /**
     * @var int
     */
    protected $_instanceNumber;

    /**
     * @var int
     */
    protected $_sleepTime;

    /**
     * @var int
     */
    protected $_retryNumber;

    /**
     * @var string
     */
    protected $_displayString;

    /**
     * Constructor.
     * @param int $currentRetryCount
     * @param string $displayText
     */
    public function __construct ($currentRetryCount, $displayText)
    {
        $this->_instanceNumber = self::$_instancesCounter++;
        $this->_displayString = $displayText;
        $this->_retryNumber = Ilios_Database_Constants::TRANSACTION_RETRY_COUNT - $currentRetryCount;

        usleep(mt_rand(20, 973) * 1000); // stop script execution for a random amount of time

        // calculate a random "sleep time", based on given variables
        $this->_sleepTime = $this->_retryNumber * 10000 + mt_rand(1, 673) * 1091 + $this->_instanceNumber * 10000;
    }

    /**
     * Halts script execution for a pre-calculated amount of time.
     */
    public function sleep ()
    {
        // log this
        $msg = "DEADLOCK CONTROLLER LEVEL (sleeping for {$this->_sleepTime} usecs) :"
               . " {$this->_displayString} rtc: {$this->_retryNumber} instance: "
               . $this->_instanceNumber;
        log_message('info', $msg);
        // halt script execution
        usleep($this->_sleepTime);
    }
}
